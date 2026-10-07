<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Menu;
use App\Entity\Utilisateur;
use App\Repository\MenuRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //

#[Route('/api/commandes', name: 'app_api_commande_')]

final class CommandeApiController extends AbstractController
{

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Reçoit le détail d'une commande depuis le Front-end.

    #[Route('',name: 'create',methods: ['POST'])]

    public function create(Request $request,ProduitRepository $produitRepository,MenuRepository $menuRepository,EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->getContentTypeFormat() !== 'json')
        {
            return $this->json(['message' => 'La requête doit être envoyée au format JSON.',],JsonResponse::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        try
        {
            $data = json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR);
        } catch (\JsonException)
        {
            return $this->json(['message' => 'Le JSON envoyé est invalide.',],JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($data))
        {
                return $this->json(['message' => 'Le contenu de la commande est invalide.',],JsonResponse::HTTP_BAD_REQUEST);
        }

        $detailsEnvoyes = $data['details'] ?? null;

        if (!is_array($detailsEnvoyes)|| $detailsEnvoyes === [])
        {
            return $this->json(['message' => 'La commande doit contenir au moins un article.',],JsonResponse::HTTP_BAD_REQUEST);
        }

        $detailsValides = [];
        $commandeTotal = 0.0;

        foreach ($detailsEnvoyes as $index => $detailEnvoye)
        {
            if (!is_array($detailEnvoye))
            {
                return $this->json(['message' => sprintf('Le détail numéro %d est invalide.',$index + 1),],JsonResponse::HTTP_BAD_REQUEST);
            }

            $type = strtolower(trim((string) ($detailEnvoye['type'] ?? '')));
            $id = $this->normalizePositiveInteger($detailEnvoye['id'] ?? null);
            $quantite = $this->normalizePositiveInteger($detailEnvoye['quantite'] ?? null);

            if (!in_array($type,['produit','menu',],true))
            {
                return $this->json(['message' => sprintf('Le type du détail numéro %d doit être "produit" ou "menu".',$index + 1),],JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($id === null)
            {
                return $this->json(['message' => sprintf('L\'identifiant du détail numéro %d est invalide.',$index + 1),],JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($quantite === null)
            {
                return $this->json(['message' => sprintf('La quantité du détail numéro %d doit être supérieure à zéro.',$index + 1),],JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($type === 'produit') {
                $produit = $produitRepository->find(
                    $id
                );

                if ($produit === null)
                {
                    return $this->json(['message' => sprintf('Le produit #%d est introuvable.',$id),],JsonResponse::HTTP_NOT_FOUND);
                }

                if (!$produit->isProduitStock())
                {
                    return $this->json(['message' => sprintf('Le produit "%s" n\'est pas disponible.',$produit->getProduitNom()),],JsonResponse::HTTP_CONFLICT);
                }

                $prixUnitaire = (float) $produit->getProduitPrix();
                $detailTotal = $prixUnitaire * $quantite;

                $detailsValides[] =
                [
                    'type' => 'produit',
                    'produit' => $produit,
                    'menu' => null,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total' => $detailTotal,
                ];

                $commandeTotal += $detailTotal;
                continue;
            }
            $menu = $menuRepository->find($id);

            if ($menu === null)
            {
                return $this->json(['message' => sprintf('Le menu #%d est introuvable.',$id),],JsonResponse::HTTP_NOT_FOUND);
            }

            if (!$this->isMenuDisponible($menu))
            {
                return $this->json(['message' => sprintf('Le menu "%s" n\'est pas disponible.',$menu->getMenuNom()),],JsonResponse::HTTP_CONFLICT);
            }

            $prixUnitaire = (float) $menu->getMenuPrix();
            $detailTotal = $prixUnitaire * $quantite;

            $detailsValides[] =
            [
                'type' => 'menu',
                'produit' => null,
                'menu' => $menu,
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'total' => $detailTotal,
            ];

            $commandeTotal += $detailTotal;
        }

        $commande = new Commande();
        $commande->setCommandeDate(new \DateTime());
        $commande->setCommandeStatut('en attente');
        $commande->setCommandeTotal(number_format($commandeTotal,2,'.',''));
        $utilisateur = $this->getUser();

        if ($utilisateur instanceof Utilisateur)
        {
            $commande->setUtilisateur($utilisateur);
        }

        $entityManager->persist($commande);

        foreach ($detailsValides as $detailValide)
        {
            $detail = new Detail();
            $detail->setCommande($commande);
            $detail->setProduit($detailValide['produit']);
            $detail->setMenu($detailValide['menu']);
            $detail->setDetailQuantite($detailValide['quantite']);
            $detail->setDetailUnitaire(number_format($detailValide['prix_unitaire'],2,'.',''));
            $detail->setDetailTotal(number_format($detailValide['total'],2,'.',''));
            $commande->addDetail($detail);
            $entityManager->persist($detail);
        }

        $entityManager->flush();

        return $this->json($this->normalizeCommande($commande),JsonResponse::HTTP_CREATED);
    }

    // ====================================================== //
    // ================= METHODES PRIVEES ================== //
    // ====================================================== //

    // Vérifie et convertit une valeur en entier positif.

    private function normalizePositiveInteger(mixed $value): ?int
    {
        if (is_int($value))
        {
            return $value > 0? $value : null;
        }

        if (is_string($value)&& ctype_digit($value))
        {
            $integerValue = (int) $value;
            return $integerValue > 0? $integerValue: null;
        }
        return null;
    }

    // Vérifie qu'un menu et tous ses produits sont disponibles.

    private function isMenuDisponible(Menu $menu): bool
    {
        if (!$menu->isMenuStatus())
        {
            return false;
        }

        if ($menu->getProduits()->isEmpty())
        {
            return false;
        }

        foreach ($menu->getProduits()as $produit)
        {
            if (!$produit->isProduitStock()) {return false;}
        }
        return true;
    }

    // Transforme une commande en tableau pour la réponse JSON.

    private function normalizeCommande(Commande $commande): array
    {
        $details = [];

        foreach ($commande->getDetails()as $detail)
        {
            if ($detail->getProduit() !== null)
            {
                $article = ['type' => 'produit','id' => $detail->getProduit()->getId(),'nom' => $detail->getProduit()->getProduitNom(),];
            } elseif ($detail->getMenu() !== null)
            {
                $article = ['type' => 'menu','id' => $detail->getMenu()->getId(),'nom' => $detail->getMenu()->getMenuNom(),];
            } else {
                $article = ['type' => 'inconnu','id' => null,'nom' => null,];
            }

            $details[] =
            [
                'article' => $article,
                'quantite' => $detail->getDetailQuantite(),
                'prix_unitaire' => $detail->getDetailUnitaire(),
                'total' => $detail->getDetailTotal(),
            ];
        }

        $utilisateur = $commande->getUtilisateur();

        return
        [
            'id' => $commande->getId(),
            'numero' => '#' . $commande->getId(),
            'statut' => $commande->getCommandeStatut(),
            'date' => $commande->getCommandeDate()?->format(\DateTimeInterface::ATOM),
            'total' => $commande->getCommandeTotal(),
            'utilisateur' => $utilisateur !== null?['id' => $utilisateur->getId(),'email' => $utilisateur->getUtilisateurEmail(),]: null,'details' => $details,
        ];
    }
}