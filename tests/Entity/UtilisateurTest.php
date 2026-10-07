<?php

namespace App\Tests\Entity;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

final class UtilisateurTest extends TestCase
{
    // Vérifie que l'adresse email est utilisée comme identifiant.

    public function testEmailIsUsedAsUserIdentifier(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setUtilisateurEmail('test@wacdo.fr');
        self::assertSame('test@wacdo.fr',$utilisateur->getUserIdentifier());
    }

    // Vérifie que le rôle de l'utilisateur est correctement retourné.
    public function testUtilisateurReturnsItsRole(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setUtilisateurRole('ROLE_USER');

        self::assertSame(['ROLE_USER'],$utilisateur->getRoles());
    }

    // Vérifie qu'une commande peut être associée à un utilisateur.
    public function testUtilisateurCanContainCommande(): void
    {
        $utilisateur = new Utilisateur();
        $commande = new Commande();
        $utilisateur->addCommande($commande);

        self::assertCount(1, $utilisateur->getCommandes());
        self::assertSame($utilisateur, $commande->getUtilisateur());
    }

    // Vérifie qu'une commande peut être retirée d'un utilisateur.
    public function testUtilisateurCanRemoveCommande(): void
    {
        $utilisateur = new Utilisateur();
        $commande = new Commande();
        $utilisateur->addCommande($commande);
        $utilisateur->removeCommande($commande);

        self::assertCount(0, $utilisateur->getCommandes());
        self::assertNull($commande->getUtilisateur());
    }
}