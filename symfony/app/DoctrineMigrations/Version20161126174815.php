<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161126174815 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city_blocked (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, blockStart DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', blockEnd DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', description LONGTEXT NOT NULL, url VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, photosLink TINYINT(1) NOT NULL, rideListLink TINYINT(1) NOT NULL, INDEX IDX_671D55D28BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_blocked ADD CONSTRAINT FK_671D55D28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city_blocked');
    }
}
