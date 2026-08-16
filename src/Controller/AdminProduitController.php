<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/produit')]
final class AdminProduitController extends AbstractController
{
    // Affiche, recherche et filtre les produits.
    #[Route(
        name: 'app_admin_produit_index',
        methods: ['GET', 'POST']
    )]
    public function index(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        Request $request
    ): Response {
        $search = trim(
            (string) $request
                ->request
                ->get(
                    'search',
                    ''
                )
        );

        $stock = trim(
            (string) $request
                ->request
                ->get(
                    'stock',
                    ''
                )
        );

        $categorieId = trim(
            (string) $request
                ->request
                ->get(
                    'categorie',
                    ''
                )
        );

        if (
            $search !== ''
            || $stock !== ''
            || $categorieId !== ''
        ) {
            $produits = $produitRepository->search(
                $search,
                $stock,
                $categorieId
            );
        } else {
            $produits = $produitRepository->findBy(
                [],
                [
                    'produit_nom' => 'ASC',
                ]
            );
        }

        return $this->render(
            'admin_produit/index.html.twig',
            [
                'produits' => $produits,
                'categories' => $categorieRepository->findBy(
                    [],
                    [
                        'categorie_nom' => 'ASC',
                    ]
                ),
                'search' => $search,
                'stock' => $stock,
                'categorieSelectionnee' => $categorieId,
            ]
        );
    }

    // Crée un produit.
    #[Route(
        '/new',
        name: 'app_admin_produit_new',
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $produit = new Produit();

        $form = $this->createForm(
            ProduitType::class,
            $produit
        );

        $form->handleRequest(
            $request
        );

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->persist(
                $produit
            );

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le produit a été ajouté avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_produit_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'admin_produit/new.html.twig',
            [
                'produit' => $produit,
                'form' => $form,
            ]
        );
    }

    // Affiche un produit.
    #[Route(
        '/{id}',
        name: 'app_admin_produit_show',
        methods: ['GET']
    )]
    public function show(
        Produit $produit
    ): Response {
        return $this->render(
            'admin_produit/show.html.twig',
            [
                'produit' => $produit,
            ]
        );
    }

    // Modifie un produit.
    #[Route(
        '/{id}/edit',
        name: 'app_admin_produit_edit',
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            ProduitType::class,
            $produit
        );

        $form->handleRequest(
            $request
        );

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le produit a été modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_produit_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'admin_produit/edit.html.twig',
            [
                'produit' => $produit,
                'form' => $form,
            ]
        );
    }

    // Supprime un produit.
    #[Route(
        '/{id}',
        name: 'app_admin_produit_delete',
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $produit->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            $entityManager->remove(
                $produit
            );

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le produit a été supprimé avec succès.'
            );
        } else {
            $this->addFlash(
                'danger',
                'La suppression du produit a échoué.'
            );
        }

        return $this->redirectToRoute(
            'app_admin_produit_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}