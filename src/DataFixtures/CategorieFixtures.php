<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    // public const permettant aux autres Fixtures de récupérer les catégories.

    public const BURGERS_REFERENCE = 'categorie-burgers';
    public const BOISSONS_REFERENCE = 'categorie-boissons';
    public const DESSERTS_REFERENCE = 'categorie-desserts';
    public const ACCOMPAGNEMENTS_REFERENCE = 'categorie-accompagnements';
    public function load(ObjectManager $manager): void
    {
        // ====================================================== //
        //     Création des différentes catégories de produits :  //
        // ====================================================== //

        // 1. La catégorie Burgers :
        $burgers = new Categorie();
        $burgers->setCategorieNom('Burgers');
        $burgers->setCategorieDescription('Les burgers proposés par Wacdo.');
        $manager->persist($burgers);

        // Création d'une référence pour l'utiliser dans ProduitFixtures.
        $this->addReference(self::BURGERS_REFERENCE, $burgers);

        // 2. La catégorie Boissons :
        $boissons = new Categorie();
        $boissons->setCategorieNom('Boissons');
        $boissons->setCategorieDescription('Les boissons fraîches proposées par Wacdo.');
        $manager->persist($boissons);

        // Référence vers la catégorie Boissons.
        $this->addReference(self::BOISSONS_REFERENCE, $boissons);

        // 3. La catégorie Desserts :
        $desserts = new Categorie();
        $desserts->setCategorieNom('Desserts');
        $desserts->setCategorieDescription('Les desserts proposés par Wacdo.');
        $manager->persist($desserts);

        // Référence vers la catégorie Desserts.
        $this->addReference(self::DESSERTS_REFERENCE, $desserts);

        // 4. La catégorie Accompagnements :
        $accompagnements = new Categorie();
        $accompagnements->setCategorieNom('Accompagnements');
        $accompagnements->setCategorieDescription('Les accompagnements proposés avec les menus Wacdo.');
        $manager->persist($accompagnements);

        // Référence vers la catégorie Accompagnements.
        $this->addReference(self::ACCOMPAGNEMENTS_REFERENCE, $accompagnements);

        // Enregistrement des catégories dans la base de données.
        $manager->flush();
    }
}