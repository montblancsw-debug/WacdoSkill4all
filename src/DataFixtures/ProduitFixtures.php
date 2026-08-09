<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    // Références permettant d'utiliser les produits dans DetailFixtures.
    public const WACFAT_REFERENCE = 'produit-wacfat';
    public const TOMMEAWAC_REFERENCE = 'produit-tommeawac';
    public const WACOCO_REFERENCE = 'produit-wacoco';
    public const EAU_REFERENCE = 'produit-eau';
    public const WACADELE_REFERENCE = 'produit-wacadele';
    public const WACADAMIA_REFERENCE = 'produit-wacadamia';

    public function load(ObjectManager $manager): void
    {
        // Chaque produit est obligatoirement associé à une catégorie.

        //Création des produits disponibles :

        // --------------------
        // BURGERS
        // --------------------

        // 1. Le Burger Wacfat :

        $wacfat = new Produit();
        $wacfat->setProduitNom('WacFat');
        $wacfat->setProduitDescription('Un Classique Burger ! Ce Burgers est composé de : salades, tomates, oignons, cornichons, emental et de deux steack hachés');
        $wacfat->setProduitPrix('8.50');
        $wacfat->setProduitStock(true);
        $wacfat->setCategorie(
            $this->getReference(
                CategorieFixtures::BURGERS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($wacfat);

        // Référence vers le Wacfat.
        $this->addReference(self::WACFAT_REFERENCE, $wacfat);

        // 2. Le Burger à la tomme :

        $tommeawac = new Produit();
        $tommeawac->setProduitNom('Tommeawac');
        $tommeawac->setProduitDescription('Attention ça va chauffer ! Ce Burgers est composé de : Tomme Pimenté, salade, tomates, oignons, cornichons, deux steack hachés');
        $tommeawac->setProduitPrix('6.50');
        $tommeawac->setProduitStock(true);
        $tommeawac->setCategorie(
            $this->getReference(
                CategorieFixtures::BURGERS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($tommeawac);

        // Référence vers le Tommeawac.
        $this->addReference(self::TOMMEAWAC_REFERENCE, $tommeawac);

        // --------------------
        // BOISSONS
        // --------------------

        // 1. Le Sodas à la Noix de coco :

        $wacoco = new Produit();
        $wacoco->setProduitNom('Wacoco');
        $wacoco->setProduitDescription('Sodas à la noix de coco.');
        $wacoco->setProduitPrix('2.50');
        $wacoco->setProduitStock(true);
        $wacoco->setCategorie(
            $this->getReference(
                CategorieFixtures::BOISSONS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($locococo);

        // Référence vers le soda Wacoco.
        $this->addReference(self::WACOCO_REFERENCE, $wacoco);


        // 2. Bouteille d'eau Minérales :

        $eau = new Produit();
        $eau->setProduitNom('Eau');
        $eau->setProduitDescription('Bouteille d’eau minérale.');
        $eau->setProduitPrix('1.50');
        $eau->setProduitStock(true);
        $eau->setCategorie(
            $this->getReference(
                CategorieFixtures::BOISSONS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($eau);

        // Référence vers la bouteille d'eau minérales.
        $this->addReference(self::EAU_REFERENCE, $eau);


        // --------------------
        // ACCOMPAGNEMENTS
        // --------------------

        // 1. La frite Wacadele :

        $wacadele = new Produit();
        $wacadele->setProduitNom('Wacadele');
        $wacadele->setProduitDescription('Une Ch’tite Frites ?');
        $wacadele->setProduitPrix('3.00');
        $wacadele->setProduitStock(true);
        $wacadele->setCategorie(
            $this->getReference(
                CategorieFixtures::ACCOMPAGNEMENTS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($wacadele);

        // Référence vers la frite Wacadele.
        $this->addReference(self::WACADELE_REFERENCE, $wacadele);


        // --------------------
        // DESSERTS
        // --------------------

        // 1. Le dessert Wacadamia :

        $wacadamia = new Produit();
        $wacadamia->setProduitNom('Wacadamia');
        $wacadamia->setProduitDescription('Glace wacadamia. Idéal pour une crème glacée onctueuse parsemée d’éclats de noix de macadamia caramélisées');
        $wacadamia->setProduitPrix('3.50');
        $wacadamia->setProduitStock(true);
        $wacadamia->setCategorie(
            $this->getReference(
                CategorieFixtures::DESSERTS_REFERENCE,
                Categorie::class
            )
        );

        $manager->persist($wacadamia);

        // Référence vers le dessert Wacadamia.
        $this->addReference(self::WACADAMIA_REFERENCE, $wacadamia);


        // Enregistrement de l'ensemble des produits.
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorieFixtures::class,
        ];
    }
}