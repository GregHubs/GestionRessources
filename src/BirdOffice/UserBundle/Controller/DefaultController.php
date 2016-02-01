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
     * @Route("/prout", name="list")
     */
    public function listAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');

        // On récupère la requête
        $request = $this->get('request');
        $template = array();
        // On vérifie qu'elle est de type POST
        if ('POST' == $request->getMethod()){

            $manager = $request->get('manager');

            if(!empty($manager))
              //  $users  = $em->getRepository('UserBundle:User')->findByManager($manager);
                $users = array();
            else
                $users  = $em->getRepository('UserBundle:User')->findAll();

                $template = $this->renderView('UserBundle:Default:list.html.twig', array('users' => $users));
        }

        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }


    /**
     * @Route("/dashboard", name="admin_index")
     */
    public function adminIndexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('UserBundle:Default:index2.html.twig');
    }
}
