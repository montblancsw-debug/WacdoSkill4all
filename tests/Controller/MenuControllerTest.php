<?php

namespace App\Tests\Controller;

use App\Entity\Menu;
use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

final class MenuControllerTest extends TestCase
{
    public function testMenuCanContainProducts(): void
    {
        $menu = (new Menu())
            ->setMenuNom('Menu test')
            ->setMenuDescription('Description')
            ->setMenuPrix('12.90')
            ->setMenuStatus(true);

        $produit = (new Produit())
            ->setProduitNom('Produit test')
            ->setProduitPrix('5.00')
            ->setProduitStock(true);

        $menu->addProduit($produit);

        self::assertCount(1, $menu->getProduits());
        self::assertSame($produit, $menu->getProduits()->first());
        self::assertTrue($menu->isMenuStatus());
    }
}