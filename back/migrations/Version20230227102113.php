<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230227102113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, maiden_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, note LONGTEXT DEFAULT NULL, is_participated TINYINT(1) NOT NULL, is_public TINYINT(1) NOT NULL, active_years VARCHAR(255) NOT NULL, function VARCHAR(255) DEFAULT NULL, link LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
