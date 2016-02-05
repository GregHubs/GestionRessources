<?php

namespace BirdOffice\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\MonologBundle\SwiftMailer;


class MailController extends Controller
{
    /**
     * @Route("/sendmail", name="send_mail")
     */
    public function superAdminIndexAction(Request $request)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('ponsgregory@gmail.com')
            ->setTo('ponsgregory@gmail.com')
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'UserBundle:Mail:test.html.twig'),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;
        $this->get('mailer')->send($message);

        $managers = $this->getDoctrine()->getRepository('UserBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        return $this->render('UserBundle:Default:index.html.twig',
            array(
                'managers' => $managers
            )
        );    }


}
