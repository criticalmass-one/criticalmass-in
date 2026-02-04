<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260204165620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add rating table for ride star ratings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, ride_id INT NOT NULL, user_id INT NOT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D8892622302A8A70 (ride_id), INDEX IDX_D8892622A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622302A8A70');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('DROP TABLE rating');
    }
}
