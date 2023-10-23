<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011111238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `series` (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE realisations ADD serie_id INT NOT NULL');
        $this->addSql('ALTER TABLE realisations ADD CONSTRAINT FK_FC5C476DD94388BD FOREIGN KEY (serie_id) REFERENCES `series` (id)');
        $this->addSql('CREATE INDEX IDX_FC5C476DD94388BD ON realisations (serie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `realisations` DROP FOREIGN KEY FK_FC5C476DD94388BD');
        $this->addSql('DROP TABLE `series`');
        $this->addSql('DROP INDEX IDX_FC5C476DD94388BD ON `realisations`');
        $this->addSql('ALTER TABLE `realisations` DROP serie_id');
    }
}
