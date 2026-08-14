<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDashboardController extends AbstractController
{
    // Permet d'accéder au Dashboard depuis /admin.
    #[Route('/admin', name: 'app_admin')]

    // Permet également d'accéder au Dashboard depuis /admin/dashboard.
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        CommandeRepository $commandeRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        // Compte le nombre de catégories.
        $numCategorie = $categorieRepository->count([]);

        // Compte le nombre de produits.
        $numProduit = $produitRepository->count([]);

        // Compte le nombre de commandes.
        $numCommande = $commandeRepository->count([]);

        // Compte le nombre d'utilisateurs.
        $numUtilisateur = $utilisateurRepository->count([]);

        // Envoie les statistiques au template du Dashboard.
        return $this->render('admin_dashboard/index.html.twig', [
            'numCategorie' => $numCategorie,
            'numProduit' => $numProduit,
            'numCommande' => $numCommande,
            'numUtilisateur' => $numUtilisateur,
        ]);
    }
}
