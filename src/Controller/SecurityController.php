<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
  // Route vers la page de connexion utilisateur
  #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
  public function login(AuthenticationUtils $utils): Response
  {
    return $this->render(
      'login.html.twig',
      [
        // Permet de générer une erreur si les informations de connexion sont incorrectes
        'error' => $utils->getLastAuthenticationError(),
        // Garde en mémoire le nom d'utilisateur afin d'éviter à l'utilisateur de re-écrire son nom si une erreur survient
        'last_username' => $utils->getLastUsername()
      ]
    );
  }


  
}
