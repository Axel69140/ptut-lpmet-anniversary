<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228085212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492A4DB562');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649825B2E45');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491EBAF6CC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('DROP INDEX IDX_8D93D6491EBAF6CC ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE45BDBF ON user');
        $this->addSql('DROP INDEX IDX_8D93D649825B2E45 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6492A4DB562 ON user');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD is_verified TINYINT(1) NOT NULL, DROP guests_id, DROP activities_id, DROP picture_id, DROP articles_id, CHANGE email email VARCHAR(180) NOT NULL, CHANGE active_years active_years LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE phone_number phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD guests_id INT DEFAULT NULL, ADD activities_id INT DEFAULT NULL, ADD picture_id INT DEFAULT NULL, ADD articles_id INT DEFAULT NULL, DROP roles, DROP is_verified, CHANGE email email VARCHAR(255) NOT NULL, CHANGE active_years active_years VARCHAR(255) NOT NULL, CHANGE phone phone_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492A4DB562 FOREIGN KEY (activities_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649825B2E45 FOREIGN KEY (guests_id) REFERENCES guest (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491EBAF6CC FOREIGN KEY (articles_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491EBAF6CC ON user (articles_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649825B2E45 ON user (guests_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492A4DB562 ON user (activities_id)');
    }
}
