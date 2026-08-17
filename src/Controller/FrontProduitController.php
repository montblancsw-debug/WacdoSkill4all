<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontProduitController extends AbstractController
{
    // Affiche les Produits public.
    #[Route(
        '/Produits',
        name: 'app_front_produit',
        methods: ['GET', 'POST']
    )]
    public function index(
        ProduitRepository $produitRepository,
        Request $request
    ): Response {
        $search = $request
            ->request
            ->get('search');

        if ($search !== null) {
            $produits = $produitRepository->search(
                trim((string) $search)
            );
        } else {
            $produits = $produitRepository->findAll();
        }

        return $this->render(
            'front_produit/index.html.twig',
            [
                'produits' => $produits,
                'search' => $search,
            ]
        );
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
        return $this->render(
            'front_produit/detail.html.twig',
            [
                'produit' => $produit,
            ]
        );
    }
}