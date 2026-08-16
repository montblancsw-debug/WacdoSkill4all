<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    // Permet à un client de créer son compte.
    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Un utilisateur connecté n'a pas besoin de s'inscrire.
        if ($this->getUser()) {
            return $this->redirectToRoute(
                'app_front_home'
            );
        }

        $utilisateur = new Utilisateur();

        // Un compte créé depuis le site est toujours un client.
        $utilisateur->setUtilisateurRole(
            'ROLE_USER'
        );

        $form = $this->createForm(
            RegistrationFormType::class,
            $utilisateur
        );

        $form->handleRequest(
            $request
        );

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form
                ->get('plain_password')
                ->getData();

            // Hash le mot de passe avec le système Symfony.
            $utilisateur->setUtilisateurPassword(
                $passwordHasher->hashPassword(
                    $utilisateur,
                    $plainPassword
                )
            );

            $entityManager->persist(
                $utilisateur
            );

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.'
            );

            return $this->redirectToRoute(
                'app_login',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form,
            ]
        );
    }
}