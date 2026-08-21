<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Menu;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //
#[Route('/mes-commandes')]class FrontCommandeController extends AbstractController{
#[Route('',name: 'app_front_commande',methods: ['GET'])]

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

// Affiche les commandes du client connecté.

    public function index(CommandeRepository $commandeRepository): Response {$utilisateur = $this->getCurrentUtilisateur();

        if ($utilisateur === null) {return $this->redirectToRoute('app_login');
        }

        return $this->render('front_commande/index.html.twig',
        ['commandes' => $commandeRepository->findBy(['utilisateur' => $utilisateur,],['commande_date' => 'DESC',]),]
        );
    }

    #[Route('/{id}',name: 'app_front_commande_show',methods: ['GET'])]
    
// Affiche le détail d'une commande du client.
    
    public function show(Commande $commande): Response {
        $this->assertCommandeOwner($commande);

        return $this->render('front_commande/show.html.twig',['commande' => $commande,]);
    }

    #[Route('/{id}/modifier',name: 'app_front_commande_edit',methods: ['GET'])]
    
// Affiche la modification d'une commande.
    
    public function edit(Commande $commande,ProduitRepository $produitRepository,MenuRepository $menuRepository): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        $menusDisponibles = [];

        foreach ($menuRepository->findActive() as $menu) {
            if ($this->isMenuDisponible($menu)) {
                $menusDisponibles[] = $menu;
            }
        }

        return $this->render('front_commande/edit.html.twig',
            [
                'commande' => $commande,
                'produitsDisponibles' =>$produitRepository->findBy(['produit_stock' => true,],['produit_nom' => 'ASC',]),
                'menusDisponibles' =>$menusDisponibles,
            ]
        );
    }

    #[Route('/{id}/produit/ajouter',name: 'app_front_commande_add_product',methods: ['POST'])]
    
