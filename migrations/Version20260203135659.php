<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203135659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add CityActivity entity and activityScore field to City';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city_activity (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, score DOUBLE PRECISION NOT NULL, participationScore DOUBLE PRECISION NOT NULL, participationRawCount INT NOT NULL, photoScore DOUBLE PRECISION NOT NULL, photoRawCount INT NOT NULL, trackScore DOUBLE PRECISION NOT NULL, trackRawCount INT NOT NULL, socialFeedScore DOUBLE PRECISION NOT NULL, socialFeedRawCount INT NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_29AC713C8BAC62AF (city_id), INDEX city_activity_city_created_at_idx (city_id, createdAt), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_activity ADD CONSTRAINT FK_29AC713C8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE city ADD activityScore DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city_activity DROP FOREIGN KEY FK_29AC713C8BAC62AF');
        $this->addSql('DROP TABLE city_activity');
        $this->addSql('ALTER TABLE city DROP activityScore');
    }
}
