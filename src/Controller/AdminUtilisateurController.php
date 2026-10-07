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

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Affiche, recherche, filtre et trie les utilisateurs.

    #[Route(name: 'app_admin_utilisateur_index', methods: ['GET', 'POST'])]

    public function index(UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        $search = trim((string) $request->request->get('search', ''));
        $role = trim((string) $request->request->get('role', ''));
        $triRole = trim((string) $request->request->get('tri_role', ''));

        if ($search !== '' || $role !== '' || $triRole !== '') {
            $utilisateurs = $utilisateurRepository->search($search, $role, $triRole);
        } else {
            $utilisateurs = $utilisateurRepository->findBy([], ['utilisateur_nom' => 'ASC',]);
        }
        return $this->render('admin_utilisateur/index.html.twig',
        [
            'utilisateurs' => $utilisateurs,
            'search' => $search,
            'role' => $role,
            'triRole' => $triRole,
        ]);
    }

    // Ajoute un nouvel utilisateur.

    #[Route('/new', name: 'app_admin_utilisateur_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $plainPassword = $form->get('plain_password')->getData();

            // Chiffre le mot de passe avant de l'enregistrer.
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            $utilisateur->setUtilisateurPassword($hashedPassword);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été ajouté avec succès.');
            return $this->redirectToRoute('app_admin_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin_utilisateur/new.html.twig', ['utilisateur' => $utilisateur,'form' => $form,]);
    }

    // Affiche le détail d'un utilisateur.

    #[Route('/{id}', name: 'app_admin_utilisateur_show', methods: ['GET'])]

    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin_utilisateur/show.html.twig', ['utilisateur' => $utilisateur,]);
    }

    // Modifie un utilisateur existant.

    #[Route('/{id}/edit', name: 'app_admin_utilisateur_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $plainPassword = $form->get('plain_password')->getData();

            // Modifie le mot de passe uniquement si un nouveau mot de passe est saisi.
            if (!empty($plainPassword))
            {
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
                $utilisateur->setUtilisateurPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès.');
            return $this->redirectToRoute('app_admin_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin_utilisateur/edit.html.twig', ['utilisateur' => $utilisateur,'form' => $form,]);
    }

    // Supprime un utilisateur.
    #[Route('/{id}', name: 'app_admin_utilisateur_delete', methods: ['POST'])]

    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->getPayload()->getString('_token')))
        {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'La suppression de l\'utilisateur a échoué.');
        }
        return $this->redirectToRoute('app_admin_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}