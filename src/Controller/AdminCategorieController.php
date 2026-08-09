<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur du back-office permettant la gestion des catégories

#[Route('/admin/categorie')]
final class AdminCategorieController extends AbstractController
{
    /*
     * Affiche la liste complète des catégories.
     */
    #[Route(name: 'app_admin_categorie_index', methods: ['GET'])]
    public function index(
        CategorieRepository $categorieRepository
    ): Response {
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Création d'une nouvelle catégorie.
        $categorie = new Categorie();

        // Création du formulaire
        $form = $this->createForm(
            CategorieType::class,
            $categorie
        );

        // Traitement des données
        $form->handleRequest($request);

        // Vérification
        if ($form->isSubmitted() && $form->isValid()) {

            // Ajout de la nouvelle catégorie
            $entityManager->persist($categorie);

            // Enregistrement dans la base de données.
            $entityManager->flush();

            // Message de confirmation pour l'administrateur.
            $this->addFlash(
                'success',
                'La catégorie a été ajoutée avec succès.'
            );

            // Retour à la liste des catégories.
            return $this->redirectToRoute(
                'app_admin_categorie_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affichage de création.
        return $this->render('admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /*
     * Affiche les informations d'une catégorie.
     */
    #[Route('/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]
    public function show(
        Categorie $categorie
    ): Response {
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /*
     * Modification d'une catégorie existante.
     */
    #[Route('/{id}/edit', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Categorie $categorie,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            CategorieType::class,
            $categorie
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Message de confirmation.
            $this->addFlash(
                'success',
                'La catégorie a été modifiée avec succès.'
            );

            // Retour vers la liste.
            return $this->redirectToRoute(
                'app_admin_categorie_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affichage du formulaire de modification.
        return $this->render('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Suppression d'une catégorie.
    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Categorie $categorie,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification du token CSRF.
        if (
            $this->isCsrfTokenValid(
                'delete' . $categorie->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            $entityManager->remove($categorie);
            $entityManager->flush();

            // Message affiché dans le back-office.
            $this->addFlash(
                'success',
                'La catégorie a été supprimée avec succès. Les produits associés ont été conservés.'
            );
        } else {
            // Le token CSRF est incorrect.
            $this->addFlash(
                'danger',
                'Impossible de supprimer la catégorie : le jeton de sécurité est invalide.'
            );
        }

        // Retour à la liste des catégories.
        return $this->redirectToRoute(
            'app_admin_categorie_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
