<?php

namespace App\Tests\Entity;

use App\Entity\Menu;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

final class ProduitTest extends TestCase
{
    // Vérifie les principales informations d'un produit.

    public function testProduitInformations(): void
    {
        $produit = new Produit();

        $produit
            ->setProduitNom('Produit test')
            ->setProduitDescription('Description du produit')
            ->setProduitPrix('5.50')
            ->setProduitStock(true);

        self::assertSame('Produit test', $produit->getProduitNom());
        self::assertSame('Description du produit', $produit->getProduitDescription());
        self::assertSame('5.50', $produit->getProduitPrix());
        self::assertTrue($produit->isProduitStock());
    }

    // Vérifie qu'un produit peut être ajouté à un menu.

    public function testProduitCanBeAddedToMenu(): void
    {
        $produit = new Produit();
        $menu = new Menu();

        $produit->addMenu($menu);

        self::assertCount(1, $produit->getMenus());
        self::assertCount(1, $menu->getProduits());
        self::assertSame($menu, $produit->getMenus()->first());
        self::assertSame($produit, $menu->getProduits()->first());
    }

    // Vérifie qu'un produit peut être retiré d'un menu.

    public function testProduitCanBeRemovedFromMenu(): void
    {
        $produit = new Produit();
        $menu = new Menu();

        $produit->addMenu($menu);
        $produit->removeMenu($menu);

        self::assertCount(0, $produit->getMenus());
        self::assertCount(0, $menu->getProduits());
    }
}