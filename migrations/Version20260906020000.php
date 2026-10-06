<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gibt den Staedten ihre Lage als Geometrie (Issue #1136).
 *
 * Die Spalte wird aus latitude und longitude gefuellt und danach von den
 * Settern der Entity nachgefuehrt. Die beiden Fliesskommaspalten bleiben
 * vorerst die fuehrenden Werte — sie haengen an den DataQuery-Attributen, am
 * Formular, an der API-Ausgabe und an zwei Abfragen im CityRepository. Erst
 * wenn die auf PostGIS umgestellt sind (#1141), koennen sie fallen.
 *
 * Der Punkt 0,0 liegt im Golf von Guinea und dient im Bestand als Platzhalter
 * fuer Staedte ohne Koordinaten; solche Zeilen bekommen NULL statt einer
 * Geometrie, die eine Angabe vortaeuschen wuerde.
 */
final class Version20260906020000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a point geometry to city and fill it from latitude and longitude';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Geometriespalten gibt es nur mit PostGIS.'
        );

        $this->addSql('ALTER TABLE city ADD coordinates geometry(POINT, 4326) DEFAULT NULL');

        // In WKT steht die Laenge vor der Breite.
        $this->addSql('
            UPDATE city
            SET coordinates = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL
              AND latitude <> 0 AND longitude <> 0
        ');

        $this->addSql('CREATE INDEX city_coordinates_gist ON city USING gist(coordinates)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS city_coordinates_gist');
        $this->addSql('ALTER TABLE city DROP COLUMN coordinates');
    }
}
