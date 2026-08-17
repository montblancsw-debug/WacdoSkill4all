<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontMenuController extends AbstractController
{
    #[Route('/menus', name: 'app_front_menu', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('front_menu/index.html.twig', [
            'menus' => $menuRepository->findActive(),
        ]);
    }

    #[Route('/menus/{id}', name: 'app_front_menu_detail', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function detail(int $id, MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->findOneActive($id);

        if ($menu === null) {
            throw $this->createNotFoundException('Menu introuvable.');
        }

        return $this->render('front_menu/detail.html.twig', ['menu' => $menu]);
    }
}