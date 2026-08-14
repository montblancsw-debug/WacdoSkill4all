<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // Affiche le formulaire de connexion.
    #[Route('/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response {
        // Redirige un utilisateur déjà connecté vers le Dashboard.
        if ($this->getUser()) {
            return $this->redirectToRoute(
                'app_admin_dashboard'
            );
        }

        // Récupère la dernière erreur de connexion.
        $error = $authenticationUtils
            ->getLastAuthenticationError();

        // Récupère le dernier email saisi.
        $lastUsername = $authenticationUtils
            ->getLastUsername();

        // Affiche la page de connexion.
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Symfony intercepte automatiquement cette route.
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'Cette méthode est interceptée automatiquement par Symfony.'
        );
    }
}
