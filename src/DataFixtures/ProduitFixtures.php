<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
// ====================================================== //
// ===================== CONSTANTES ====================== //
// ====================================================== //

// Références permettant d'utiliser les produits dans DetailFixtures.

    public const WACFAT_REFERENCE = 'produit-wacfat';
    public const TOMMEAWAC_REFERENCE = 'produit-tommeawac';
    public const WACOCO_REFERENCE = 'produit-wacoco';
    public const EAU_REFERENCE = 'produit-eau';
    public const WACADELE_REFERENCE = 'produit-wacadele';
    public const WACADAMIA_REFERENCE = 'produit-wacadamia';

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

// Crée les produits et les associe à leurs catégories.

    public function load(ObjectManager $manager): void
    {

// ======================= BURGERS ======================= //

// Crée le produit WacFat.

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

// Enregistre les références utilisées par les autres Fixtures.

        $this->addReference(self::WACFAT_REFERENCE, $wacfat);

// Crée le produit Tommeawac.

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

        $this->addReference(self::TOMMEAWAC_REFERENCE, $tommeawac);

// ====================== BOISSONS ======================= //

// Crée le produit Wacoco.

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
        $manager->persist($wacoco);

        $this->addReference(self::WACOCO_REFERENCE, $wacoco);

// Crée le produit Eau.

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

        $this->addReference(self::EAU_REFERENCE, $eau);

// =================== ACCOMPAGNEMENTS =================== //

// Crée le produit Wacadele.

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

        $this->addReference(self::WACADELE_REFERENCE, $wacadele);

// ====================== DESSERTS ======================= //

// Crée le produit Wacadamia.

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

        $this->addReference(self::WACADAMIA_REFERENCE, $wacadamia);

// Enregistre tous les produits dans la base de données.

        $manager->flush();
    }

// Indique que les catégories doivent être créées avant les produits.

    public function getDependencies(): array
    {
        return [
            CategorieFixtures::class,
        ];
    }
}