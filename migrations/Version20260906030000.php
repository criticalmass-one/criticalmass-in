<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gibt Fahrten und Tourenmustern ihre Lage als Geometrie (Issues #1137, #1142).
 *
 * Gleiches Vorgehen wie bei den Staedten in Version20260906020000: Die Spalte
 * wird aus latitude und longitude gefuellt und danach von den Settern
 * nachgefuehrt; die beiden Fliesskommaspalten bleiben vorerst die fuehrenden
 * Werte, weil API-Ausgabe und Abfragen an ihnen haengen.
 *
 * Der Punkt 0,0 im Golf von Guinea dient im Bestand als Platzhalter fuer
 * "keine Koordinaten" — solche Zeilen bleiben ohne Geometrie.
 */
final class Version20260906030000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add point geometries to ride and city_cycle';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Geometriespalten gibt es nur mit PostGIS.'
        );

        foreach (['ride', 'city_cycle'] as $tabelle) {
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
        foreach (['ride', 'city_cycle'] as $tabelle) {
            $this->addSql(sprintf('DROP INDEX IF EXISTS %s_coordinates_gist', $tabelle));
            $this->addSql(sprintf('ALTER TABLE %s DROP COLUMN coordinates', $tabelle));
        }
    }
}
