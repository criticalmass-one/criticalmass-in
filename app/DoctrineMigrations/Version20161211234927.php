<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161211234927 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE incident_tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, font_color VARCHAR(255) DEFAULT NULL, background_color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident_incident_tag (incident_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E90604AC59E53FB9 (incident_id), INDEX IDX_E90604ACBAD26311 (tag_id), PRIMARY KEY(incident_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE incident_incident_tag ADD CONSTRAINT FK_E90604AC59E53FB9 FOREIGN KEY (incident_id) REFERENCES incident_tag (id)');
        $this->addSql('ALTER TABLE incident_incident_tag ADD CONSTRAINT FK_E90604ACBAD26311 FOREIGN KEY (tag_id) REFERENCES incident (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE incident_incident_tag DROP FOREIGN KEY FK_E90604AC59E53FB9');
        $this->addSql('DROP TABLE incident_tag');
        $this->addSql('DROP TABLE incident_incident_tag');
    }
}
