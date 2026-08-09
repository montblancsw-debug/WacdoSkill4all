<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DetailFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /*
         * Création des lignes de détail des commandes.
         * Chaque détail associe un produit à une commande
         * avec une quantité et le prix unitaire au moment de l'achat.
         */


        // ==================================================
        // COMMANDE 1 : 2 Wacfat + 1 Wacoco = 19,50 €
        // ==================================================

        $detail1 = new Detail();
        $detail1->setDetailQuantite(2);
        $detail1->setDetailUnitaire('8.50');
        $detail1->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_1_REFERENCE,
                Commande::class
            )
        );
        $detail1->setProduit(
            $this->getReference(
                ProduitFixtures::WACFAT_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail1);


        $detail2 = new Detail();
        $detail2->setDetailQuantite(1);
        $detail2->setDetailUnitaire('2.50');
        $detail2->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_1_REFERENCE,
                Commande::class
            )
        );
        $detail2->setProduit(
            $this->getReference(
                ProduitFixtures::WACOCO_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail2);


        // ==================================================
        // COMMANDE 2 : 2 Wacadele + 2 Eaux = 9,00 €
        // ==================================================

        $detail3 = new Detail();
        $detail3->setDetailQuantite(2);
        $detail3->setDetailUnitaire('3.00');
        $detail3->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_2_REFERENCE,
                Commande::class
            )
        );
        $detail3->setProduit(
            $this->getReference(
                ProduitFixtures::WACADELE_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail3);


        $detail4 = new Detail();
        $detail4->setDetailQuantite(2);
        $detail4->setDetailUnitaire('1.50');
        $detail4->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_2_REFERENCE,
                Commande::class
            )
        );
        $detail4->setProduit(
            $this->getReference(
                ProduitFixtures::EAU_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail4);


        // ==================================================
        // COMMANDE 3 : 1 Tommeawac + 1 Wacadamia = 10,00 €
        // ==================================================

        $detail5 = new Detail();
        $detail5->setDetailQuantite(1);
        $detail5->setDetailUnitaire('6.50');
        $detail5->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_3_REFERENCE,
                Commande::class
            )
        );
        $detail5->setProduit(
            $this->getReference(
                ProduitFixtures::TOMMEAWAC_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail5);


        $detail6 = new Detail();
        $detail6->setDetailQuantite(1);
        $detail6->setDetailUnitaire('3.50');
        $detail6->setCommande(
            $this->getReference(
                CommandeFixtures::COMMANDE_3_REFERENCE,
                Commande::class
            )
        );
        $detail6->setProduit(
            $this->getReference(
                ProduitFixtures::WACADAMIA_REFERENCE,
                Produit::class
            )
        );

        $manager->persist($detail6);


        // Enregistrement des lignes de détail dans la base de données.
        $manager->flush();
    }


    /*
     * Les produits et les commandes doivent être créés
     * avant de pouvoir générer leurs lignes de détail.
     */
    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class,
            CommandeFixtures::class,
        ];
    }
}