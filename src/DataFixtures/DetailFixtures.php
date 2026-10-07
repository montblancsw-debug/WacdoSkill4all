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

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

// Crée les lignes de détail associées aux commandes.
    public function load(ObjectManager $manager): void
    {

// =================== COMMANDE 1 ======================== //

// Ajoute deux WacFat à la première commande.
        $detail1 = new Detail();
        $detail1->setDetailQuantite(2);
        $detail1->setDetailUnitaire('8.50');
        $detail1->setDetailTotal('17.00');
        $detail1->setCommande($this->getReference(CommandeFixtures::COMMANDE_1_REFERENCE,Commande::class));
        $detail1->setProduit($this->getReference(ProduitFixtures::WACFAT_REFERENCE,Produit::class));
        $manager->persist($detail1);

// Ajoute un Wacoco à la première commande.

        $detail2 = new Detail();
        $detail2->setDetailQuantite(1);
        $detail2->setDetailUnitaire('2.50');
        $detail2->setDetailTotal('2.50');
        $detail2->setCommande($this->getReference(CommandeFixtures::COMMANDE_1_REFERENCE,Commande::class));
        $detail2->setProduit($this->getReference(ProduitFixtures::WACOCO_REFERENCE,Produit::class));
        $manager->persist($detail2);

// =================== COMMANDE 2 ======================== //

// Ajoute deux Wacadele à la deuxième commande.

        $detail3 = new Detail();
        $detail3->setDetailQuantite(2);
        $detail3->setDetailUnitaire('3.00');
        $detail3->setDetailTotal('6.00');
        $detail3->setCommande($this->getReference(CommandeFixtures::COMMANDE_2_REFERENCE,Commande::class));
        $detail3->setProduit($this->getReference(ProduitFixtures::WACADELE_REFERENCE,Produit::class));
        $manager->persist($detail3);

// Ajoute deux bouteilles d'eau à la deuxième commande.

        $detail4 = new Detail();
        $detail4->setDetailQuantite(2);
        $detail4->setDetailUnitaire('1.50');
        $detail4->setDetailTotal('3.00');
        $detail4->setCommande($this->getReference(CommandeFixtures::COMMANDE_2_REFERENCE,Commande::class));
        $detail4->setProduit($this->getReference(ProduitFixtures::EAU_REFERENCE,Produit::class));
        $manager->persist($detail4);

// =================== COMMANDE 3 ======================== //

// Ajoute un Tommeawac à la troisième commande.

        $detail5 = new Detail();
        $detail5->setDetailQuantite(1);
        $detail5->setDetailUnitaire('6.50');
        $detail5->setDetailTotal('6.50');
        $detail5->setCommande($this->getReference(CommandeFixtures::COMMANDE_3_REFERENCE,Commande::class));
        $detail5->setProduit($this->getReference(ProduitFixtures::TOMMEAWAC_REFERENCE,Produit::class));
        $manager->persist($detail5);

// Ajoute un Wacadamia à la troisième commande.

        $detail6 = new Detail();
        $detail6->setDetailQuantite(1);
        $detail6->setDetailUnitaire('3.50');
        $detail6->setDetailTotal('3.50');
        $detail6->setCommande($this->getReference(CommandeFixtures::COMMANDE_3_REFERENCE,Commande::class));
        $detail6->setProduit($this->getReference(ProduitFixtures::WACADAMIA_REFERENCE,Produit::class));
        $manager->persist($detail6);

// Enregistre toutes les lignes de détail dans la base de données.
        $manager->flush();
    }

// Indique que les produits et les commandes doivent être créés avant les détails.
    public function getDependencies(): array
    {
        return [ProduitFixtures::class,CommandeFixtures::class,];
    }
}