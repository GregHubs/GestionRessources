<?php

namespace BirdOffice\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    /**
     * @Route("/associate/list", name="super_admin_index")
     */
    public function superAdminIndexAction(Request $request)
    {
        $em = $this->getDoctrine();

        $managers = $em->getRepository('UserBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
            'managers' => $managers
            )
        );
    }


    /**
     * @Route("/dashboard", name="admin_index")
     */
    public function adminIndexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('UserBundle:Default:index2.html.twig');

    }

    /**
     * @Route("/partner/detail/{id}", name="partner_detail")
     */
    public function PartnerDetailAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('id');
        $user = $em->getRepository('UserBundle:User')->find($userId);
        return $this->render('UserBundle:Default:collaborateur.html.twig', array('user'=>$user));
    }

    /**
     * @Route("/emailing/recap", name="emailing_recap")
     */
    public function emailingRecapAction(Request $request)
    {
        return $this->render('UserBundle:Default:emailing-recap.html.twig');
    }


}