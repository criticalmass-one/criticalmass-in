<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gibt Tracks ihren Weg als Geometrie (Issue #1140, erster Schritt).
 *
 * Anders als bei den Punktgeometrien fuellt diese Migration die Spalte
 * **nicht**: Die Quelle ist die GPX-Datei, und die kann SQL nicht lesen. Das
 * uebernimmt danach `criticalmass:tracks:linestring` — 2.186 Dateien, in
 * Haeppchen und beliebig oft wiederholbar.
 *
 * Bewusst nicht aus den vorhandenen Polylinien gefuellt: Das Polylinienformat
 * rundet auf fuenf Nachkommastellen, die GPX-Datei ist verlustfrei. Und die
 * gespeicherten Polylinien liegen ohnehin nur in drei groben Aufloesungen vor.
 */
final class Version20260907130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a linestring geometry to track';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Geometriespalten gibt es nur mit PostGIS.'
        );

        $this->addSql('ALTER TABLE track ADD lineString geometry(LINESTRING, 4326) DEFAULT NULL');
        $this->addSql('CREATE INDEX track_linestring_gist ON track USING gist(lineString)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS track_linestring_gist');
        $this->addSql('ALTER TABLE track DROP COLUMN lineString');
    }
}
