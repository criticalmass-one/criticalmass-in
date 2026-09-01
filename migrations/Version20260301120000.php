<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260301120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop main_network column from social_network_profile';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social_network_profile DROP COLUMN main_network');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social_network_profile ADD main_network TINYINT(1) NOT NULL DEFAULT 0');
    }
}
