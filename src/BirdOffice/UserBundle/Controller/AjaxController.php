<?php

namespace BirdOffice\UserBundle\Controller;

use BirdOffice\UserBundle\Entity\Day;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;


class AjaxController extends Controller
{
    public function ListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère la requête
        $request = $this->get('request');
        $template = array();
        // On vérifie qu'elle est de type POST
        if ('POST' == $request->getMethod()) {

            $managerId = $request->get('manager');

            $manager = $em->getRepository('UserBundle:User')->find($managerId);

            if (!empty($manager))
                $users = $em->getRepository('UserBundle:User')->findBy(array('managedBy' => $manager));
            else
                $users = $em->getRepository('UserBundle:User')->findBy(array('enabled' => true));

            $template = $this->renderView('UserBundle:Default:list.html.twig', array('users' => $users));
        }

        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    public function ShowAddModalAction()
    {
        $modalTitle = 'Ajout collaborateur';

        $formFactory = $this->container->get('fos_user.registration.form.factory');

        $form = $formFactory->createForm();

        $modalBody = $this->render('FOSUserBundle:Registration:register.html.twig', array('form' => $form->createView()))->getContent();

        $return = json_encode(array('responseCode' => 200, 'notification' => 'success', 'modalTitle' => $modalTitle, 'modalBody' => $modalBody));

        return new Response($return, 200, array('Content-Type' => 'application/json'));

    }


    public function AddPartnerAjaxAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $user->setEmail($request->get('email'));
        $user->setCivility($request->get('civility'));
        $user->setName($request->get('name'));
        $user->setUsername($request->get('username'));
        $user->setPlainPassword($request->get('plainPassword'));

        $userManager->updateUser($user);

        $modalTitle = 'Ajout collaborateur';
        $modalBody = $this->render('FOSUserBundle:Registration:register.html.twig')->getContent();

        $return = json_encode(array('responseCode' => 200, 'notification' => 'success', 'modalTitle' => $modalTitle, 'modalBody' => $modalBody));

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }


    public function DeletePartnerAjaxAction(Request $request)
    {
        $helper = $this->get('bird_office.helper');

        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user');

        $user = $em->getRepository('UserBundle:User')->find($userId);

        $user->setEnabled(false);

        $em->persist($user);

        $em->flush();

        $users = $em->getRepository('UserBundle:User')->findBy(array('enabled' => true));

        $message = 'Utilisateur supprimé avec succès';

        $htmlContent = $this->render('UserBundle:Default:list.html.twig', array('users' => $users))->getContent();

        $return = json_encode(array('responseCode' => 200, 'message' => $message, 'notification' => 'success', 'htmlContent' => $htmlContent));

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    public function EditPartnerAjaxAction(Request $request)
    {
        $em = $this->getDoctrine();

        $userId = $request->get('user');

        $user = $em->getRepository('UserBundle:User')->find($userId);

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;

        } else {

            $modalTitle = 'Modification fiche collaborateur';
            $modalBody = $this->renderView('FOSUserBundle:Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));

            $return = json_encode(array('responseCode' => 200, 'notification' => 'success', 'modalTitle' => $modalTitle, 'modalBody' => $modalBody));

            return new Response($return, 200, array('Content-Type' => 'application/json'));
        }

    }

    public function AddPresenceAbsenceAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('userId');

        $user = $em->getRepository('UserBundle:User')->find($userId);

        $template = array();

        // check if POST
        if ('POST' == $request->getMethod()) {

            $monthId = $request->get('month');
          //  var_dump($monthId);die;

            if ($monthId == 0) {
                $args = array('user' => $user);
            } else {
                $args = array('user' => $user, 'month' => $monthId);
            }

            $days = $em->getRepository('UserBundle:Day')->getList($args);

            $template = $this->renderView('UserBundle:Presence:presence.html.twig', array('user' => $user, 'days' => $days));


        }

        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function AddNewDayAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userId         = $request->get('userId');
        $startDate      = $request->get('startDate');
        $endDate        = $request->get('endDate');
        $hours          = $request->get('hours');
        $absenceType    = $request->get('absenceType');
        $presenceType   = $request->get('presenceType');
        $description    = $request->get('description');

        $template = array();

        $day = new Day();

        $user = $em->getRepository('UserBundle:User')->find($userId);
        $absence = $em->getRepository('UserBundle:AbsenceType')->find($absenceType);
        $presence = $em->getRepository('UserBundle:PresenceType')->find($presenceType);

        if ('POST' == $request->getMethod()) {

            $day->setUser($user);
            $day->setStartDate(new \DateTime($startDate));
            $day->setEndDate(new \DateTime($endDate));
            $day->setHours($hours);
            $day->setAbsenceType($absence);
            $day->setPresenceType($presence);
            $day->setDescription($description);
            $day->setIsValidated(false);
            $day->setAskingDate(new \DateTime('now'));

            $em->persist($day);
            $em->flush();
        }

        // Envoi de mail pour la demande d'absence
        $admin = $em->getRepository('UserBundle:User')->find(15);
        $mailer = $this->get('bird_office.mailer');
        $mailer->sendDemandToSuperAdmin($user, $admin, $day);

        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function ModalDetailContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId = $request->get('dayId');

        $day = $em->getRepository('UserBundle:Day')->find($dayId);

        $modalTitle = 'Détails de la demande';

        $modalBody = $this->renderView('UserBundle:Presence:detailJour.html.twig', array(
            'day' => $day
        ));

        $return = json_encode(array('responseCode' => 200, 'notification' => 'success', 'modalTitle' => $modalTitle, 'modalBody' => $modalBody));

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    public function ValidationAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId = $request->get('dayId');
        $validation = $request->get('validation');

        $day = $em->getRepository('UserBundle:Day')->find($dayId);

        $day->setIsValidated($validation);
        $day->setValidationDate(new \DateTime('now'));

        $em->persist($day);
        $em->flush();

        $template = array();
        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


}
