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



}
