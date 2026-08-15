<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur principal du site (Front-office)
// Gère l'affichage des pages publiques, comme la page d'accueil.
final class FrontHomeController extends AbstractController
{
    #[Route('/', name: 'app_front_home')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('front_home/index.html.twig', [
            'variable' => 'Chez Wacdo',
        ]);
    }
}
