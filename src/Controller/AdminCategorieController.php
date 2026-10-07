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

#[Route('/admin/categorie')]

final class AdminCategorieController extends AbstractController
{

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Affiche et recherche les catégories.

    #[Route(name: 'app_admin_categorie_index', methods: ['GET', 'POST'])]

    public function index(CategorieRepository $categorieRepository, Request $request): Response
    {
        $search = trim((string) $request->request->get('search', ''));
        if ($search !== '') {
            $categories = $categorieRepository->search($search);
        } else {
            $categories = $categorieRepository->findBy([], [
                'categorie_nom' => 'ASC',
            ]);
        }
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    // Ajoute une nouvelle catégorie.

    #[Route('/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');
            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Affiche le détail d'une catégorie.

    #[Route('/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]

    public function show(Categorie $categorie): Response
    {
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    // Modifie une catégorie existante.

    #[Route('/{id}/edit', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Supprime une catégorie.

    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]

    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été supprimée avec succès. Les produits associés ont été conservés.');
        } else {
            $this->addFlash('danger', 'Impossible de supprimer la catégorie : le jeton de sécurité est invalide.');
        }
        return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}