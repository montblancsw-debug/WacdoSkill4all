<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontHomeController extends AbstractController
{
    #[Route('/', name: 'app_front_home')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('front_home/index.html.twig', [
            'variable' => 'jean',
        ]);
    }
}
