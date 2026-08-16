<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontCategorieController extends AbstractController
{
    // Affiche toutes les catégories.
    #[Route('/categorie', name: 'app_front_categorie', methods: ['GET'])]
    public function index(
        CategorieRepository $categorieRepository
    ): Response {
        return $this->render('front_categorie/index.html.twig', [
            'categories' => $categorieRepository->findBy(
                [],
                [
                    'categorie_nom' => 'ASC',
                ]
            ),
        ]);
    }

    // Affiche une catégorie et ses produits.
    #[Route(
        '/categorie/{id}',
        name: 'app_front_categorie_detail',
        methods: ['GET']
    )]
    public function detail(
        Categorie $categorie,
        ProduitRepository $produitRepository
    ): Response {
        return $this->render(
            'front_categorie/detail.html.twig',
            [
                'categorie' => $categorie,
                'produits' => $produitRepository->findBy(
                    [
                        'categorie' => $categorie,
                    ],
                    [
                        'produit_nom' => 'ASC',
                    ]
                ),
            ]
        );
    }
}