<?php

namespace BirdOffice\UserBundle\Controller;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class RegistrationController extends BaseController
{

    public function RegisterAction(Request $request)
    {
        $formFactory = $this->container->get('fos_user.registration.form.factory');

        $form = $formFactory->createForm();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView()
        ));
    }



}
