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

// =========================================================  //
//  Contrôleur du back-office pour la gestion des catégories: //
// =========================================================  //

#[Route('/admin/categorie')]
final class AdminCategorieController extends AbstractController
{
    //Affiche la liste des catégories.

    #[Route(name: 'app_admin_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    // Permet à l'administrateur de créer une nouvelle catégorie.

    #[Route('/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Création d'une nouvelle Categorie.
        $categorie = new Categorie();

        // Création du formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // Si le formulaire a été envoyé et qu'il est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistrement de la nouvelle catégorie.
            $entityManager->persist($categorie);
            $entityManager->flush();

            // Message affiché
            $this->addFlash(
                'success',
                'La catégorie a été ajoutée avec succès.'
            );

            // Retour vers la liste des catégories.
            return $this->redirectToRoute(
                'app_admin_categorie_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affichage du formulaire
        return $this->render('admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Affiche le détail

    #[Route('/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    // Permet à l'administrateur de modifier une catégorie existante.

    #[Route('/{id}/edit', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Categorie $categorie,
        EntityManagerInterface $entityManager
    ): Response {

        // Création du formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // Si le formulaire est envoyé et valide.
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Message de confirmation.
            $this->addFlash(
                'success',
                'La catégorie a été modifiée avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_categorie_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Formulaire de modification.
        return $this->render('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Categorie $categorie,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification du jeton CSRF avant toute suppression.
        if (
            $this->isCsrfTokenValid(
                'delete' . $categorie->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {

            // On refuse donc la suppression.
            if (!$categorie->getProduits()->isEmpty()) {
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer cette catégorie : elle contient encore des produits.'
                );
                return $this->redirectToRoute(
                    'app_admin_categorie_index',
                    [],
                    Response::HTTP_SEE_OTHER
                );
            }

            // la suppression peut être effectuée
            $entityManager->remove($categorie);
            $entityManager->flush();

            // Message de confirmation.
            $this->addFlash(
                'success',
                'La catégorie a été supprimée avec succès.'
            );
        } else {

            // Si ce n'est pas valide.
            $this->addFlash(
                'danger',
                'La suppression de la catégorie a échoué.'
            );
        }

        return $this->redirectToRoute(
            'app_admin_categorie_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
