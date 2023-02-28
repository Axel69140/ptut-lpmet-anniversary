<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228081039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity ADD medias_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC7F4A74B FOREIGN KEY (medias_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_AC74095AC7F4A74B ON activity (medias_id)');
        $this->addSql('ALTER TABLE article ADD medias_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C7F4A74B FOREIGN KEY (medias_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66C7F4A74B ON article (medias_id)');
        $this->addSql('ALTER TABLE timeline_step ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE timeline_step ADD CONSTRAINT FK_42B10B49EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42B10B49EA9FDD75 ON timeline_step (media_id)');
        $this->addSql('ALTER TABLE user ADD guests_id INT DEFAULT NULL, ADD activities_id INT DEFAULT NULL, ADD picture_id INT DEFAULT NULL, ADD articles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649825B2E45 FOREIGN KEY (guests_id) REFERENCES guest (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492A4DB562 FOREIGN KEY (activities_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491EBAF6CC FOREIGN KEY (articles_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649825B2E45 ON user (guests_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492A4DB562 ON user (activities_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491EBAF6CC ON user (articles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66C7F4A74B');
        $this->addSql('DROP INDEX IDX_23A0E66C7F4A74B ON article');
        $this->addSql('ALTER TABLE article DROP medias_id');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC7F4A74B');
        $this->addSql('DROP INDEX IDX_AC74095AC7F4A74B ON activity');
        $this->addSql('ALTER TABLE activity DROP medias_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649825B2E45');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492A4DB562');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491EBAF6CC');
        $this->addSql('DROP INDEX IDX_8D93D649825B2E45 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6492A4DB562 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE45BDBF ON user');
        $this->addSql('DROP INDEX IDX_8D93D6491EBAF6CC ON user');
        $this->addSql('ALTER TABLE user DROP guests_id, DROP activities_id, DROP picture_id, DROP articles_id');
        $this->addSql('ALTER TABLE timeline_step DROP FOREIGN KEY FK_42B10B49EA9FDD75');
        $this->addSql('DROP INDEX UNIQ_42B10B49EA9FDD75 ON timeline_step');
        $this->addSql('ALTER TABLE timeline_step DROP media_id');
    }
}
