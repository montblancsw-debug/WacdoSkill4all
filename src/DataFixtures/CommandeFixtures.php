<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
class CommandeFixtures extends Fixture implements DependentFixtureInterface
{
    // Références utilisées par DetailFixtures.
    public const COMMANDE_1_REFERENCE = 'commande-1';
    public const COMMANDE_2_REFERENCE = 'commande-2';
    public const COMMANDE_3_REFERENCE = 'commande-3';
    public function load(ObjectManager $manager): void
    {
    // ====================================================== //
    // ===== différents états possibles d'une commande. ===== //
    // ====================================================== //

        // --------------------
        // COMMANDE EN ATTENTE
        // --------------------
        $commande1 = new Commande();
        $commande1->setCommandeStatut('en attente');
        $commande1->setCommandeDate(new \DateTime('-30 minutes'));
        $commande1->setCommandeTotal('19.50');
        $commande1->setUtilisateur(
            $this->getReference(
                UtilisateurFixtures::ACCUEIL_REFERENCE,
                Utilisateur::class
            )
        );
        $manager->persist($commande1);
        // Référence vers le premier statut.
        $this->addReference(self::COMMANDE_1_REFERENCE, $commande1);

        // --------------------
        // COMMANDE PRÉPARÉE
        // --------------------
        $commande2 = new Commande();
        $commande2->setCommandeStatut('préparée');
        $commande2->setCommandeDate(new \DateTime('-20 minutes'));
        $commande2->setCommandeTotal('9.00');
        $commande2->setUtilisateur(
            $this->getReference(
                UtilisateurFixtures::ACCUEIL_REFERENCE,
                Utilisateur::class
            )
        );
        $manager->persist($commande2);
        // Référence vers le second statut.
        $this->addReference(self::COMMANDE_2_REFERENCE, $commande2);

        // --------------------
        // COMMANDE LIVRÉE
        // --------------------
        $commande3 = new Commande();
        $commande3->setCommandeStatut('livrée');
        $commande3->setCommandeDate(new \DateTime('-10 minutes'));
        $commande3->setCommandeTotal('10.00');
        $commande3->setUtilisateur(
            $this->getReference(
                UtilisateurFixtures::ACCUEIL_REFERENCE,
                Utilisateur::class
            )
        );
        $manager->persist($commande3);
        // Référence vers le 3eme statut.
        $this->addReference(self::COMMANDE_3_REFERENCE, $commande3);

        // Enregistrement des commandes dans la base de données.
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
        ];
    }
}