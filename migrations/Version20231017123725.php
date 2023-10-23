<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231017123725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recap_details (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, quantity INT NOT NULL, produit VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_1D1FD6982EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recap_details ADD CONSTRAINT FK_1D1FD6982EA2E54 FOREIGN KEY (commande_id) REFERENCES `commandes` (id)');
        $this->addSql('ALTER TABLE adresses CHANGE code code_postal VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recap_details DROP FOREIGN KEY FK_1D1FD6982EA2E54');
        $this->addSql('DROP TABLE recap_details');
        $this->addSql('ALTER TABLE `adresses` CHANGE code_postal code VARCHAR(255) NOT NULL');
    }
}
