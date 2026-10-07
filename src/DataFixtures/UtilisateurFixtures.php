<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class UtilisateurFixtures extends Fixture
{

// ====================================================== //
// ===================== CONSTANTES ====================== //
// ====================================================== //

// Références permettant d'identifier les utilisateurs dans les autres Fixtures.

    public const ADMIN_REFERENCE = 'utilisateur-admin';
    public const PREPARATEUR_REFERENCE = 'utilisateur-preparateur';
    public const ACCUEIL_REFERENCE = 'utilisateur-accueil';

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

// Crée les utilisateurs nécessaires au fonctionnement de l'application.

    public function load(ObjectManager $manager): void
    {

        // Crée le compte administrateur.
        $administrateur = new Utilisateur();
        $administrateur->setUtilisateurNom('Administrateur');
        $administrateur->setUtilisateurPrenom('Wacdo');
        $administrateur->setUtilisateurEmail('admin@wacdo.fr');
        $administrateur->setUtilisateurPassword(password_hash('Admin123!', PASSWORD_BCRYPT));
        $administrateur->setUtilisateurRole('ROLE_ADMIN');
        $manager->persist($administrateur);

        // Enregistre les références utilisées dans les autres Fixtures.
        $this->addReference(self::ADMIN_REFERENCE, $administrateur);

        // Crée le compte préparateur.
        $preparateur = new Utilisateur();
        $preparateur->setUtilisateurNom('Preparateur');
        $preparateur->setUtilisateurPrenom('Wacdo');
        $preparateur->setUtilisateurEmail('preparateur@wacdo.fr');
        $preparateur->setUtilisateurPassword(password_hash('Preparateur123!', PASSWORD_BCRYPT));
        $preparateur->setUtilisateurRole('ROLE_PREPARATEUR');
        $manager->persist($preparateur);
        $this->addReference(self::PREPARATEUR_REFERENCE, $preparateur);

        // Crée le compte accueil.
        $accueil = new Utilisateur();
        $accueil->setUtilisateurNom('Accueil');
        $accueil->setUtilisateurPrenom('Wacdo');
        $accueil->setUtilisateurEmail('accueil@wacdo.fr');
        $accueil->setUtilisateurPassword(password_hash('accueil123!', PASSWORD_BCRYPT));
        $accueil->setUtilisateurRole('ROLE_ACCUEIL');
        $manager->persist($accueil);
        $this->addReference(self::ACCUEIL_REFERENCE, $accueil);

        // Enregistre tous les utilisateurs dans la base de données.
        $manager->flush();
    }
}
