<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]public function login(AuthenticationUtils $authenticationUtils): Response
    {

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

// Affiche le formulaire de connexion.

if ($this->getUser())
{
    // Redirige l'utilisateur s'il est déjà connecté.
    if ($this->isGranted('ROLE_ADMIN')|| $this->isGranted('ROLE_PREPARATEUR')|| $this->isGranted('ROLE_ACCUEIL'))
    {
        return $this->redirectToRoute('app_admin_dashboard');
    }
        return $this->redirectToRoute('app_front_home');
}
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername,'error' => $error,]);
    }

// Gère la route de déconnexion utilisée par Symfony.

    #[Route('/logout', name: 'app_logout')]

    public function logout(): void
    {
        throw new \LogicException('Cette méthode est interceptée automatiquement par Symfony.');
    }
}