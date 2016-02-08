<?php

namespace BirdOffice\UserBundle\ConnectionHandler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface{

    protected $router;
    private $container;

    public function __construct(Router $router, Container $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('ROLE_SUPER_ADMIN'))
        {
            $response = new RedirectResponse($this->router->generate('superAdminIndex'));
        }
        elseif ($security->isGranted('ROLE_ADMIN'))
        {
            $response = new RedirectResponse($this->router->generate('admin_index'));
        }
        elseif ($security->isGranted('ROLE_USER'))
        {
            $response = new RedirectResponse($this->router->generate('admin_index'));

        }

        return $response;
    }
}