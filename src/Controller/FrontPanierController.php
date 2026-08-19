<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Menu;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\MenuRepository;
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
    #[Route(
        '',
        name: 'app_front_panier',
        methods: ['GET']
    )]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        MenuRepository $menuRepository
    ): Response {
        $panier = $this->buildPanier(
            $request,
            $produitRepository,
            $menuRepository
        );

        return $this->render(
            'front_panier/index.html.twig',
            [
                'articles' => $panier['articles'],
                'menus' => $panier['menus'],
                'total' => $panier['total'],
                'quantiteTotale' =>
                    $panier['quantiteTotale'],
            ]
        );
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
                'panier_add'
                . $produit->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
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
            $request
                ->getPayload()
                ->getInt(
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

    // Augmente la quantité d'un produit.
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
                'panier_plus'
                . $produit->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$produit->isProduitStock()) {
            $this->addFlash(
                'warning',
                'Ce produit n\'est plus disponible.'
            );

            return $this->redirectToRoute(
                'app_front_panier'
            );
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

    // Diminue la quantité d'un produit.
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
                'panier_minus'
                . $produit->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
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
                'panier_delete'
                . $produit->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
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

    // Ajoute un menu au panier.
    #[Route(
        '/menu/ajouter/{id}',
        name: 'app_front_panier_menu_add',
        methods: ['POST']
    )]
    public function addMenu(
        Request $request,
        Menu $menu
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_menu_add'
                . $menu->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isMenuDisponible($menu)) {
            $this->addFlash(
                'warning',
                'Ce menu n\'est actuellement pas disponible.'
            );

            return $this->redirectToRoute(
                'app_front_menu_detail',
                [
                    'id' => $menu->getId(),
                ]
            );
        }

        $quantite = max(
            1,
            $request
                ->getPayload()
                ->getInt(
                    'quantite',
                    1
                )
        );

        $session = $request->getSession();

        $panierMenus = $session->get(
            'panier_menus',
            []
        );

        $menuId = $menu->getId();

        if (isset($panierMenus[$menuId])) {
            $panierMenus[$menuId] += $quantite;
        } else {
            $panierMenus[$menuId] = $quantite;
        }

        $session->set(
            'panier_menus',
            $panierMenus
        );

        $this->addFlash(
            'success',
            'Le menu a été ajouté au panier.'
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Augmente la quantité d'un menu.
    #[Route(
        '/menu/plus/{id}',
        name: 'app_front_panier_menu_plus',
        methods: ['POST']
    )]
    public function plusMenu(
        Request $request,
        Menu $menu
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_menu_plus'
                . $menu->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isMenuDisponible($menu)) {
            $this->addFlash(
                'warning',
                'Ce menu n\'est plus disponible.'
            );

            return $this->redirectToRoute(
                'app_front_panier'
            );
        }

        $session = $request->getSession();

        $panierMenus = $session->get(
            'panier_menus',
            []
        );

        $menuId = $menu->getId();

        if (isset($panierMenus[$menuId])) {
            $panierMenus[$menuId]++;
        }

        $session->set(
            'panier_menus',
            $panierMenus
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Diminue la quantité d'un menu.
    #[Route(
        '/menu/moins/{id}',
        name: 'app_front_panier_menu_minus',
        methods: ['POST']
    )]
    public function minusMenu(
        Request $request,
        Menu $menu
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_menu_minus'
                . $menu->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $session = $request->getSession();

        $panierMenus = $session->get(
            'panier_menus',
            []
        );

        $menuId = $menu->getId();

        if (isset($panierMenus[$menuId])) {
            if ($panierMenus[$menuId] > 1) {
                $panierMenus[$menuId]--;
            } else {
                unset(
                    $panierMenus[$menuId]
                );
            }
        }

        $session->set(
            'panier_menus',
            $panierMenus
        );

        return $this->redirectToRoute(
            'app_front_panier'
        );
    }

    // Supprime un menu du panier.
    #[Route(
        '/menu/supprimer/{id}',
        name: 'app_front_panier_menu_delete',
        methods: ['POST']
    )]
    public function deleteMenu(
        Request $request,
        Menu $menu
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'panier_menu_delete'
                . $menu->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $session = $request->getSession();

        $panierMenus = $session->get(
            'panier_menus',
            []
        );

        unset(
            $panierMenus[$menu->getId()]
        );

        $session->set(
            'panier_menus',
            $panierMenus
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
        MenuRepository $menuRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $this->getUser();

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
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $panier = $this->buildPanier(
            $request,
            $produitRepository,
            $menuRepository
        );

        if (
            $panier['articles'] === []
            && $panier['menus'] === []
        ) {
            $this->addFlash(
                'warning',
                'Votre panier est vide.'
            );

            return $this->redirectToRoute(
                'app_front_panier'
            );
        }

        // Vérifie les produits.
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

        // Vérifie les menus.
        foreach ($panier['menus'] as $articleMenu) {
            if (!$articleMenu['disponible']) {
                $this->addFlash(
                    'warning',
                    'Un menu du panier n\'est plus disponible.'
                );

                return $this->redirectToRoute(
                    'app_front_panier'
                );
            }
        }

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

        // Crée les lignes produits.
        foreach ($panier['articles'] as $article) {
            $produit = $article['produit'];
            $quantite = $article['quantite'];

            $prixUnitaire =
                (float) $produit->getProduitPrix();

            $detail = new Detail();

            $detail->setProduit(
                $produit
            );

            $detail->setMenu(
                null
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

        // Crée les lignes menus.
        foreach ($panier['menus'] as $articleMenu) {
            $menu = $articleMenu['menu'];
            $quantite = $articleMenu['quantite'];

            $prixUnitaire =
                (float) $menu->getMenuPrix();

            $detail = new Detail();

            $detail->setProduit(
                null
            );

            $detail->setMenu(
                $menu
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

        // Vide entièrement le panier.
        $request
            ->getSession()
            ->remove(
                'panier'
            );

        $request
            ->getSession()
            ->remove(
                'panier_menus'
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

    // Construit le panier complet.
    private function buildPanier(
        Request $request,
        ProduitRepository $produitRepository,
        MenuRepository $menuRepository
    ): array {
        $session = $request->getSession();

        $articles = [];
        $menus = [];

        $total = 0.0;
        $quantiteTotale = 0;

        // Produits.
        $panierSession = $session->get(
            'panier',
            []
        );

        $panierNettoye = $panierSession;

        foreach (
            $panierSession
            as $produitId => $quantite
        ) {
            $produit = $produitRepository->find(
                $produitId
            );

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

        // Menus.
        $panierMenusSession = $session->get(
            'panier_menus',
            []
        );

        $panierMenusNettoye =
            $panierMenusSession;

        foreach (
            $panierMenusSession
            as $menuId => $quantite
        ) {
            $menu = $menuRepository->find(
                $menuId
            );

            if ($menu === null) {
                unset(
                    $panierMenusNettoye[$menuId]
                );

                continue;
            }

            $quantite = max(
                1,
                (int) $quantite
            );

            $sousTotal =
                (float) $menu->getMenuPrix()
                * $quantite;

            $menus[] = [
                'menu' => $menu,
                'quantite' => $quantite,
                'sousTotal' => $sousTotal,

                'disponible' =>
                    $this->isMenuDisponible(
                        $menu
                    ),
            ];

            $total += $sousTotal;
            $quantiteTotale += $quantite;
        }

        if (
            $panierMenusNettoye
            !== $panierMenusSession
        ) {
            $session->set(
                'panier_menus',
                $panierMenusNettoye
            );
        }

        return [
            'articles' => $articles,
            'menus' => $menus,
            'total' => $total,
            'quantiteTotale' =>
                $quantiteTotale,
        ];
    }

    // Vérifie si un menu peut être commandé.
    private function isMenuDisponible(
        Menu $menu
    ): bool {
        if (!$menu->isMenuStatus()) {
            return false;
        }

        if ($menu->getProduits()->isEmpty()) {
            return false;
        }

        foreach ($menu->getProduits() as $produit) {
            if (!$produit->isProduitStock()) {
                return false;
            }
        }

        return true;
    }
}