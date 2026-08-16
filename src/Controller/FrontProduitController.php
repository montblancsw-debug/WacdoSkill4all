<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontProduitController extends AbstractController
{
    // Affiche le catalogue public.
    #[Route('/catalogue', name: 'app_front_produit', methods: ['GET'])]
    public function index(
        ProduitRepository $produitRepository
    ): Response {
        return $this->render('front_produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    // Affiche le détail d'un produit.
    #[Route(
        '/produit/{id}',
        name: 'app_front_produit_detail',
        methods: ['GET']
    )]
    public function detail(
        Produit $produit
    ): Response {
        return $this->render('front_produit/detail.html.twig', [
            'produit' => $produit,
        ]);
    }
}