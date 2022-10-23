<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171005152853 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city_cycle (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dayOfWeek SMALLINT NOT NULL, weekOfMonth SMALLINT DEFAULT NULL, time TIME DEFAULT NULL COMMENT \'(DC2Type:time)\', location VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validFrom DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validUntil DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_F30C76C48BAC62AF (city_id), INDEX IDX_F30C76C4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_cycle ADD CONSTRAINT FK_F30C76C48BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE city_cycle ADD CONSTRAINT FK_F30C76C4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city_cycle');
    }
}
