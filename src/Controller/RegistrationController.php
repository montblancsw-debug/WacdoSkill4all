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

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Permet à un client de créer un nouveau compte.

    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Empêche un utilisateur connecté de créer un nouveau compte.

        if ($this->getUser()) {
            return $this->redirectToRoute('app_front_home');
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setUtilisateurRole('ROLE_USER');

        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        // Enregistre le nouveau compte si le formulaire est valide.

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plain_password')->getData();

            // Chiffre le mot de passe avant son enregistrement.

            $utilisateur->setUtilisateurPassword(
                $passwordHasher->hashPassword($utilisateur, $plainPassword)
            );

            $entityManager->persist($utilisateur);
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

        // Affiche un message lorsque les informations saisies sont invalides.

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                'danger',
                'Inscription impossible. Vérifiez que le mot de passe contient au moins 8 caractères et que l\'adresse email est valide et n\'est pas déjà utilisée.'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}