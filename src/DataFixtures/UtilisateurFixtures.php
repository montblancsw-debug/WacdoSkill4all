<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    // Références permettant d'identifier les utilisateurs dans les autres Fixtures.

    public const ADMIN_REFERENCE = 'utilisateur-admin';
    public const PREPARATEUR_REFERENCE = 'utilisateur-preparateur';
    public const CAISSE_REFERENCE = 'utilisateur-caisse';

    public function load(ObjectManager $manager): void
    {
        // Chaque utilisateur possède un rôle, et Les autorisations des rôles définies sont conforme au cahier des charges.

        // Création des utilisateurs :

        // 1. Le rôle Administrateur :

        $administrateur = new Utilisateur();
        $administrateur->setUtilisateurNom('Administrateur');
        $administrateur->setUtilisateurPrenom('Wacdo');
        $administrateur->setUtilisateurEmail('admin@wacdo.fr');
        $administrateur->setUtilisateurPassword(
            password_hash('Admin123!', PASSWORD_BCRYPT)
        );
        $administrateur->setUtilisateurRole('ROLE_ADMIN');

        $manager->persist($administrateur);

        // Référence utilisée pour retrouver cet administrateur dans d'autres Fixtures.
        $this->addReference(self::ADMIN_REFERENCE, $administrateur);

        // 1. Le rôle Préparateur :

        $preparateur = new Utilisateur();
        $preparateur->setUtilisateurNom('Preparateur');
        $preparateur->setUtilisateurPrenom('Wacdo');
        $preparateur->setUtilisateurEmail('preparateur@wacdo.fr');
        $preparateur->setUtilisateurPassword(
            password_hash('Preparateur123!', PASSWORD_BCRYPT)
        );
        $preparateur->setUtilisateurRole('ROLE_PREPARATEUR');

        $manager->persist($preparateur);

        // Référence vers le compte de préparation.
        $this->addReference(self::PREPARATEUR_REFERENCE, $preparateur);

        // 1. Le rôle Caisse :
        $caisse = new Utilisateur();
        $caisse->setUtilisateurNom('Caisse');
        $caisse->setUtilisateurPrenom('Wacdo');
        $caisse->setUtilisateurEmail('caisse@wacdo.fr');
        $caisse->setUtilisateurPassword(
            password_hash('Caisse123!', PASSWORD_BCRYPT)
        );
        $caisse->setUtilisateurRole('ROLE_CAISSE');

        $manager->persist($caisse);

        // Référence utilisée par CommandeFixtures.
        $this->addReference(self::CAISSE_REFERENCE, $caisse);

        // Enregistrement des utilisateurs dans la base de données.
        $manager->flush();
    }
}
