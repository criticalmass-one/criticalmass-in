<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170201233201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE incident_incident_tag DROP FOREIGN KEY FK_E90604ACBAD26311');
        $this->addSql('ALTER TABLE incident_view DROP FOREIGN KEY FK_14F1DC3159E53FB9');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841859E53FB9');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D59E53FB9');
        $this->addSql('ALTER TABLE incident_incident_tag DROP FOREIGN KEY FK_E90604AC59E53FB9');
        $this->addSql('DROP TABLE incident');
        $this->addSql('DROP TABLE incident_incident_tag');
        $this->addSql('DROP TABLE incident_tag');
        $this->addSql('DROP TABLE incident_view');
        $this->addSql('DROP INDEX IDX_14B7841859E53FB9 ON photo');
        $this->addSql('ALTER TABLE photo DROP incident_id');
        $this->addSql('DROP INDEX IDX_5A8A6C8D59E53FB9 ON post');
        $this->addSql('ALTER TABLE post DROP incident_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE incident (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, geometryType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, polyline LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, expires TINYINT(1) NOT NULL, visibleFrom DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', visibleTo DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', incidentType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, dangerLevel VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, address LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, slug VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, houseNumber VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, zipCode VARCHAR(5) DEFAULT NULL COLLATE utf8_unicode_ci, suburb VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, district VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, dateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', permalink VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, views INT NOT NULL, streetviewLink LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_3D03A11AA76ED395 (user_id), INDEX IDX_3D03A11A8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident_incident_tag (incident_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E90604AC59E53FB9 (incident_id), INDEX IDX_E90604ACBAD26311 (tag_id), PRIMARY KEY(incident_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident_tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, font_color VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, background_color VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident_view (id INT AUTO_INCREMENT NOT NULL, incident_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_14F1DC31A76ED395 (user_id), INDEX IDX_14F1DC3159E53FB9 (incident_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11A8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11AA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE incident_incident_tag ADD CONSTRAINT FK_E90604AC59E53FB9 FOREIGN KEY (incident_id) REFERENCES incident_tag (id)');
        $this->addSql('ALTER TABLE incident_incident_tag ADD CONSTRAINT FK_E90604ACBAD26311 FOREIGN KEY (tag_id) REFERENCES incident (id)');
        $this->addSql('ALTER TABLE incident_view ADD CONSTRAINT FK_14F1DC3159E53FB9 FOREIGN KEY (incident_id) REFERENCES incident (id)');
        $this->addSql('ALTER TABLE incident_view ADD CONSTRAINT FK_14F1DC31A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE photo ADD incident_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841859E53FB9 FOREIGN KEY (incident_id) REFERENCES incident (id)');
        $this->addSql('CREATE INDEX IDX_14B7841859E53FB9 ON photo (incident_id)');
        $this->addSql('ALTER TABLE post ADD incident_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D59E53FB9 FOREIGN KEY (incident_id) REFERENCES incident (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D59E53FB9 ON post (incident_id)');
    }
}