// Ajoute un produit à une commande existante.
    
    public function addProduct(Request $request,Commande $commande,ProduitRepository $produitRepository,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        if (!$this->isCsrfTokenValid('front_commande_add_product'. $commande->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $produitId = $request->getPayload()->getInt('produit_id');
        $quantite = max(1,$request->getPayload()->getInt('quantite',1));
        $produit = $produitRepository->find($produitId);

        if ($produit === null|| !$produit->isProduitStock()) {$this->addFlash('warning','Le produit sélectionné n\'est plus disponible.');

            return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
        }

        $detailExistant = null;

        foreach ($commande->getDetails() as $detail) {
            if ($detail->getProduit()?->getId()=== $produit->getId()
            ) {$detailExistant = $detail;
                break;
            }
        }

        if ($detailExistant !== null) {$detailExistant->setDetailQuantite(($detailExistant->getDetailQuantite() ?? 0)+ $quantite);

            $this->updateDetailTotal($detailExistant);
        } else {
            $detail = new Detail();
            $detail->setCommande($commande);
            $detail->setProduit($produit);
            $detail->setMenu(null);
            $detail->setDetailQuantite($quantite);
            $detail->setDetailUnitaire(number_format((float) $produit->getProduitPrix(),2,'.',''));

            $this->updateDetailTotal($detail);

            $commande->addDetail($detail);
            $entityManager->persist($detail);
        }

        $this->recalculateCommandeTotal($commande);
        $entityManager->flush();

        $this->addFlash('success','Le produit a été ajouté à votre commande.');

        return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
    }

    #[Route('/{id}/menu/ajouter',name: 'app_front_commande_add_menu',methods: ['POST'])]
    
// Ajoute un menu à une commande existante.
    
    public function addMenu(Request $request,Commande $commande,MenuRepository $menuRepository,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        if (!$this->isCsrfTokenValid('front_commande_add_menu'. $commande->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $menuId = $request->getPayload()->getInt('menu_id');
        $quantite = max(1,$request->getPayload()->getInt('quantite',1));
        $menu = $menuRepository->find($menuId);

        if ($menu === null|| !$this->isMenuDisponible($menu)) {
            $this->addFlash('warning','Le menu sélectionné n\'est plus disponible.');

            return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
        }

        $detailExistant = null;

        foreach ($commande->getDetails() as $detail) {
            if ($detail->getMenu()?->getId()=== $menu->getId()) {$detailExistant = $detail;
                break;
            }
        }

        if ($detailExistant !== null) {
            $detailExistant->setDetailQuantite(($detailExistant->getDetailQuantite() ?? 0)+ $quantite);

            $this->updateDetailTotal($detailExistant);
        } else {
            $detail = new Detail();
            $detail->setCommande($commande);
            $detail->setProduit(null);
            $detail->setMenu($menu);
            $detail->setDetailQuantite($quantite);
            $detail->setDetailUnitaire(number_format((float) $menu->getMenuPrix(),2,'.',''));

            $this->updateDetailTotal($detail);

            $commande->addDetail($detail);
            $entityManager->persist($detail);
        }

        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        $this->addFlash('success','Le menu a été ajouté à votre commande.');

        return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
    }

    #[Route('/{id}/detail/{detailId}/plus',name: 'app_front_commande_detail_plus',methods: ['POST'])]
    
// Augmente la quantité d'un article.
    
    public function increaseDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('front_commande_detail_plus'. $detail->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isDetailDisponible($detail)) {$this->addFlash('warning','Cet article n\'est plus disponible.');

            return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
        }

        $detail->setDetailQuantite(($detail->getDetailQuantite() ?? 0)+ 1
        );

        $this->updateDetailTotal($detail);
        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
    }

    #[Route('/{id}/detail/{detailId}/moins',name: 'app_front_commande_detail_minus',methods: ['POST'])]
    
// Diminue la quantité d'un article.
    
    public function decreaseDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('front_commande_detail_minus'. $detail->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if (($detail->getDetailQuantite() ?? 0) > 1) {$detail->setDetailQuantite($detail->getDetailQuantite()- 1);

            $this->updateDetailTotal($detail);
            $this->recalculateCommandeTotal($commande);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
    }

    #[Route('/{id}/detail/{detailId}/supprimer',name: 'app_front_commande_detail_delete',methods: ['POST'])]
    
// Retire un article de la commande.
    
    public function deleteDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->canModify($commande)) {$this->addModificationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('front_commande_detail_delete'. $detail->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $commande->removeDetail($detail);
        $entityManager->remove($detail);

        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        $this->addFlash('success','L\'article a été retiré de votre commande.');

        return $this->redirectToRoute('app_front_commande_edit',['id' => $commande->getId(),]);
    }

// Annule une commande encore annulable.
    #[Route('/{id}/annuler',name: 'app_front_commande_cancel',methods: ['POST'])]
    public function cancel(Request $request,Commande $commande,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->isCsrfTokenValid('front_commande_cancel'. $commande->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->canCancel($commande)) {$this->addCancellationDeniedFlash($commande);

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        $commande->setCommandeStatut('annulée');
        $entityManager->flush();

        $this->addFlash('success','Votre commande a été annulée.');

        return $this->redirectToRoute('app_front_commande',[],Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/relancer',name: 'app_front_commande_relaunch',methods: ['POST'])]
    
// Crée une nouvelle commande à partir d'une commande annulée.
    
    public function relaunch(Request $request,Commande $commande,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->isCsrfTokenValid('front_commande_relaunch'. $commande->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($commande->getCommandeStatut()!== 'annulée') {
            $this->addFlash('warning','Seule une commande annulée peut être relancée.');

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        if ($commande->getDetails()->isEmpty()) {$this->addFlash('warning','Cette commande ne contient aucun article et ne peut pas être relancée.');

            return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        foreach ($commande->getDetails() as $detail) {
            if ($detail->getProduit() === null&& $detail->getMenu() === null) {$this->addFlash('warning','Cette commande contient un article qui n\'existe plus et ne peut pas être relancée.');

                return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
            }

            if (!$this->isDetailDisponible($detail)) {$this->addFlash('warning','Un article de cette commande n\'est plus disponible. La commande ne peut pas être relancée.');

                return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
            }
        }

        $utilisateur =$this->getCurrentUtilisateur();

        if ($utilisateur === null) {throw $this->createAccessDeniedException();}

        $nouvelleCommande = new Commande();
        $nouvelleCommande->setCommandeDate(new \DateTime());
        $nouvelleCommande->setCommandeStatut('en attente');
        $nouvelleCommande->setUtilisateur($utilisateur);
        $nouvelleCommande->setCommandeTotal('0.00');
        $entityManager->persist($nouvelleCommande);
        $totalCommande = 0.0;

        foreach ($commande->getDetails()as $ancienDetail) {
            $quantite = max(1,(int) $ancienDetail->getDetailQuantite());
            $nouveauDetail =new Detail();
            $nouveauDetail->setCommande($nouvelleCommande);

            if ($ancienDetail->getProduit()!== null) {
                $produit =$ancienDetail->getProduit();
                $prixUnitaire =(float) $produit->getProduitPrix();
                $nouveauDetail->setProduit($produit);
                $nouveauDetail->setMenu(null);
            } else {
                $menu =$ancienDetail->getMenu();

            if ($menu === null) {continue;
            }
                $prixUnitaire =(float) $menu->getMenuPrix();
                $nouveauDetail->setProduit(null);
                $nouveauDetail->setMenu($menu);
            }

            $totalLigne =$prixUnitaire * $quantite;
            $nouveauDetail->setDetailQuantite($quantite);
            $nouveauDetail->setDetailUnitaire(number_format($prixUnitaire,2,'.',''));
            $nouveauDetail->setDetailTotal(number_format($totalLigne,2,'.',''));
            $nouvelleCommande->addDetail($nouveauDetail);
            $entityManager->persist($nouveauDetail);
            $totalCommande +=$totalLigne;
        }

        $nouvelleCommande->setCommandeTotal(number_format($totalCommande,2,'.',''));
        $entityManager->flush();
        $this->addFlash('success','Votre commande a été relancée sous le numéro #'. $nouvelleCommande->getId(). '.');

        return $this->redirectToRoute('app_front_commande_show',['id' =>$nouvelleCommande->getId(),],Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/supprimer',name: 'app_front_commande_delete',methods: ['POST'])]
    
// Supprime définitivement une commande annulée.

    public function delete(Request $request,Commande $commande,EntityManagerInterface $entityManager): Response {
        $this->assertCommandeOwner($commande);

        if (!$this->isCsrfTokenValid('front_commande_delete'. $commande->getId(),$request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($commande->getCommandeStatut()!== 'annulée') {$this->addFlash('warning','Seule une commande annulée peut être supprimée.');

        return $this->redirectToRoute('app_front_commande_show',['id' => $commande->getId(),]);
        }

        foreach ($commande->getDetails()->toArray()as $detail) {$entityManager->remove($detail);
        }

        $entityManager->remove($commande);
        $entityManager->flush();

        $this->addFlash('success','La commande a été supprimée définitivement.');

        return $this->redirectToRoute('app_front_commande',[],Response::HTTP_SEE_OTHER);
    }

// Récupère l'utilisateur actuellement connecté.

    private function getCurrentUtilisateur(): ?Utilisateur{$utilisateur = $this->getUser();

        return $utilisateur instanceof Utilisateur? $utilisateur: null;
    }

// Vérifie que la commande appartient au client connecté.

    private function assertCommandeOwner(Commande $commande): void {
        $utilisateur =$this->getCurrentUtilisateur();

        if ($utilisateur === null) {throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($commande->getUtilisateur()?->getId()!== $utilisateur->getId()) {throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette commande.');
        }
    }

// Vérifie si la commande peut encore être modifiée.

    private function canModify(Commande $commande): bool {
        return in_array($commande->getCommandeStatut(),['en attente','en préparation',],true);
    }

// Vérifie si la commande peut encore être annulée.

    private function canCancel(Commande $commande): bool {
        return in_array($commande->getCommandeStatut(),['en attente','en préparation','prête',],true);
    }

// Affiche le message correspondant à une modification refusée.

    private function addModificationDeniedFlash(Commande $commande): void {
        $statut =$commande->getCommandeStatut();

        if ($statut === 'prête') {
            $message =
                'Cette commande est déjà prête et ne peut plus être modifiée. Vous pouvez encore l\'annuler.';
        } elseif (
            $statut === 'en livraison'
        ) {
            $message =
                'Cette commande est déjà en cours de livraison et ne peut plus être modifiée.';
        } elseif (
            $statut === 'livrée'
        ) {
            $message =
                'Cette commande a déjà été livrée et ne peut plus être modifiée.';
        } elseif (
            $statut === 'annulée'
        ) {
            $message =
                'Cette commande est annulée et ne peut plus être modifiée.';
        } else {
            $message =
                'Cette commande ne peut plus être modifiée.';
        }

        $this->addFlash('warning',$message);
    }

// Affiche le message correspondant à une annulation refusée.

    private function addCancellationDeniedFlash(Commande $commande): void {$statut =$commande->getCommandeStatut();

        if (
            $statut === 'en livraison'
        ) {
            $message =
                'Cette commande est déjà en cours de livraison et ne peut plus être annulée.';
        } elseif (
            $statut === 'livrée'
        ) {
            $message =
                'Cette commande a déjà été livrée et ne peut plus être annulée.';
        } elseif (
            $statut === 'annulée'
        ) {
            $message =
                'Cette commande est déjà annulée.';
        } else {
            $message =
                'Cette commande ne peut plus être annulée.';
        }

        $this->addFlash('warning',$message);
    }

// Recherche une ligne appartenant à la commande.

    private function findDetail(Commande $commande,int $detailId,EntityManagerInterface $entityManager): Detail {
        $detail = $entityManager->getRepository(Detail::class)->find($detailId);

        if ($detail === null|| $detail->getCommande()?->getId()!== $commande->getId()) {
            throw $this->createNotFoundException('Cette ligne de commande n\'existe pas.');
        }

        return $detail;
    }

// Recalcule le total d'une ligne de commande.

    private function updateDetailTotal(Detail $detail): void {$prixUnitaire =(float) $detail->getDetailUnitaire();

        $quantite =$detail->getDetailQuantite()?? 0;
        $detail->setDetailTotal(number_format($prixUnitaire * $quantite,2,'.',''));
    }

// Recalcule le montant total de la commande.

    private function recalculateCommandeTotal(Commande $commande): void {$total = 0.0;

        foreach ($commande->getDetails()as $detail) {
            if ($detail->getProduit() === null&& $detail->getMenu() === null) {
                continue;
            }

            $this->updateDetailTotal($detail);

            $total +=(float) $detail->getDetailTotal();
        }

        $commande->setCommandeTotal(number_format($total,2,'.',''));
    }

// Vérifie que l'article d'une ligne est encore disponible.

    private function isDetailDisponible(Detail $detail): bool {
        if ($detail->getProduit() !== null) {
            return $detail->getProduit()->isProduitStock();
        }

        if ($detail->getMenu() !== null) {
            return $this->isMenuDisponible($detail->getMenu());
        }

        return false;
    }

// Vérifie qu'un menu et tous ses produits sont disponibles.

    private function isMenuDisponible(Menu $menu): bool {
        if (!$menu->isMenuStatus()) {return false;}
        if ($menu->getProduits()->isEmpty()) {return false;}
        foreach ($menu->getProduits()as $produit) {
            if (!$produit->isProduitStock()) {return false;}
        }
        return true;
    }
}