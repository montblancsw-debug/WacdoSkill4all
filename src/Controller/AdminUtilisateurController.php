<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/utilisateur')]
final class AdminUtilisateurController extends AbstractController
{
    // Affiche la liste des utilisateurs.
    #[Route(name: 'app_admin_utilisateur_index', methods: ['GET'])]
    public function index(
        UtilisateurRepository $utilisateurRepository
    ): Response {
        return $this->render('admin_utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findBy([], [
                'id' => 'ASC',
            ]),
        ]);
    }

    // Permet de créer un nouvel utilisateur.
    #[Route('/new', name: 'app_admin_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée une nouvelle instance d'Utilisateur.
        $utilisateur = new Utilisateur();

        // Crée le formulaire de l'utilisateur.
        $form = $this->createForm(
            UtilisateurType::class,
            $utilisateur,
            [
                'is_edit' => false,
            ]
        );

        // Récupère les données envoyées.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère le mot de passe saisi.
            $plainPassword = $form
                ->get('plain_password')
                ->getData();

            // Hash le mot de passe avant son enregistrement.
            $hashedPassword = password_hash(
                $plainPassword,
                PASSWORD_BCRYPT
            );

            // Enregistre uniquement le mot de passe haché.
            $utilisateur->setUtilisateurPassword(
                $hashedPassword
            );

            // Prépare l'enregistrement de l'utilisateur.
            $entityManager->persist($utilisateur);

            // Enregistre l'utilisateur en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'L\'utilisateur a été ajouté avec succès.'
            );

            // Retourne vers la liste des utilisateurs.
            return $this->redirectToRoute(
                'app_admin_utilisateur_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de création.
        return $this->render('admin_utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    // Affiche les informations d'un utilisateur.
    #[Route('/{id}', name: 'app_admin_utilisateur_show', methods: ['GET'])]
    public function show(
        Utilisateur $utilisateur
    ): Response {
        return $this->render('admin_utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // Permet de modifier un utilisateur.
    #[Route('/{id}/edit', name: 'app_admin_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Utilisateur $utilisateur,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée le formulaire avec l'utilisateur existant.
        $form = $this->createForm(
            UtilisateurType::class,
            $utilisateur,
            [
                'is_edit' => true,
            ]
        );

        // Récupère les données envoyées.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère éventuellement le nouveau mot de passe.
            $plainPassword = $form
                ->get('plain_password')
                ->getData();

            // Modifie le mot de passe uniquement s'il est renseigné.
            if (!empty($plainPassword)) {

                // Hash le nouveau mot de passe.
                $hashedPassword = password_hash(
                    $plainPassword,
                    PASSWORD_BCRYPT
                );

                // Enregistre le nouveau mot de passe haché.
                $utilisateur->setUtilisateurPassword(
                    $hashedPassword
                );
            }

            // Enregistre les modifications.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'L\'utilisateur a été modifié avec succès.'
            );

            // Retourne vers la liste des utilisateurs.
            return $this->redirectToRoute(
                'app_admin_utilisateur_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de modification.
        return $this->render('admin_utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    // Supprime uniquement l'utilisateur sélectionné.
    #[Route('/{id}', name: 'app_admin_utilisateur_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Utilisateur $utilisateur,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifie le jeton CSRF.
        if (
            $this->isCsrfTokenValid(
                'delete' . $utilisateur->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            // Supprime uniquement l'utilisateur sélectionné.
            $entityManager->remove($utilisateur);

            // Exécute la suppression en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'L\'utilisateur a été supprimé avec succès.'
            );
        } else {
            // Affiche un message si le jeton est invalide.
            $this->addFlash(
                'danger',
                'La suppression de l\'utilisateur a échoué.'
            );
        }

        // Retourne vers la liste des utilisateurs.
        return $this->redirectToRoute(
            'app_admin_utilisateur_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
