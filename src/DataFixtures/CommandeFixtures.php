<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
class CommandeFixtures extends Fixture implements DependentFixtureInterface
{
// ====================================================== //
// ===================== CONSTANTES ====================== //
// ====================================================== //

// Références utilisées par DetailFixtures.

    public const COMMANDE_1_REFERENCE = 'commande-1';
    public const COMMANDE_2_REFERENCE = 'commande-2';
    public const COMMANDE_3_REFERENCE = 'commande-3';

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

// Crée plusieurs commandes avec différents statuts.

    public function load(ObjectManager $manager): void
    {

// =================== COMMANDE 1 ======================== //

// Crée une commande en attente.

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

// Enregistre les références utilisées dans DetailFixtures.

        $this->addReference(self::COMMANDE_1_REFERENCE, $commande1);

// =================== COMMANDE 2 ======================== //

// Crée une commande préparée.

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

        $this->addReference(self::COMMANDE_2_REFERENCE, $commande2);

// =================== COMMANDE 3 ======================== //

// Crée une commande livrée.

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

        $this->addReference(self::COMMANDE_3_REFERENCE, $commande3);

// Enregistre toutes les commandes dans la base de données.
        $manager->flush();

// Indique que les utilisateurs doivent être créés avant les commandes.

    }
        public function getDependencies(): array{return [UtilisateurFixtures::class,];
    }
}