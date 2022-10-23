<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171005192621 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city_cycle_audit (id INT NOT NULL, rev INT NOT NULL, city_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dayOfWeek SMALLINT DEFAULT NULL, weekOfMonth SMALLINT DEFAULT NULL, time TIME DEFAULT NULL COMMENT \'(DC2Type:time)\', location VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validFrom DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validUntil DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', revtype VARCHAR(4) NOT NULL, INDEX rev_a88c19a0d7a832aa7656286cd6915b09_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city_cycle_audit');
    }
}
