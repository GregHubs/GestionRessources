<?php

namespace BirdOffice\UserBundle\Controller;

use BirdOffice\UserBundle\Entity\Day;
use BirdOffice\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AjaxController extends Controller
{
    /**
     * Liste les utilisateurs pour l'acceuil SUPER_ADMIN
     * @return Response
     *
     */
    public function ListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Get current month (return 01, 02, ..)
       // $currentMonth = date_format(new \DateTime, "m");

        $template = array();
        // On vérifie qu'elle est de type POST
        if ('POST' == $request->getMethod()) {
            $managerId = $request->get('manager');

            $manager = $em->getRepository('UserBundle:User')->find($managerId);

            if (!empty($manager))
                $users = $em->getRepository('UserBundle:User')->findBy(array('manager' => $manager));
            else
                $users = $em->getRepository('UserBundle:User')->findBy(array('enabled' => true));

            $template = $this->renderView('UserBundle:Default:list.html.twig', array('users' => $users));
        }
        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Affiche la modale pour l'ajout d'un collaborateur depuis l'acceuil SUPER_ADMIN
     * @return Response
     *
     */
    public function ShowAddModalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('user');

        if($userId){
            $user = $em->getRepository('UserBundle:User')->find($userId);
            $formFactory = $this->get('fos_user.profile.form.factory');

            $form = $formFactory->createForm();
            $form->setData($user);

            $modalTitle = 'Modification fiche collaborateur';
            $modalBody = $this->renderView('FOSUserBundle:Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));

        } else {
            $modalTitle = 'Ajout collaborateur';

            $formFactory = $this->container->get('fos_user.registration.form.factory');

            $form = $formFactory->createForm();

            $modalBody = $this->render('FOSUserBundle:Registration:register.html.twig', array(
                    'form' => $form->createView())
            )->getContent();
        }
        $return = json_encode(array(
            'responseCode'  => 200,
            'notification'  => 'success',
            'modalTitle'    => $modalTitle,
            'modalBody'     => $modalBody,
            'userId'        => $userId
            )
        );
        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Ajout d'un nouveau partenaire
     * @param Request $request
     * @return Response
     *
     */
    public function AddPartnerAjaxAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();

        if (!$user instanceof User){
            throw new \Exception;
        }

        $user->setEnabled(true);
        $user->setEmail($request->get('email'));
        $user->setCivility($request->get('civility'));
        $user->setLastName($request->get('lastname'));
        $user->setFirstName($request->get('firstname'));
        $user->setUsername($request->get('firstname'));
        $user->setPlainPassword($request->get('plainPassword'));
        $user->setManager($request->get('manager'));

        //Set the admin role
        $user->addRole("ROLE_ADMIN");

        $userManager->updateUser($user);

        $message = 'Utilisateur ajouté avec succès';

        $return = json_encode(array(
            'responseCode' => 200,
            'notification' => 'success',
            'message' => $message)
        );

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }


    /**
     * Suppression partenaire : enabled => 0 en base
     * @param Request $request
     * @return Response
     *
     */
    public function DeletePartnerAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user');

        $user = $em->getRepository('UserBundle:User')->find($userId);
        if (!$user instanceof User){
            throw new \Exception;
        }
        $user->setEnabled(false);

        $em->persist($user);

        $em->flush();

        $users = $em->getRepository('UserBundle:User')->findBy(array('enabled' => true));

        $message = 'Utilisateur supprimé avec succès';

        $htmlContent = $this->render('UserBundle:Default:list.html.twig', array('users' => $users))->getContent();

        $return = json_encode(array(
            'responseCode' => 200,
            'message' => $message,
            'notification' => 'success',
            'htmlContent' => $htmlContent)
        );

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Edition d'un partenaire, depuis acceuil SUPER_ADMIN
     * @param Request $request
     * @return Response
     *
     */
    public function EditPartnerAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');

        $em = $this->getDoctrine();

        $user = $em->getRepository('UserBundle:User')->find($request->get('userId'));

        $user->setEmail($request->get('email'));
        $user->setCivility($request->get('civility'));
        $user->setLastName($request->get('lastname'));
        $user->setFirstName($request->get('firstname'));
        $user->setUsername($request->get('firstname'));
        $user->setPlainPassword($request->get('plainPassword'));
        $user->setManager($request->get('manager'));

        $userManager->updateUser($user);

        $message = 'Utilisateur modifié avec succès';

        $return = json_encode(array(
            'responseCode' => 200,
            'notification' => 'success',
            'message' => $message)
        );

        return new Response($return, 200, array('Content-Type' => 'application/json'));

    }

    /**
     * Affiche la liste des demandes de jours pour un utilisateur donné
     * @param Request $request
     * @return Response
     *
     */
    public function AjaxMonthCallAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('userId');

        $user = $em->getRepository('UserBundle:User')->find($userId);

        $template = array();

        // check if POST
        if ('POST' == $request->getMethod()) {

            $monthId = $request->get('month');

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

    /**
     * Modale affichant les détails d'une demande de jour
     *
     * @param Request $request
     * @return Response
     */
    public function ModalDetailContentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId = $request->get('dayId');

        $day = $em->getRepository('UserBundle:Day')->find($dayId);

        $modalTitle = 'Détails de la demande';

        $modalBody = $this->renderView('UserBundle:Presence:detailJour.html.twig', array(
            'day' => $day
        ));

        $return = json_encode(array(
            'responseCode' => 200,
            'notification' => 'success',
            'modalTitle' => $modalTitle,
            'modalBody' => $modalBody)
        );

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Validation d'un jour via checkbox
     *
     * @param Request $request
     * @return Response
     */
    public function ValidationAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId = $request->get('dayId');
        $status = $request->get('status');

        $day = $em->getRepository('UserBundle:Day')->find($dayId);

        if (!$day instanceof Day){
            throw new \Exception;
        }

        $day->setIsValidated($status);
        $day->setValidationDate(new \DateTime('now'));

        $em->persist($day);
        $em->flush();

        $mailer = $this->get('bird_office.mailer');
        $mailer->sendAcceptationMail($day->getUser(), $day);

        $return = json_encode(array(
                'responseCode' => 200,
                'notification' => 'success',
                'message' => 'Mail envoyé !'
        ));

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Affiche la modale pour l'ajout d'un collaborateur depuis l'acceuil SUPER_ADMIN
     * @return Response
     *
     */
    public function ShowEditDayAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId  = $request->get('dayId');
        $userId = $request->get('userId');

        $user   = $em->getRepository('UserBundle:User')->find($userId);

        $managers = $em->getRepository('UserBundle:User')->getRole('ROLE_SUPER_ADMIN');

        if($dayId) {
            $day = $em->getRepository('UserBundle:Day')->find($dayId);
        }else{
            $day = new Day();
        }

        $form = $this->createFormBuilder($day)
            ->add('absenceType', EntityType::class, array(
                'class'         => 'UserBundle:AbsenceType',
                'choice_label'  => 'name',
                'required'      => false,

            ))
            ->add('presenceType', EntityType::class, array(
                'class'         => 'UserBundle:PresenceType',
                'choice_label'  => 'name',
                'required'      => false,

            ))
            ->add('startDate',DateType::class, array(
                'input'     => 'datetime',
                'widget'    => 'choice',
                'label'     => 'Date de début'
            ))

            ->add('endDate', DateType::class, array(
                'input'     => 'datetime',
                'widget'    => 'choice',
                'label'     => 'Date de fin',
                'required'  => false,
            ))
            ->add('description')
            ->add('hours', ChoiceType::class, array(
                'choices'  => array('', '1', '2', '3', '4', '5', '6', '7', '8'
                )))
            ->getForm();

            if ('POST' == $request->getMethod()) {

                $form->handleRequest($request);

                if ($form->isValid()) {
                    $mailer = $this->get('bird_office.mailer');

                    $startDate = $request->get('startDate');
                    $endDate = $request->get('endDate');
                    $hours = $request->get('hours');
                    $absenceType = $request->get('absenceType');
                    $presenceType = $request->get('presenceType');
                    $description = $request->get('description');

                    $absence = $em->getRepository('UserBundle:AbsenceType')->find($absenceType);
                    $presence = $em->getRepository('UserBundle:PresenceType')->find($presenceType);

                    $day->setUser($user);
                    $day->setStartDate(new \DateTime($startDate));
                    $day->setEndDate(new \DateTime($endDate));
                    $day->setHours($hours);
                    $day->setAbsenceType($absence);
                    $day->setPresenceType($presence);
                    $day->setDescription($description);
                    $day->setAskingDate(new \DateTime('now'));

                    if(!$user instanceof User){
                        throw new \Exception;
                    }

                    # Si l'utilisateur faisant la demande est un admin, celle-ci est validée directement
                    if(in_array($managers, $user)){
                        $day->setIsValidated(2);
                        $mailer->sendAcceptationMail($user, $day);
                    }else{
                        $day->setIsValidated(0);
                        // Envoi de mail pour la demande d'absence
                        $manager = $em->getRepository('UserBundle:User')->find($user->getManager());
                        $admin = $em->getRepository('UserBundle:User')->find($manager);
                        $mailer->sendDemandToSuperAdmin($user, $admin, $day);
                    }
                    $em->persist($day);
                    $em->flush();



                }
            }

        if($request->get('dayId')) {
            $modalTitle = 'Modification jour';
            $message = 'Jour modifié avec succès';

        }else{
            $modalTitle = 'Demande de jour';
            $message = 'Jour ajouté avec succès';
        }

        $modalBody = $this->renderView('UserBundle:Day:edit.html.twig', array(
            'form' => $form->createView(),
            'day' => $day
        ));

        $return = json_encode(array(
            'responseCode' => 200,
            'notification' => 'success',
            'message' => $message,
            'modalTitle' => $modalTitle,
            'modalBody' => $modalBody
        ));

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Modification d'une demande de jour(s)
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function EditDayAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        if($request->get('dayId')){
            $day = $em->getRepository('UserBundle:Day')->find($request->get('dayId'));
        }else{
            $day = new Day();
        }

        $absenceType    = $em->getRepository('UserBundle:AbsenceType')->find($request->get('absenceType'));
        $presenceType   = $em->getRepository('UserBundle:PresenceType')->find($request->get('presenceType'));

        $userId = $request->get('userId');
        $user   = $em->getRepository('UserBundle:User')->find($userId);

        $googleServiceManager = $this->get('google_service_manager');
        $response = $googleServiceManager->connect($user);
        dump($response);

        $day->setUser($user);
        $day->setAbsenceType($absenceType);
        $day->setPresenceType($presenceType);
        $day->setStartDate(new \DateTime($request->get('startDate')));
        $day->setEndDate(new \DateTime($request->get('endDate')));
        $day->setHours($request->get('hours'));
        $day->setDescription($request->get('description'));
        $day->setIsValidated(0);

        $em->persist($day);
        $em->flush();

        $message = 'Jour modifié avec succès';

        $return = json_encode(array(
            'responseCode' => 200,
            'notification' => 'success',
            'message' => $message
        ));

        return new Response($return, 200, array('Content-Type' => 'application/json'));

    }

}
