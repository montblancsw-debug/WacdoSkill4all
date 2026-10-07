<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commande')]
final class AdminCommandeController extends AbstractController
{

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Affiche et recherche les commandes.

    #[Route(name: 'app_admin_commande_index',methods: ['GET', 'POST'])]

    public function index(CommandeRepository $commandeRepository,Request $request): Response
    {
        $search = trim((string) $request->request->get('search',''));
        $modePreparation = $this->isGranted('ROLE_PREPARATEUR')&& !$this->isGranted('ROLE_ADMIN')&& !$this->isGranted('ROLE_ACCUEIL');

        if ($modePreparation) {
            $commandes = $commandeRepository->findForPreparation($search);
        } elseif ($search !== '') {
            $commandes = $commandeRepository->search($search);
        } else {
            $commandes = $commandeRepository->findBy([],['commande_date' => 'DESC','id' => 'DESC',]);
        }

        return $this->render('admin_commande/index.html.twig',
            [
                'commandes' => $commandes,
                'search' => $search,
                'modePreparation' => $modePreparation,
            ]
        );
    }

    // Crée une nouvelle commande.

    #[Route('/new',name: 'app_admin_commande_new',methods: ['GET', 'POST'])]

    public function new(Request $request,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();

        $commande = new Commande();
        $commande->setCommandeDate(new \DateTime());
        $commande->setCommandeStatut('en attente');
        $commande->setCommandeTotal('0.00');
        $form = $this->createForm(CommandeType::class,$commande,['status_disabled' => !$this->isGranted('ROLE_ADMIN'),]);
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid())
        {
        $commande->setCommandeTotal('0.00');
        $entityManager->persist($commande);
        $entityManager->flush();
        $this->addFlash('success','La commande a été ajoutée avec succès.');
        return $this->redirectToRoute('app_admin_commande_index',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_commande/new.html.twig',
            [
                'commande' => $commande,
                'form' => $form,
            ]
        );
    }

    // Affiche le détail d'une commande.

    #[Route('/{id}',name: 'app_admin_commande_show',methods: ['GET'])]

    public function show(Commande $commande): Response
    {
        $this->recalculateCommandeTotal($commande);

        return $this->render('admin_commande/show.html.twig',
            [
                'commande' => $commande,
            ]
        );
    }

    // Modifie le statut d'une commande selon le rôle de l'utilisateur.

    #[Route('/{id}/statut',name: 'app_admin_commande_status',methods: ['POST'])]

    public function updateStatus(Request $request,Commande $commande,EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')&& !$this->isGranted('ROLE_PREPARATEUR')&& !$this->isGranted('ROLE_ACCUEIL'))
        {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('commande_status' . $commande->getId(),$request->getPayload()->getString('_token')))
        {
            throw $this->createAccessDeniedException();
        }

        $statut = $request->getPayload()->getString('commande_statut');

        if (!$this->canUpdateStatus($commande,$statut))
        {
            $this->addFlash('danger','Vous ne pouvez pas appliquer ce statut à cette commande.');
            return $this->redirectToRoute('app_admin_commande_show',['id' => $commande->getId(),]);
        }

        $commande->setCommandeStatut($statut);
        $entityManager->flush();

        $this->addFlash('success','Le statut de la commande a été mis à jour.');

        return $this->redirectToRoute('app_admin_commande_show',['id' => $commande->getId(),]);
    }

    // Modifie les informations d'une commande.

    #[Route('/{id}/edit',name: 'app_admin_commande_edit',methods: ['GET', 'POST'])]

    public function edit(Request $request,Commande $commande,ProduitRepository $produitRepository,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();
        $this->recalculateCommandeTotal($commande);

        $form = $this->createForm(CommandeType::class,$commande,['status_disabled' => !$this->isGranted('ROLE_ADMIN'),]);
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid())
        {
            $this->recalculateCommandeTotal($commande);

            $entityManager->flush();
            $this->addFlash('success','La commande a été modifiée avec succès.');

        return $this->redirectToRoute('app_admin_commande_index',[],Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin_commande/edit.html.twig',
        [
            'commande' => $commande,
            'form' => $form,
            'produitsDisponibles' => $produitRepository->findBy(['produit_stock' => true,],['produit_nom' => 'ASC',]),
        ]
        );
    }

    // Ajoute un produit à une commande.

    #[Route('/{id}/produit/ajouter',name: 'app_admin_commande_add_product',methods: ['POST'])]

    public function addProduct(Request $request,Commande $commande,ProduitRepository $produitRepository,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();
        if (!$this->isCsrfTokenValid('commande_add_product' . $commande->getId(),$request->getPayload()->getString('_token')))
        {
            $this->addFlash('danger','L\'ajout du produit a échoué.');
            return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
        }

        $produitId = $request->getPayload()->getInt('produit_id');
        $quantite = max(1,$request->getPayload()->getInt('quantite',1));
        $produit = $produitRepository->find($produitId);

        if ($produit === null|| !$produit->isProduitStock())
        {
            $this->addFlash('danger','Le produit sélectionné n\'est pas disponible.');
            return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
        }

        $detailExistant = null;

        foreach ($commande->getDetails()as $detail)
        {
            if ($detail->getProduit() !== null&& $detail->getProduit()->getId() === $produit->getId())
            {
                $detailExistant = $detail;
                break;
            }
        }

        if ($detailExistant !== null) {$detailExistant->setDetailQuantite(($detailExistant->getDetailQuantite() ?? 0)+ $quantite);

        $this->updateDetailTotal($detailExistant);
        } else
        {
            $detail = new Detail();$detail->setCommande($commande);
            $detail->setProduit($produit);
            $detail->setMenu(null);
            $detail->setDetailQuantite($quantite);
            $detail->setDetailUnitaire($produit->getProduitPrix());

            $this->updateDetailTotal($detail);

            $commande->addDetail($detail);
            $entityManager->persist($detail);
        }

        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        $this->addFlash('success','Le produit a été ajouté à la commande.');

        return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
    }

