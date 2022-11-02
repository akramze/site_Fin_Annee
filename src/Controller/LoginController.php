<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


  class LoginController extends AbstractController
  {
      #[Route('/connect', name: 'app_login')]
      public function index(AuthenticationUtils $authenticationUtils): Response
      {
         // afficher l'erreur de connexion s'il y en a 
         $error = $authenticationUtils->getLastAuthenticationError();

         // dernier nom d'utilisateur entré par l'utilisateur
         $lastUsername = $authenticationUtils->getLastUsername();


          return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
          ]);
        }
    }