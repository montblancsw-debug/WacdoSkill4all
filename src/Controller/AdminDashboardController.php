<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        MenuRepository $menuRepository,
        CommandeRepository $commandeRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        return $this->render('admin_dashboard/index.html.twig', [
            'numCategorie' => $categorieRepository->count([]),
            'numProduit' => $produitRepository->count([]),
            'numMenu' => $menuRepository->count([]),
            'numCommande' => $commandeRepository->count([]),
            'numUtilisateur' => $utilisateurRepository->count([]),
        ]);
    }
}