    // Augmente la quantité d'un article.

    #[Route('/{id}/detail/{detailId}/plus',name: 'app_admin_commande_detail_plus',methods: ['POST'])]

    public function increaseDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('commande_detail_plus' . $detail->getId(),$request->getPayload()->getString('_token')))
        {
            throw $this->createAccessDeniedException();
        }

        $detail->setDetailQuantite(($detail->getDetailQuantite() ?? 0)+ 1);

        $this->updateDetailTotal($detail);
        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
    }

    // Diminue la quantité d'un article.

    #[Route('/{id}/detail/{detailId}/moins',name: 'app_admin_commande_detail_minus',methods: ['POST'])]
    public function decreaseDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('commande_detail_minus' . $detail->getId(),$request->getPayload()->getString('_token')))
        {
            throw $this->createAccessDeniedException();
        }

        if (($detail->getDetailQuantite() ?? 0) > 1)
        {
            $detail->setDetailQuantite($detail->getDetailQuantite()- 1);

            $this->updateDetailTotal($detail);
            $this->recalculateCommandeTotal($commande);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
    }

    // Retire un article de la commande.

    #[Route('/{id}/detail/{detailId}/supprimer',name: 'app_admin_commande_detail_delete',methods: ['POST'])]

    public function deleteDetail(Request $request,Commande $commande,int $detailId,EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessCommandeEditable();

        $detail = $this->findDetail($commande,$detailId,$entityManager);

        if (!$this->isCsrfTokenValid('commande_detail_delete' . $detail->getId(),$request->getPayload()->getString('_token')))
        {
            throw $this->createAccessDeniedException();
        }

        $commande->removeDetail($detail);

        $entityManager->remove($detail);

        $this->recalculateCommandeTotal($commande);

        $entityManager->flush();

        $this->addFlash('success','L\'article a été retiré de la commande.');

        return $this->redirectToRoute('app_admin_commande_edit',['id' => $commande->getId(),]);
    }

    // Supprime définitivement une commande.

    #[Route('/{id}',name: 'app_admin_commande_delete',methods: ['POST'])]

    public function delete(Request $request,Commande $commande,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $commande->getId(),$request->getPayload()->getString('_token')))
        {
            foreach ($commande->getDetails()as $detail) {$entityManager->remove($detail);}

            $entityManager->remove($commande);
            $entityManager->flush();

            $this->addFlash('success','La commande a été supprimée avec succès.');
        } else {
            $this->addFlash('danger','La suppression de la commande a échoué.');
        }

        return $this->redirectToRoute('app_admin_commande_index',[],Response::HTTP_SEE_OTHER);
    }

    // ====================================================== //
    // ================= METHODES PRIVEES ================== //
    // ====================================================== //

    // Vérifie que l'utilisateur peut créer ou modifier une commande.

    private function denyUnlessCommandeEditable(): void
    {
        if (!$this->isGranted('ROLE_ADMIN')&& !$this->isGranted('ROLE_ACCUEIL'))
        {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette commande.');
        }
    }

    // Vérifie si le changement de statut est autorisé.

    private function canUpdateStatus(Commande $commande,string $nouveauStatut): bool
    {
        $statutsExistants =
        [
            'en attente',
            'en préparation',
            'prête',
            'en livraison',
            'livrée',
            'annulée',
        ];

        if (!in_array($nouveauStatut,$statutsExistants,true))
        {
            return false;
        }

        // L'administrateur peut gérer tous les statuts.

        if ($this->isGranted('ROLE_ADMIN'))
        {
            return true;
        }

        // Le préparateur fait progresser la commande jusqu'à l'état prête.

        if ($this->isGranted('ROLE_PREPARATEUR'))
            {
            if ($commande->getCommandeStatut() === 'en attente'&& $nouveauStatut === 'en préparation')
            {
                return true;
            }

            if ($commande->getCommandeStatut() === 'en préparation'&& $nouveauStatut === 'prête')
            {
                return true;
            }

            return false;
        }

        // L'accueil remet au client une commande prête.

        if ($this->isGranted('ROLE_ACCUEIL'))
        {
            return $commande->getCommandeStatut() === 'prête'&& $nouveauStatut === 'livrée';
        }

        return false;
    }

    // Recherche une ligne appartenant à la commande.

    private function findDetail(Commande $commande,int $detailId,EntityManagerInterface $entityManager): Detail
    {
        $detail = $entityManager->getRepository(Detail::class)->find($detailId);

        if ($detail === null|| $detail->getCommande()?->getId() !== $commande->getId())
        {
            throw $this->createNotFoundException('Cette ligne de commande n\'existe pas.');
        }

        return $detail;
    }

    // Recalcule le total d'une ligne de commande.

    private function updateDetailTotal(Detail $detail): void
    {
        $prixUnitaire = (float) $detail->getDetailUnitaire();
        $quantite = $detail->getDetailQuantite() ?? 0;
        $detail->setDetailTotal(number_format($prixUnitaire * $quantite,2,'.',''));
    }

    // Recalcule le montant total de la commande.

    private function recalculateCommandeTotal(Commande $commande): void
    {
        $total = 0.0;

        foreach ($commande->getDetails()as $detail)
        {
            if ($detail->getProduit() === null && $detail->getMenu() === null) {continue;}
            $this->updateDetailTotal($detail);
            $total += (float) $detail->getDetailTotal();
        }

        $commande->setCommandeTotal(number_format($total,2,'.',''));
    }
}