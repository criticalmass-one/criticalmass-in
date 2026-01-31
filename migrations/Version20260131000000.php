<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260131000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Extend track.source ENUM with TRACK_SOURCE_FIT';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE track MODIFY source ENUM('TRACK_SOURCE_GPX', 'TRACK_SOURCE_STRAVA', 'TRACK_SOURCE_RUNKEEPER', 'TRACK_SOURCE_RUNTASTIC', 'TRACK_SOURCE_DRAW', 'TRACK_SOURCE_GLYMPSE', 'TRACK_SOURCE_CRITICALMAPS', 'TRACK_SOURCE_FIT', 'TRACK_SOURCE_UNKNOWN') DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE track MODIFY source ENUM('TRACK_SOURCE_GPX', 'TRACK_SOURCE_STRAVA', 'TRACK_SOURCE_RUNKEEPER', 'TRACK_SOURCE_RUNTASTIC', 'TRACK_SOURCE_DRAW', 'TRACK_SOURCE_GLYMPSE', 'TRACK_SOURCE_CRITICALMAPS', 'TRACK_SOURCE_UNKNOWN') DEFAULT NULL
        SQL);
    }
}
