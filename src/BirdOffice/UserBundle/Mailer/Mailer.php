<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BirdOffice\UserBundle\Mailer;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer
{
    private $container;
    private $templating;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->templating = $container->get('templating');
    }

    public function sendMail($subject, $from, $to, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
            ->setContentType('text/html')
        ;

        $this->container->get('mailer')->send($message);
    }

    public function sendDemandToSuperAdmin(\BirdOffice\UserBundle\Entity\User $user, \BirdOffice\UserBundle\Entity\User $admin, $day)
    {
        $subject    = "Nouvelle demande de jour(s)";
        $template   = "UserBundle:Mail:emailing-recap.html.twig";
        $from       = 'gestion-personnel@bird-office.com';
        $to         = $admin->getEmail();
        $body       = $this->templating->render($template, array('admin' => $admin, 'user' => $user, 'day' => $day));

        $this->sendMail($subject, $from, $to, $body);
    }


    public function sendAcceptationMail(\BirdOffice\UserBundle\Entity\User $user, $day)
    {
        $subject = "Mise à jour de votre demande de jour(s)";
        $template = "UserBundle:Mail:emailing-reponse.html.twig";
        $from = 'gestion-personnel@bird-office.com';
        $to = $user->getEmail();
        $body = $this->templating->render($template, array('user' => $user, 'day' => $day));

        $this->sendMail($subject, $from, $to, $body);
    }

}
