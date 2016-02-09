<?php

namespace BirdOffice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\MonologBundle\SwiftMailer;
use BirdOffice\UserBundle\Mailer;


class MailController extends Controller
{

    public function sendMailAction($subject, $fromEmail, $toEmail, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody(
                $this->renderView(
                    'UserBundle:Mail:emailing-recap.html.twig'),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }

    public function sendDemandAction($userId, $superAdminId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($userId);
        $admin = $em->getRepository('UserBundle:User')->find($superAdminId);
        $mailer = $this->get('bird_office.mailer');
        $mailer->sendDemandToSuperAdmin($user, $admin);
    }




}
