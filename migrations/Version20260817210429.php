<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817210429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, menu_nom VARCHAR(255) NOT NULL, menu_description LONGTEXT DEFAULT NULL, menu_prix NUMERIC(10, 2) NOT NULL, menu_status TINYINT NOT NULL, menu_image VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_produit (menu_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_A17EF199CCD7E912 (menu_id), INDEX IDX_A17EF199F347EFB (produit_id), PRIMARY KEY (menu_id, produit_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE menu_produit ADD CONSTRAINT FK_A17EF199CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_produit ADD CONSTRAINT FK_A17EF199F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE detail ADD menu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE detail ADD CONSTRAINT FK_2E067F93CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('CREATE INDEX IDX_2E067F93CCD7E912 ON detail (menu_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_produit DROP FOREIGN KEY FK_A17EF199CCD7E912');
        $this->addSql('ALTER TABLE menu_produit DROP FOREIGN KEY FK_A17EF199F347EFB');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_produit');
        $this->addSql('ALTER TABLE detail DROP FOREIGN KEY FK_2E067F93CCD7E912');
        $this->addSql('DROP INDEX IDX_2E067F93CCD7E912 ON detail');
        $this->addSql('ALTER TABLE detail DROP menu_id');
    }
}
