<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_front_produit', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('front_produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }
}