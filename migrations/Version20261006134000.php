<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261006134000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la date et l heure prévues de livraison aux commandes.';
    }

    public function up(Schema $schema): void
    {// this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD commande_horaire_livraison DATETIME DEFAULT NULL');
        $this->addSql('UPDATE commande SET commande_horaire_livraison = DATE_ADD(commande_date, INTERVAL 30 MINUTE) WHERE commande_horaire_livraison IS NULL');
        $this->addSql('ALTER TABLE commande CHANGE commande_horaire_livraison commande_horaire_livraison DATETIME NOT NULL');
    }
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP commande_horaire_livraison');
    }
}