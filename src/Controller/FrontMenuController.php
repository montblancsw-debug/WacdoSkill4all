<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //
final class FrontMenuController extends AbstractController
{
    #[Route('/menus', name: 'app_front_menu', methods: ['GET'])]

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('front_menu/index.html.twig', ['menus' => $menuRepository->findActive(),]);
    }

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //

    #[Route('/menus/{id}', name: 'app_front_menu_detail', requirements: ['id' => '\\d+'], methods: ['GET'])]

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

    public function detail(int $id, MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->findOneActive($id);
        if ($menu === null)
        {
            throw $this->createNotFoundException('Menu introuvable.');
        }
        return $this->render('front_menu/detail.html.twig', ['menu' => $menu]);
    }
}