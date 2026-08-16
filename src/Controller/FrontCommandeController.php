<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-commandes')]
class FrontCommandeController extends AbstractController
{
    // Affiche les commandes de l'utilisateur connecté.
    #[Route('', name: 'app_front_commande', methods: ['GET'])]
    public function index(
        CommandeRepository $commandeRepository
    ): Response {
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            return $this->redirectToRoute(
                'app_login'
            );
        }

        return $this->render('front_commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy(
                [
                    'utilisateur' => $utilisateur,
                ],
                [
                    'commande_date' => 'DESC',
                ]
            ),
        ]);
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
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            return $this->redirectToRoute(
                'app_login'
            );
        }

        // Interdit l'accès aux commandes d'un autre utilisateur.
        if (
            $commande->getUtilisateur()?->getId()
            !== $utilisateur->getId()
        ) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas consulter cette commande.'
            );
        }

        return $this->render('front_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}