<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\CategorieRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/menu')]
final class AdminMenuController extends AbstractController
{
    #[Route(
        name: 'app_admin_menu_index',
        methods: ['GET', 'POST']
    )]
    public function index(
        MenuRepository $menuRepository,
        Request $request
    ): Response {
        $search = trim(
            (string) $request
                ->request
                ->get(
                    'search',
                    ''
                )
        );

        $status = trim(
            (string) $request
                ->request
                ->get(
                    'status',
                    ''
                )
        );

        return $this->render(
            'admin_menu/index.html.twig',
            [
                'menus' => $menuRepository->search(
                    $search,
                    $status
                ),
                'search' => $search,
                'status' => $status,
            ]
        );
    }

    #[Route(
        '/new',
        name: 'app_admin_menu_new',
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CategorieRepository $categorieRepository
    ): Response {
        $menu = new Menu();

        $form = $this->createForm(
            MenuType::class,
            $menu
        );

        $form->handleRequest(
            $request
        );

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->persist(
                $menu
            );

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le menu a été ajouté avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_menu_index'
            );
        }

        return $this->render(
            'admin_menu/new.html.twig',
            [
                'menu' => $menu,
                'form' => $form,

                'categories' => $categorieRepository->findBy(
                    [],
                    [
                        'categorie_nom' => 'ASC',
                    ]
                ),
            ]
        );
    }

    #[Route(
        '/{id}',
        name: 'app_admin_menu_show',
        methods: ['GET']
    )]
    public function show(
        Menu $menu
    ): Response {
        return $this->render(
            'admin_menu/show.html.twig',
            [
                'menu' => $menu,
            ]
        );
    }

    #[Route(
        '/{id}/edit',
        name: 'app_admin_menu_edit',
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        Menu $menu,
        EntityManagerInterface $entityManager,
        CategorieRepository $categorieRepository
    ): Response {
        $form = $this->createForm(
            MenuType::class,
            $menu
        );

        $form->handleRequest(
            $request
        );

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le menu a été modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_menu_index'
            );
        }

        return $this->render(
            'admin_menu/edit.html.twig',
            [
                'menu' => $menu,
                'form' => $form,

                'categories' => $categorieRepository->findBy(
                    [],
                    [
                        'categorie_nom' => 'ASC',
                    ]
                ),
            ]
        );
    }

    #[Route(
        '/{id}',
        name: 'app_admin_menu_delete',
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        Menu $menu,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'delete' . $menu->getId(),
                $request
                    ->getPayload()
                    ->getString('_token')
            )
        ) {
            $this->addFlash(
                'danger',
                'La suppression du menu a échoué.'
            );

            return $this->redirectToRoute(
                'app_admin_menu_index'
            );
        }

        if (!$menu->getDetails()->isEmpty()) {
            $this->addFlash(
                'danger',
                'Ce menu est lié à une commande. Désactivez-le au lieu de le supprimer.'
            );

            return $this->redirectToRoute(
                'app_admin_menu_index'
            );
        }

        $entityManager->remove(
            $menu
        );

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le menu a été supprimé avec succès.'
        );

        return $this->redirectToRoute(
            'app_admin_menu_index'
        );
    }
}