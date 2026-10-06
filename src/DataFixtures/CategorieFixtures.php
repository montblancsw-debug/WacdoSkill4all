<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
// ====================================================== //
// ===================== CONSTANTES ====================== //
// ====================================================== //

// public const permettant aux autres Fixtures de récupérer les catégories.

        public const BURGERS_REFERENCE = 'categorie-burgers';
        public const BOISSONS_REFERENCE = 'categorie-boissons';
        public const DESSERTS_REFERENCE = 'categorie-desserts';
        public const ACCOMPAGNEMENTS_REFERENCE = 'categorie-accompagnements';

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

// Crée les catégories utilisées dans l'application.

        public function load(ObjectManager $manager): void
        {

// Crée la catégorie Burgers.

        $burgers = new Categorie();
        $burgers->setCategorieNom('Burgers');
        $burgers->setCategorieDescription('Les burgers proposés par Wacdo.');
        $manager->persist($burgers);

// Enregistre une référence pour utiliser cette catégorie dans les autres Fixtures.
        $this->addReference(self::BURGERS_REFERENCE, $burgers);

// Crée la catégorie Boissons.

        $boissons = new Categorie();
        $boissons->setCategorieNom('Boissons');
        $boissons->setCategorieDescription('Les boissons fraîches proposées par Wacdo.');
        $manager->persist($boissons);

// Enregistre une référence pour utiliser cette catégorie dans les autres Fixtures.
        $this->addReference(self::BOISSONS_REFERENCE, $boissons);

// Crée la catégorie Desserts.

        $desserts = new Categorie();
        $desserts->setCategorieNom('Desserts');
        $desserts->setCategorieDescription('Les desserts proposés par Wacdo.');
        $manager->persist($desserts);

// Enregistre une référence pour utiliser cette catégorie dans les autres Fixtures.
        $this->addReference(self::DESSERTS_REFERENCE, $desserts);

// Crée la catégorie Accompagnements.

        $accompagnements = new Categorie();
        $accompagnements->setCategorieNom('Accompagnements');
        $accompagnements->setCategorieDescription('Les accompagnements proposés avec les menus Wacdo.');
        $manager->persist($accompagnements);

// Enregistre une référence pour utiliser cette catégorie dans les autres Fixtures.
        $this->addReference(self::ACCOMPAGNEMENTS_REFERENCE, $accompagnements);

// Enregistre toutes les catégories dans la base de données.

        $manager->flush();
        }
}