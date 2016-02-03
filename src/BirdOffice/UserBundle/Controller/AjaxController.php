<?php

namespace BirdOffice\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;




class AjaxController extends Controller
{
    /**
     * @Route("/ajaxCall", name="addPartnerAjax", options={"expose"=true} )
     *
     */
    public function AddPartnerAjaxAction(Request $request)
    {
        $managerId = $request->get('managedBy');

        $em = $this->getDoctrine();
        // $manager = $em->getRepository('UserBundle:User')->find($managerId);


        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $user->setEmail($request->get('email'));
        $user->setCivility($request->get('civility'));
        $user->setName($request->get('name'));
        $user->setUsername($request->get('username'));
        $user->setPlainPassword($request->get('plainPassword'));
       // $user->setManagedBy();

        $userManager->updateUser($user);
    }

    /**
     * @Route("/deleteUser/{id}", name="delete_user", options={"expose"=true} )
     *
     */
    public function DeleteUserAjaxAction(Request $request)
    {
        $em = $this->getDoctrine();

        $userId = $request->get('id');

        $user = $em->getRepository('UserBundle:User')->find($userId);

        $userManager = $this->get('fos_user.user_manager');

        $userManager->deleteUser($user);

        $managers = $em->getRepository('UserBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
                'managers' => $managers
            )
        );
    }


}
