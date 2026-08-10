<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/produit')]
final class AdminProduitController extends AbstractController
{
    // Affiche la liste des produits.
    #[Route(name: 'app_admin_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin_produit/index.html.twig', [
            'produits' => $produitRepository->findBy([], [
                'id' => 'ASC',
            ]),
        ]);
    }

    // Permet de créer un nouveau produit.
    #[Route('/new', name: 'app_admin_produit_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée une nouvelle instance de Produit.
        $produit = new Produit();

        // Crée le formulaire du produit.
        $form = $this->createForm(
            ProduitType::class,
            $produit
        );

        // Récupère les données envoyées.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Prépare l'enregistrement du produit.
            $entityManager->persist($produit);

            // Enregistre le produit en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'Le produit a été ajouté avec succès.'
            );

            // Retourne vers la liste des produits.
            return $this->redirectToRoute(
                'app_admin_produit_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de création.
        return $this->render('admin_produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    // Affiche le détail d'un produit.
    #[Route('/{id}', name: 'app_admin_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('admin_produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    // Permet de modifier un produit.
    #[Route('/{id}/edit', name: 'app_admin_produit_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée le formulaire avec le produit existant.
        $form = $this->createForm(
            ProduitType::class,
            $produit
        );

        // Récupère les nouvelles données.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistre les modifications.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'Le produit a été modifié avec succès.'
            );

            // Retourne vers la liste des produits.
            return $this->redirectToRoute(
                'app_admin_produit_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de modification.
        return $this->render('admin_produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    // Supprime uniquement le produit sélectionné.
    #[Route('/{id}', name: 'app_admin_produit_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifie le jeton CSRF.
        if (
            $this->isCsrfTokenValid(
                'delete' . $produit->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            // Supprime uniquement le produit reçu dans l'URL.
            $entityManager->remove($produit);

            // Exécute la suppression en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'Le produit a été supprimé avec succès.'
            );
        } else {
            // Affiche une erreur si le jeton est invalide.
            $this->addFlash(
                'danger',
                'La suppression du produit a échoué.'
            );
        }

        // Retourne vers la liste des produits.
        return $this->redirectToRoute(
            'app_admin_produit_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}