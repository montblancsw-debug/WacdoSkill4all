<?php

namespace App\Tests\Entity;

use App\Entity\Commande;
use App\Entity\Detail;
use PHPUnit\Framework\TestCase;

final class CommandeTest extends TestCase
{
    // Vérifie qu'une commande en attente peut être modifiée et annulée.
    public function testCommandeEnAttenteIsModifiableAndAnnulable(): void
    {
        $commande = new Commande();
        $commande->setCommandeStatut('en attente');
        self::assertTrue($commande->isModifiableByClient());
        self::assertTrue($commande->isAnnulableByClient());
    }

    // Vérifie qu'une commande prête ne peut plus être modifiée.
    public function testCommandePreteIsNotModifiableButCanBeCancelled(): void
    {
        $commande = new Commande();
        $commande->setCommandeStatut('prête');
        self::assertFalse($commande->isModifiableByClient());
        self::assertTrue($commande->isAnnulableByClient());
    }

    // Vérifie qu'une commande livrée ne peut plus être modifiée ou annulée.
    public function testCommandeLivreeIsNotModifiableOrAnnulable(): void
    {
        $commande = new Commande();
        $commande->setCommandeStatut('livrée');
        self::assertFalse($commande->isModifiableByClient());
        self::assertFalse($commande->isAnnulableByClient());
    }

    // Vérifie qu'un détail peut être ajouté à une commande.
    public function testCommandeCanContainDetails(): void
    {
        $commande = new Commande();
        $detail = new Detail();
        $commande->addDetail($detail);
        self::assertCount(1, $commande->getDetails());
        self::assertSame($commande, $detail->getCommande());
    }

    // Vérifie qu'un détail peut être retiré d'une commande.
    public function testCommandeCanRemoveDetail(): void
    {
        $commande = new Commande();
        $detail = new Detail();
        $commande->addDetail($detail);
        $commande->removeDetail($detail);
        self::assertCount(0, $commande->getDetails());
        self::assertNull($detail->getCommande());
    }
}