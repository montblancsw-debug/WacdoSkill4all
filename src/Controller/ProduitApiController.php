<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //

#[Route('/api/produits', name: 'app_api_produit_')]

final class ProduitApiController extends AbstractController
{
    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Retourne la liste détaillée des produits au format JSON.

    #[Route('', name: 'index', methods: ['GET'])]

    public function index(Request $request,ProduitRepository $produitRepository,CategorieRepository $categorieRepository): JsonResponse
    {
        $categorieId = trim((string) $request->query->get('categorie',''));
        if ($categorieId !== '')
        {
            if (!ctype_digit($categorieId))
            {
                return $this->json(['message' => 'L\'identifiant de catégorie est invalide.',],JsonResponse::HTTP_BAD_REQUEST);
            }
            $categorie = $categorieRepository->find((int) $categorieId);
            if ($categorie === null)
            {
                return $this->json(['message' => 'Catégorie introuvable.',],JsonResponse::HTTP_NOT_FOUND);
            }

        $produits = $produitRepository->findBy(['categorie' => $categorie,],['produit_nom' => 'ASC',]);
        } else {
        $produits = $produitRepository->findBy([],['produit_nom' => 'ASC',]);
        }

        $data = array_map( fn (Produit $produit): array => $this->normalizeProduit($produit),$produits);
        return $this->json($data);
    }

    // Retourne le détail d'un produit au format JSON.

    #[Route('/{id}',name: 'show',requirements: ['id' => '\d+',],methods: ['GET'])]

    public function show(int $id,ProduitRepository $produitRepository): JsonResponse
    {
        $produit = $produitRepository->find($id);
        if ($produit === null)
        {
            return $this->json(['message' => 'Produit introuvable.',],JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json($this->normalizeProduit($produit));
    }

    // ====================================================== //
    // ================= METHODES PRIVEES ================== //
    // ====================================================== //

    // Transforme un produit en tableau pour la réponse JSON.

    private function normalizeProduit(Produit $produit): array
    {
        $categorie = $produit->getCategorie();

        return
        [
            'id' => $produit->getId(),
            'nom' => $produit->getProduitNom(),
            'description' => $produit->getProduitDescription(),
            'prix' => $produit->getProduitPrix(),
            'disponible' => $produit->isProduitStock(),
            'image' => $produit->getProduitImage() !== null? '/images/produits/' . $produit->getProduitImage(): null,
            'categorie' => $categorie !== null? ['id' => $categorie->getId(),'nom' => $categorie->getCategorieNom(),]: null,
        ];
    }
}