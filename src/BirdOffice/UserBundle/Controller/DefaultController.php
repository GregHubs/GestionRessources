<?php

namespace BirdOffice\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    public function SuperAdminIndexAction()
    {
        $em = $this->getDoctrine();

        $managers = $em->getRepository('UserBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
            'managers' => $managers
            )
        );
    }


    public function AdminIndexAction()
    {
        // replace this example code with whatever you need
        return $this->render('UserBundle:Default:index2.html.twig');

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

    public function testMailAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user   = $em->getRepository('UserBundle:User')->find(17);
        $admin  = $em->getRepository('UserBundle:User')->find(15);
        $day    = $em->getRepository('UserBundle:Day')->find(1);

        return $this->render('UserBundle:Mail:emailing-reponse.html.twig',
            array(
                'user'=>$user,
                'admin'=>$admin,
                'day'=>$day
            )
        );
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
        $validation = $request->get('validation');
        $userId     = $request->get('user');

        $day  = $em->getRepository('UserBundle:Day')->find($dayId);
        $user = $em->getRepository('UserBundle:User')->find($userId);

        $day->setIsValidated($validation);

        $em->persist($day);
        $em->flush();

        if($validation){
            $admin = $em->getRepository('UserBundle:User')->find(15);
            $mailer = $this->get('bird_office.mailer');
            $mailer->sendAcceptationMail($user, $day);
        }

        $managers = $em->getRepository('UserBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
                'managers' => $managers
            )
        );

    }



}
