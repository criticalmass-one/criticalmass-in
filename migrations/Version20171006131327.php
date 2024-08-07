<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171006131327 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE frontpage_teaser_button (id INT AUTO_INCREMENT NOT NULL, teaser_id INT DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, icon VARCHAR(32) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, final class VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_CCA0C4A17ADE9C9E (teaser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE frontpage_teaser_button ADD CONSTRAINT FK_CCA0C4A17ADE9C9E FOREIGN KEY (teaser_id) REFERENCES frontpage_teaser (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE frontpage_teaser_button');
    }
}
