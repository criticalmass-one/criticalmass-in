<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Moderation states for forum threads: closed threads take no further replies,
 * sticky threads stay on top of the list.
 */
final class Version20260901213000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add locked and sticky to thread';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE thread ADD locked TINYINT(1) DEFAULT 0 NOT NULL, ADD sticky TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE thread DROP locked, DROP sticky');
    }
}
