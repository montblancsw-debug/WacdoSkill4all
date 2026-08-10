<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commande')]
final class AdminCommandeController extends AbstractController
{
    // Affiche la liste des commandes.
    #[Route(name: 'app_admin_commande_index', methods: ['GET'])]
    public function index(
        CommandeRepository $commandeRepository
    ): Response {
        return $this->render('admin_commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy([], [
                'id' => 'DESC',
            ]),
        ]);
    }

    // Permet de créer une nouvelle commande.
    #[Route('/new', name: 'app_admin_commande_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée une nouvelle commande.
        $commande = new Commande();

        // Initialise la date avec la date actuelle.
        $commande->setCommandeDate(new \DateTime());

        // Crée le formulaire de la commande.
        $form = $this->createForm(
            CommandeType::class,
            $commande
        );

        // Récupère les données envoyées.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Prépare l'enregistrement de la commande.
            $entityManager->persist($commande);

            // Enregistre la commande en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'La commande a été ajoutée avec succès.'
            );

            // Retourne vers la liste des commandes.
            return $this->redirectToRoute(
                'app_admin_commande_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de création.
        return $this->render('admin_commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    // Affiche le détail d'une commande.
    #[Route('/{id}', name: 'app_admin_commande_show', methods: ['GET'])]
    public function show(
        Commande $commande
    ): Response {
        return $this->render('admin_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    // Permet de modifier une commande.
    #[Route('/{id}/edit', name: 'app_admin_commande_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        // Crée le formulaire avec la commande existante.
        $form = $this->createForm(
            CommandeType::class,
            $commande
        );

        // Récupère les nouvelles données.
        $form->handleRequest($request);

        // Vérifie que le formulaire est valide.
        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistre les modifications.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'La commande a été modifiée avec succès.'
            );

            // Retourne vers la liste des commandes.
            return $this->redirectToRoute(
                'app_admin_commande_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        // Affiche le formulaire de modification.
        return $this->render('admin_commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    // Supprime uniquement la commande sélectionnée.
    #[Route('/{id}', name: 'app_admin_commande_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifie le jeton CSRF.
        if (
            $this->isCsrfTokenValid(
                'delete' . $commande->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            // Supprime les détails appartenant à cette commande.
            foreach ($commande->getDetails() as $detail) {
                $entityManager->remove($detail);
            }

            // Supprime uniquement la commande sélectionnée.
            $entityManager->remove($commande);

            // Exécute les suppressions en base.
            $entityManager->flush();

            // Affiche un message de confirmation.
            $this->addFlash(
                'success',
                'La commande a été supprimée avec succès.'
            );
        } else {
            // Affiche une erreur si le jeton est invalide.
            $this->addFlash(
                'danger',
                'La suppression de la commande a échoué.'
            );
        }

        // Retourne vers la liste des commandes.
        return $this->redirectToRoute(
            'app_admin_commande_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}