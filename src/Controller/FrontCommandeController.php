<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-commandes')]
class FrontCommandeController extends AbstractController
{
    // Affiche les commandes du client connecté.
    #[Route(
        '',
        name: 'app_front_commande',
        methods: ['GET']
    )]
    public function index(
        CommandeRepository $commandeRepository
    ): Response {
        $utilisateur = $this->getCurrentUtilisateur();

        if ($utilisateur === null) {
            return $this->redirectToRoute(
                'app_login'
            );
        }

        return $this->render(
            'front_commande/index.html.twig',
            [
                'commandes' => $commandeRepository->findBy(
                    [
                        'utilisateur' => $utilisateur,
                    ],
                    [
                        'commande_date' => 'DESC',
                    ]
                ),
            ]
        );
    }

    // Affiche une commande appartenant au client.
    #[Route(
        '/{id}',
        name: 'app_front_commande_show',
        methods: ['GET']
    )]
    public function show(
        Commande $commande
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        return $this->render(
            'front_commande/show.html.twig',
            [
                'commande' => $commande,
            ]
        );
    }

    // Affiche la modification d'une commande.
    #[Route(
        '/{id}/modifier',
        name: 'app_front_commande_edit',
        methods: ['GET']
    )]
    public function edit(
        Commande $commande,
        ProduitRepository $produitRepository
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (!$this->canModify($commande)) {
            $this->addModificationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        return $this->render(
            'front_commande/edit.html.twig',
            [
                'commande' => $commande,
                'produitsDisponibles' => $produitRepository->findBy(
                    [
                        'produit_stock' => true,
                    ],
                    [
                        'produit_nom' => 'ASC',
                    ]
                ),
            ]
        );
    }

    // Ajoute un produit à une commande.
    #[Route(
        '/{id}/produit/ajouter',
        name: 'app_front_commande_add_product',
        methods: ['POST']
    )]
    public function addProduct(
        Request $request,
        Commande $commande,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (!$this->canModify($commande)) {
            $this->addModificationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        if (
            !$this->isCsrfTokenValid(
                'front_commande_add_product' . $commande->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        $produitId = $request
            ->getPayload()
            ->getInt('produit_id');

        $quantite = max(
            1,
            $request
                ->getPayload()
                ->getInt(
                    'quantite',
                    1
                )
        );

        $produit = $produitRepository->find(
            $produitId
        );

        if (
            $produit === null
            || !$produit->isProduitStock()
        ) {
            $this->addFlash(
                'warning',
                'Le produit sélectionné n\'est plus disponible.'
            );

            return $this->redirectToRoute(
                'app_front_commande_edit',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $detailExistant = null;

        foreach ($commande->getDetails() as $detail) {
            if (
                $detail->getProduit()?->getId()
                === $produit->getId()
            ) {
                $detailExistant = $detail;
                break;
            }
        }

        if ($detailExistant !== null) {
            $detailExistant->setDetailQuantite(
                ($detailExistant->getDetailQuantite() ?? 0)
                + $quantite
            );

            $this->updateDetailTotal(
                $detailExistant
            );
        } else {
            $detail = new Detail();

            $detail->setCommande(
                $commande
            );

            $detail->setProduit(
                $produit
            );

            $detail->setDetailQuantite(
                $quantite
            );

            $detail->setDetailUnitaire(
                number_format(
                    (float) $produit->getProduitPrix(),
                    2,
                    '.',
                    ''
                )
            );

            $this->updateDetailTotal(
                $detail
            );

            $commande->addDetail(
                $detail
            );

            $entityManager->persist(
                $detail
            );
        }

        $this->recalculateCommandeTotal(
            $commande
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le produit a été ajouté à votre commande.'
        );

        return $this->redirectToRoute(
            'app_front_commande_edit',
            [
                'id' => $commande->getId(),
            ]
        );
    }

    // Augmente la quantité d'un produit.
    #[Route(
        '/{id}/detail/{detailId}/plus',
        name: 'app_front_commande_detail_plus',
        methods: ['POST']
    )]
    public function increaseDetail(
        Request $request,
        Commande $commande,
        int $detailId,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (!$this->canModify($commande)) {
            $this->addModificationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $detail = $this->findDetail(
            $commande,
            $detailId,
            $entityManager
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_detail_plus' . $detail->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (
            $detail->getProduit() === null
            || !$detail->getProduit()->isProduitStock()
        ) {
            $this->addFlash(
                'warning',
                'Ce produit n\'est plus disponible.'
            );

            return $this->redirectToRoute(
                'app_front_commande_edit',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $detail->setDetailQuantite(
            ($detail->getDetailQuantite() ?? 0) + 1
        );

        $this->updateDetailTotal(
            $detail
        );

        $this->recalculateCommandeTotal(
            $commande
        );

        $entityManager->flush();

        return $this->redirectToRoute(
            'app_front_commande_edit',
            [
                'id' => $commande->getId(),
            ]
        );
    }

    // Diminue la quantité d'un produit.
    #[Route(
        '/{id}/detail/{detailId}/moins',
        name: 'app_front_commande_detail_minus',
        methods: ['POST']
    )]
    public function decreaseDetail(
        Request $request,
        Commande $commande,
        int $detailId,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (!$this->canModify($commande)) {
            $this->addModificationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $detail = $this->findDetail(
            $commande,
            $detailId,
            $entityManager
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_detail_minus' . $detail->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (($detail->getDetailQuantite() ?? 0) > 1) {
            $detail->setDetailQuantite(
                $detail->getDetailQuantite() - 1
            );

            $this->updateDetailTotal(
                $detail
            );

            $this->recalculateCommandeTotal(
                $commande
            );

            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_front_commande_edit',
            [
                'id' => $commande->getId(),
            ]
        );
    }

    // Retire un produit de la commande.
    #[Route(
        '/{id}/detail/{detailId}/supprimer',
        name: 'app_front_commande_detail_delete',
        methods: ['POST']
    )]
    public function deleteDetail(
        Request $request,
        Commande $commande,
        int $detailId,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (!$this->canModify($commande)) {
            $this->addModificationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $detail = $this->findDetail(
            $commande,
            $detailId,
            $entityManager
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_detail_delete' . $detail->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($commande->getDetails()->count() <= 1) {
            $this->addFlash(
                'warning',
                'Une commande doit contenir au moins un produit.'
            );

            return $this->redirectToRoute(
                'app_front_commande_edit',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $commande->removeDetail(
            $detail
        );

        $entityManager->remove(
            $detail
        );

        $this->recalculateCommandeTotal(
            $commande
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le produit a été retiré de votre commande.'
        );

        return $this->redirectToRoute(
            'app_front_commande_edit',
            [
                'id' => $commande->getId(),
            ]
        );
    }

    // Annule une commande sans supprimer son historique.
    #[Route(
        '/{id}/annuler',
        name: 'app_front_commande_cancel',
        methods: ['POST']
    )]
    public function cancel(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_cancel' . $commande->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->canCancel($commande)) {
            $this->addCancellationDeniedFlash(
                $commande
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        $commande->setCommandeStatut(
            'annulée'
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre commande a été annulée.'
        );

        return $this->redirectToRoute(
            'app_front_commande',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    // Relance une ancienne commande annulée.
    #[Route(
        '/{id}/relancer',
        name: 'app_front_commande_relaunch',
        methods: ['POST']
    )]
    public function relaunch(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_relaunch' . $commande->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($commande->getCommandeStatut() !== 'annulée') {
            $this->addFlash(
                'warning',
                'Seule une commande annulée peut être relancée.'
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        if ($commande->getDetails()->isEmpty()) {
            $this->addFlash(
                'warning',
                'Cette commande ne contient aucun produit et ne peut pas être relancée.'
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        // Vérifie tous les produits avant de créer la nouvelle commande.
        foreach ($commande->getDetails() as $detail) {
            $produit = $detail->getProduit();

            if ($produit === null) {
                $this->addFlash(
                    'warning',
                    'Cette commande contient un produit qui n\'existe plus et ne peut pas être relancée.'
                );

                return $this->redirectToRoute(
                    'app_front_commande_show',
                    [
                        'id' => $commande->getId(),
                    ]
                );
            }

            if (!$produit->isProduitStock()) {
                $this->addFlash(
                    'warning',
                    'Le produit "' . $produit->getProduitNom() . '" n\'est plus disponible. La commande ne peut pas être relancée.'
                );

                return $this->redirectToRoute(
                    'app_front_commande_show',
                    [
                        'id' => $commande->getId(),
                    ]
                );
            }
        }

        $utilisateur = $this->getCurrentUtilisateur();

        if ($utilisateur === null) {
            throw $this->createAccessDeniedException();
        }

        // Crée une nouvelle commande.
        $nouvelleCommande = new Commande();

        $nouvelleCommande->setCommandeDate(
            new \DateTime()
        );

        $nouvelleCommande->setCommandeStatut(
            'en attente'
        );

        $nouvelleCommande->setUtilisateur(
            $utilisateur
        );

        $nouvelleCommande->setCommandeTotal(
            '0.00'
        );

        $entityManager->persist(
            $nouvelleCommande
        );

        $totalCommande = 0.0;

        // Recrée les lignes avec les prix actuels.
        foreach ($commande->getDetails() as $ancienDetail) {
            $produit = $ancienDetail->getProduit();

            if ($produit === null) {
                continue;
            }

            $quantite = max(
                1,
                (int) $ancienDetail->getDetailQuantite()
            );

            $prixUnitaire =
                (float) $produit->getProduitPrix();

            $totalLigne =
                $prixUnitaire * $quantite;

            $nouveauDetail = new Detail();

            $nouveauDetail->setCommande(
                $nouvelleCommande
            );

            $nouveauDetail->setProduit(
                $produit
            );

            $nouveauDetail->setDetailQuantite(
                $quantite
            );

            $nouveauDetail->setDetailUnitaire(
                number_format(
                    $prixUnitaire,
                    2,
                    '.',
                    ''
                )
            );

            $nouveauDetail->setDetailTotal(
                number_format(
                    $totalLigne,
                    2,
                    '.',
                    ''
                )
            );

            $nouvelleCommande->addDetail(
                $nouveauDetail
            );

            $entityManager->persist(
                $nouveauDetail
            );

            $totalCommande += $totalLigne;
        }

        $nouvelleCommande->setCommandeTotal(
            number_format(
                $totalCommande,
                2,
                '.',
                ''
            )
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre commande a été relancée sous le numéro #' . $nouvelleCommande->getId() . '.'
        );

        return $this->redirectToRoute(
            'app_front_commande_show',
            [
                'id' => $nouvelleCommande->getId(),
            ],
            Response::HTTP_SEE_OTHER
        );
    }

    // Supprime définitivement une commande annulée.
    #[Route(
        '/{id}/supprimer',
        name: 'app_front_commande_delete',
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        $this->assertCommandeOwner(
            $commande
        );

        if (
            !$this->isCsrfTokenValid(
                'front_commande_delete' . $commande->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($commande->getCommandeStatut() !== 'annulée') {
            $this->addFlash(
                'warning',
                'Seule une commande annulée peut être supprimée.'
            );

            return $this->redirectToRoute(
                'app_front_commande_show',
                [
                    'id' => $commande->getId(),
                ]
            );
        }

        // Supprime d'abord les lignes de commande.
        foreach ($commande->getDetails()->toArray() as $detail) {
            $entityManager->remove(
                $detail
            );
        }

        // Supprime ensuite la commande.
        $entityManager->remove(
            $commande
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'La commande a été supprimée définitivement.'
        );

        return $this->redirectToRoute(
            'app_front_commande',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    // Retourne l'utilisateur connecté.
    private function getCurrentUtilisateur(): ?Utilisateur
    {
        $utilisateur = $this->getUser();

        return $utilisateur instanceof Utilisateur
            ? $utilisateur
            : null;
    }

    // Vérifie que la commande appartient au client.
    private function assertCommandeOwner(
        Commande $commande
    ): void {
        $utilisateur = $this->getCurrentUtilisateur();

        if ($utilisateur === null) {
            throw $this->createAccessDeniedException(
                'Vous devez être connecté.'
            );
        }

        if (
            $commande->getUtilisateur()?->getId()
            !== $utilisateur->getId()
        ) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas accéder à cette commande.'
            );
        }
    }

    // Indique si la commande peut être modifiée.
    private function canModify(
        Commande $commande
    ): bool {
        return in_array(
            $commande->getCommandeStatut(),
            [
                'en attente',
                'en préparation',
            ],
            true
        );
    }

    // Indique si la commande peut être annulée.
    private function canCancel(
        Commande $commande
    ): bool {
        return in_array(
            $commande->getCommandeStatut(),
            [
                'en attente',
                'en préparation',
                'prête',
            ],
            true
        );
    }

    // Affiche le motif d'interdiction de modification.
    private function addModificationDeniedFlash(
        Commande $commande
    ): void {
        $statut = $commande->getCommandeStatut();

        if ($statut === 'prête') {
            $message =
                'Cette commande est déjà prête et ne peut plus être modifiée. Vous pouvez encore l\'annuler.';
        } elseif ($statut === 'en livraison') {
            $message =
                'Cette commande est déjà en cours de livraison et ne peut plus être modifiée.';
        } elseif ($statut === 'livrée') {
            $message =
                'Cette commande a déjà été livrée et ne peut plus être modifiée.';
        } elseif ($statut === 'annulée') {
            $message =
                'Cette commande est annulée et ne peut plus être modifiée.';
        } else {
            $message =
                'Cette commande ne peut plus être modifiée.';
        }

        $this->addFlash(
            'warning',
            $message
        );
    }

    // Affiche le motif d'interdiction d'annulation.
    private function addCancellationDeniedFlash(
        Commande $commande
    ): void {
        $statut = $commande->getCommandeStatut();

        if ($statut === 'en livraison') {
            $message =
                'Cette commande est déjà en cours de livraison et ne peut plus être annulée.';
        } elseif ($statut === 'livrée') {
            $message =
                'Cette commande a déjà été livrée et ne peut plus être annulée.';
        } elseif ($statut === 'annulée') {
            $message =
                'Cette commande est déjà annulée.';
        } else {
            $message =
                'Cette commande ne peut plus être annulée.';
        }

        $this->addFlash(
            'warning',
            $message
        );
    }

    // Récupère une ligne appartenant à la commande.
    private function findDetail(
        Commande $commande,
        int $detailId,
        EntityManagerInterface $entityManager
    ): Detail {
        $detail = $entityManager
            ->getRepository(Detail::class)
            ->find(
                $detailId
            );

        if (
            $detail === null
            || $detail->getCommande()?->getId()
                !== $commande->getId()
        ) {
            throw $this->createNotFoundException(
                'Cette ligne de commande n\'existe pas.'
            );
        }

        return $detail;
    }

    // Recalcule le total d'une ligne.
    private function updateDetailTotal(
        Detail $detail
    ): void {
        $prixUnitaire =
            (float) $detail->getDetailUnitaire();

        $quantite =
            $detail->getDetailQuantite() ?? 0;

        $detail->setDetailTotal(
            number_format(
                $prixUnitaire * $quantite,
                2,
                '.',
                ''
            )
        );
    }

    // Recalcule le total de la commande.
    private function recalculateCommandeTotal(
        Commande $commande
    ): void {
        $total = 0.0;

        foreach ($commande->getDetails() as $detail) {
            // Ignore un ancien produit supprimé.
            if ($detail->getProduit() === null) {
                continue;
            }

            $this->updateDetailTotal(
                $detail
            );

            $total +=
                (float) $detail->getDetailTotal();
        }

        $commande->setCommandeTotal(
            number_format(
                $total,
                2,
                '.',
                ''
            )
        );
    }
}