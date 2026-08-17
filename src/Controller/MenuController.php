<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Produit;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/menus', name: 'app_api_menu_')]
final class MenuController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): JsonResponse
    {
        $menus = array_map(
            fn (Menu $menu): array => $this->normalizeMenu($menu),
            $menuRepository->findActive()
        );

        return $this->json($menus);
    }

    #[Route(
        '/{id}',
        name: 'show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(
        int $id,
        MenuRepository $menuRepository
    ): JsonResponse {
        $menu = $menuRepository->findOneActive($id);

        if ($menu === null) {
            return $this->json(
                ['message' => 'Menu introuvable.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return $this->json($this->normalizeMenu($menu));
    }

    private function normalizeMenu(Menu $menu): array
    {
        return [
            'id' => $menu->getId(),
            'nom' => $menu->getMenuNom(),
            'description' => $menu->getMenuDescription(),
            'prix' => $menu->getMenuPrix(),
            'status' => $menu->isMenuStatus(),
            'image' => $menu->getMenuImage() !== null
                ? '/images/menus/' . $menu->getMenuImage()
                : null,
            'produits' => array_values(
                $menu->getProduits()
                    ->map(
                        static fn (Produit $produit): array => [
                            'id' => $produit->getId(),
                            'nom' => $produit->getProduitNom(),
                            'prix' => $produit->getProduitPrix(),
                            'disponible' => $produit->isProduitStock(),
                        ]
                    )
                    ->toArray()
            ),
        ];
    }
}