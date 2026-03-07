<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260307120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix invalid dayOfWeek values in city_cycle (must be 0-6)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE city_cycle SET day_of_week = 0 WHERE day_of_week > 6');
    }

    public function down(Schema $schema): void
    {
    }
}
