<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
       /*  if ($this->getUser()->getRoles()) {
            return $this->redirectToRoute('target_path');
        } */

        /* // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]); */


    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [

            'error' => $error,
            'last_username' => $lastUsername,
            'page_title' => 'Log in!',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('homepage'),
            'username_label' => 'Email',
            'username_parameter' => 'email',
            'password_label' => 'Password',
            'password_parameter' => 'password',
            'sign_in_label' => 'Log in',
        ]);

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
