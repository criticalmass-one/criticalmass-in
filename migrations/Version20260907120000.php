<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gibt Orten und Fotos ihre Lage als Geometrie (Issues #1138, #1139).
 *
 * Gleiches Vorgehen wie bei Stadt, Fahrt und Tourenmuster: Die Spalte wird aus
 * latitude und longitude gefuellt und danach von den Settern nachgefuehrt; die
 * beiden Fliesskommaspalten bleiben vorerst die fuehrenden Werte, weil
 * API-Ausgabe, Formulare und die DataQuery-Attribute an ihnen haengen.
 *
 * Bestand zum Zeitpunkt dieser Migration: 23 Orte, alle mit Koordinaten, und
 * 45.499 Fotos, davon 26.024 mit echten GPS-Daten aus den EXIF-Angaben. Der
 * Punkt 0,0 im Golf von Guinea dient als Platzhalter fuer "keine Angabe" und
 * bekommt keine Geometrie — bei den Fotos betrifft das 18 Zeilen.
 */
final class Version20260907120000 extends AbstractMigration
{
    private const TABELLEN = ['location', 'photo'];

    public function getDescription(): string
    {
        return 'Add point geometries to location and photo';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Geometriespalten gibt es nur mit PostGIS.'
        );

        foreach (self::TABELLEN as $tabelle) {
            $this->addSql(sprintf('ALTER TABLE %s ADD coordinates geometry(POINT, 4326) DEFAULT NULL', $tabelle));

            // In WKT steht die Laenge vor der Breite.
            $this->addSql(sprintf('
                UPDATE %s
                SET coordinates = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)
                WHERE latitude IS NOT NULL AND longitude IS NOT NULL
                  AND latitude <> 0 AND longitude <> 0
            ', $tabelle));

            $this->addSql(sprintf(
                'CREATE INDEX %s_coordinates_gist ON %s USING gist(coordinates)', $tabelle, $tabelle
            ));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::TABELLEN as $tabelle) {
            $this->addSql(sprintf('DROP INDEX IF EXISTS %s_coordinates_gist', $tabelle));
            $this->addSql(sprintf('ALTER TABLE %s DROP COLUMN coordinates', $tabelle));
        }
    }
}
