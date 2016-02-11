<?php

namespace BirdOffice\UserBundle\Controller;

use BirdOffice\UserBundle\Entity\Day;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    public function SuperAdminIndexAction()
    {
        $em = $this->getDoctrine();

        $managers = $em->getRepository('UserBundle:User')->getRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
            'managers' => $managers
            )
        );
    }

    public function PartnerIndexAction(Request $request)
    {
        $user = $request->getUser();
        $em = $this->getDoctrine()->getManager();

        $absences = $em->getRepository('UserBundle:AbsenceType')->findAll();
        $presences = $em->getRepository('UserBundle:PresenceType')->findAll();

        return $this->render('UserBundle:Default:collaborateur.html.twig',
            array(
                'user'=>$user,
                'absences'=>$absences,
                'presences'=>$presences
            )
        );

    }


    public function AdminIndexAction()
    {
        // replace this example code with whatever you need
    }

    public function PartnerDetailAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('id');
        $user = $em->getRepository('UserBundle:User')->find($userId);
        $absences = $em->getRepository('UserBundle:AbsenceType')->findAll();
        $presences = $em->getRepository('UserBundle:PresenceType')->findAll();

        return $this->render('UserBundle:Default:collaborateur.html.twig',
            array(
            'user'=>$user,
            'absences'=>$absences,
            'presences'=>$presences
            )
        );
    }

    public function CallbackAction()
    {

        return $this->render('UserBundle:Mail:emailing-callback.html.twig');
    }

    /**
     * @param Request $request
     *
     * Gestion de la validation/refus des jours depuis email de notification
     */
    public function validateDayAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dayId      = $request->get('dayId');
        $status     = $request->get('status');
        $userId     = $request->get('userId');

        $day  = $em->getRepository('UserBundle:Day')->find($dayId);
        $user = $em->getRepository('UserBundle:User')->find($userId);

        if (!$day instanceof Day){
            throw new \Exception;
        }
        $day->setIsValidated($status);

        $em->persist($day);
        $em->flush();

        $mailer = $this->get('bird_office.mailer');
        $mailer->sendAcceptationMail($user, $day);

        $managers = $em->getRepository('UserBundle:User')->getRole('ROLE_SUPER_ADMIN');

        return $this->redirect( $this->generateUrl('Callback' ));


    }



}
