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
        // Redirige un utilisateur déjà connecté.
        if ($this->getUser()) {

            if (
                $this->isGranted('ROLE_ADMIN')
                || $this->isGranted('ROLE_PREPARATEUR')
                || $this->isGranted('ROLE_ACCUEIL')
            ) {
                return $this->redirectToRoute(
                    'app_admin_dashboard'
                );
            }

            return $this->redirectToRoute(
                'app_front_home'
            );
        }

        $error = $authenticationUtils
            ->getLastAuthenticationError();

        $lastUsername = $authenticationUtils
            ->getLastUsername();

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