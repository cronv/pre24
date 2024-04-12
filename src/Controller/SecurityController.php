<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\Constants\RouterConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller security login, logout
 */
class SecurityController extends AbstractController
{
    /**
     * Action login (authenticate)
     *
     * @param AuthenticationUtils $authenticationUtils Extracts Security Errors from Request
     * @return Response
     */
    #[Route(path: '/login', name: RouterConstants::LOGIN_ROUTE)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if user auth do redirect
        if ($this->getUser()) {
            return $this->redirectToRoute('cronv-tm-bundle');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@cronvTaskManagement/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Action logout
     *
     * @return void
     */
    #[Route(path: '/logout', name: RouterConstants::LOGOUT_ROUTE)]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
