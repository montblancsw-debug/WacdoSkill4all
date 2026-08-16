<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontCategorieController extends AbstractController
{
    // Affiche et recherche les catégories.
    #[Route(
        '/categorie',
        name: 'app_front_categorie',
        methods: ['GET', 'POST']
    )]
    public function index(
        CategorieRepository $categorieRepository,
        Request $request
    ): Response {
        $search = trim(
            (string) $request
                ->request
                ->get('search', '')
        );

        if ($search !== '') {
            $categories = $categorieRepository->search(
                $search
            );
        } else {
            $categories = $categorieRepository->findBy(
                [],
                [
                    'categorie_nom' => 'ASC',
                ]
            );
        }

        return $this->render(
            'front_categorie/index.html.twig',
            [
                'categories' => $categories,
                'search' => $search,
            ]
        );
    }

    // Affiche une catégorie et recherche ses produits.
    #[Route(
        '/categorie/{id}',
        name: 'app_front_categorie_detail',
        methods: ['GET', 'POST']
    )]
    public function detail(
        Categorie $categorie,
        ProduitRepository $produitRepository,
        Request $request
    ): Response {
        $search = trim(
            (string) $request
                ->request
                ->get('search', '')
        );

        if ($search !== '') {
            $produits = $produitRepository->searchByCategorie(
                $categorie,
                $search
            );
        } else {
            $produits = $produitRepository->findBy(
                [
                    'categorie' => $categorie,
                ],
                [
                    'produit_nom' => 'ASC',
                ]
            );
        }

        return $this->render(
            'front_categorie/detail.html.twig',
            [
                'categorie' => $categorie,
                'produits' => $produits,
                'search' => $search,
            ]
        );
    }
}