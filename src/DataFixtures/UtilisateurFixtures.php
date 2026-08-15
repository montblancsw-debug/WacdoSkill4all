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
    public const ACCUEIL_REFERENCE = 'utilisateur-accueil';

    public function load(ObjectManager $manager): void
    {
        // ====================================================== //
        // ============= Création des utilisateurs : ============ //
        // ====================================================== //

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

        // 2. Le rôle Préparateur :
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

        // 3. Le rôle Acceuil :
        $accueil = new Utilisateur();
        $accueil->setUtilisateurNom('Accueil');
        $accueil->setUtilisateurPrenom('Wacdo');
        $accueil->setUtilisateurEmail('accueil@wacdo.fr');
        $accueil->setUtilisateurPassword(
            password_hash('accueil123!', PASSWORD_BCRYPT)
        );
        $accueil->setUtilisateurRole('ROLE_ACCUEIL');
        $manager->persist($accueil);
        // Référence utilisée par CommandeFixtures.
        $this->addReference(self::ACCUEIL_REFERENCE, $accueil);

        // Enregistrement des utilisateurs dans la base de données.
        $manager->flush();
    }
}
