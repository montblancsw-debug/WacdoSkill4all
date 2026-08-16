<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class FrontPanierController extends AbstractController
{
    // Affiche le panier.
    #[Route('', name: 'app_front_panier', methods: ['GET'])]
    public function index(
        Request $request,
        ProduitRepository $produitRepository
    ): Response {
        $panier = $this->buildPanier(
            $request,
            $produitRepository
        );

        return $this->render('front_panier/index.html.twig', [
            'articles' => $panier['articles'],
            'total' => $panier['total'],
            'quantiteTotale' => $panier['quantiteTotale'],
        ]);
    }

    // Ajoute un produit au panier.
    #[Route(
        '/ajouter/{id}',
        name: 'app_front_panier_add',
        methods: ['POST']
    )]
    public function add(
        Request $request,
        Produit $produit
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_add' . $produit->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$produit->isProduitStock()) {
            $this->addFlash(
                'warning',
                'Ce produit n\'est actuellement pas disponible.'
            );

            return $this->redirectToRoute(
                'app_front_produit_detail',
                [
                    'id' => $produit->getId(),
                ]
            );
        }

        $quantite = max(
            1,
            $request->getPayload()->getInt(
                'quantite',
                1
            )
        );

        $session = $request->getSession();

        $panier = $session->get(
            'panier',
            []
        );

        $produitId = $produit->getId();

        if (isset($panier[$produitId])) {
            $panier[$produitId] += $quantite;
        } else {
            $panier[$produitId] = $quantite;
        }

        $session->set(
            'panier',
            $panier
        );

        $this->addFlash(
            'success',
            'Le produit a été ajouté au panier.'
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Augmente la quantité.
    #[Route(
        '/plus/{id}',
        name: 'app_front_panier_plus',
        methods: ['POST']
    )]
    public function plus(
        Request $request,
        Produit $produit
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_plus' . $produit->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $session = $request->getSession();

        $panier = $session->get(
            'panier',
            []
        );

        $produitId = $produit->getId();

        if (isset($panier[$produitId])) {
            $panier[$produitId]++;
        }

        $session->set(
            'panier',
            $panier
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Diminue la quantité.
    #[Route(
        '/moins/{id}',
        name: 'app_front_panier_minus',
        methods: ['POST']
    )]
    public function minus(
        Request $request,
        Produit $produit
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_minus' . $produit->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $session = $request->getSession();

        $panier = $session->get(
            'panier',
            []
        );

        $produitId = $produit->getId();

        if (isset($panier[$produitId])) {
            if ($panier[$produitId] > 1) {
                $panier[$produitId]--;
            } else {
                unset(
                    $panier[$produitId]
                );
            }
        }

        $session->set(
            'panier',
            $panier
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Supprime un produit du panier.
    #[Route(
        '/supprimer/{id}',
        name: 'app_front_panier_delete',
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        Produit $produit
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_delete' . $produit->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $session = $request->getSession();

        $panier = $session->get(
            'panier',
            []
        );

        unset(
            $panier[$produit->getId()]
        );

        $session->set(
            'panier',
            $panier
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Transforme le panier en commande.
    #[Route(
        '/valider',
        name: 'app_front_panier_validate',
        methods: ['POST']
    )]
    public function validatePanier(
        Request $request,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $this->getUser();

        // Demande une connexion avant de commander.
        if (!$utilisateur instanceof Utilisateur) {
            $this->addFlash(
                'warning',
                'Vous devez être connecté pour valider votre commande.'
            );

            return $this->redirectToRoute(
                'app_login'
            );
        }

        if (
            !$this->isCsrfTokenValid(
                'panier_validate',
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $panier = $this->buildPanier(
            $request,
            $produitRepository
        );

        // Empêche la création d'une commande vide.
        if ($panier['articles'] === []) {
            $this->addFlash(
                'warning',
                'Votre panier est vide.'
            );

            return $this->redirectToRoute(
                'app_front_panier'
            );
        }

        // Vérifie une dernière fois la disponibilité.
        foreach ($panier['articles'] as $article) {
            if (!$article['produit']->isProduitStock()) {
                $this->addFlash(
                    'warning',
                    'Un produit du panier n\'est plus disponible.'
                );

                return $this->redirectToRoute(
                    'app_front_panier'
                );
            }
        }

        // Crée la commande.
        $commande = new Commande();

        $commande->setCommandeDate(
            new \DateTime()
        );

        $commande->setCommandeStatut(
            'en attente'
        );

        $commande->setCommandeTotal(
            number_format(
                $panier['total'],
                2,
                '.',
                ''
            )
        );

        $commande->setUtilisateur(
            $utilisateur
        );

        $entityManager->persist(
            $commande
        );

        // Crée une ligne Detail pour chaque produit.
        foreach ($panier['articles'] as $article) {
            $produit = $article['produit'];
            $quantite = $article['quantite'];

            $prixUnitaire = (float) $produit->getProduitPrix();

            $detail = new Detail();

            $detail->setProduit(
                $produit
            );

            $detail->setCommande(
                $commande
            );

            $detail->setDetailQuantite(
                $quantite
            );

            $detail->setDetailUnitaire(
                number_format(
                    $prixUnitaire,
                    2,
                    '.',
                    ''
                )
            );

            $detail->setDetailTotal(
                number_format(
                    $prixUnitaire * $quantite,
                    2,
                    '.',
                    ''
                )
            );

            $commande->addDetail(
                $detail
            );

            $entityManager->persist(
                $detail
            );
        }

        $entityManager->flush();

        // Vide le panier après validation.
        $request->getSession()->remove(
            'panier'
        );

        $this->addFlash(
            'success',
            'Votre commande a été validée avec succès.'
        );

        return $this->redirectToRoute(
            'app_front_commande_show',
            [
                'id' => $commande->getId(),
            ],
            Response::HTTP_SEE_OTHER
        );
    }

    // Construit les données affichées dans le panier.
    private function buildPanier(
        Request $request,
        ProduitRepository $produitRepository
    ): array {
        $session = $request->getSession();

        $panierSession = $session->get(
            'panier',
            []
        );

        $articles = [];
        $total = 0.0;
        $quantiteTotale = 0;

        $panierNettoye = $panierSession;

        foreach ($panierSession as $produitId => $quantite) {
            $produit = $produitRepository->find(
                $produitId
            );

            // Retire automatiquement les produits inexistants.
            if ($produit === null) {
                unset(
                    $panierNettoye[$produitId]
                );

                continue;
            }

            $quantite = max(
                1,
                (int) $quantite
            );

            $sousTotal =
                (float) $produit->getProduitPrix()
                * $quantite;

            $articles[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'sousTotal' => $sousTotal,
            ];

            $total += $sousTotal;
            $quantiteTotale += $quantite;
        }

        if ($panierNettoye !== $panierSession) {
            $session->set(
                'panier',
                $panierNettoye
            );
        }

        return [
            'articles' => $articles,
            'total' => $total,
            'quantiteTotale' => $quantiteTotale,
        ];
    }
}