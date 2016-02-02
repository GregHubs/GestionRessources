<?php

namespace BirdOffice\UserBundle\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;


class RegistrationController extends BaseController
{

    public function registerAction(Request $request)
    {



        return $this->render('FOSUserBundle:Registration:register.html.twig'
        );
    }
}
