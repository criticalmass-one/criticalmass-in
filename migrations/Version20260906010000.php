<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Schaltet PostGIS in der Datenbank frei.
 *
 * Damit steht der Datentyp geometry bereit — die Voraussetzung dafuer, dass die
 * Koordinaten von Staedten, Fahrten, Orten und Fotos als Punkte statt als zwei
 * lose Fliesskommazahlen gespeichert werden koennen (Issues #1136 bis #1142).
 *
 * Die Extension legt in public zusaetzlich die Tabelle spatial_ref_sys an, in
 * der die Koordinatensysteme stehen. Sie gehoert PostGIS, nicht uns — der
 * schema_filter in doctrine.yaml haelt Doctrine davon ab, sie fuer verwaist zu
 * halten und loeschen zu wollen.
 */
final class Version20260906010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enable the PostGIS extension';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'PostGIS gibt es nur fuer PostgreSQL.'
        );

        $this->addSql('CREATE EXTENSION IF NOT EXISTS postgis');
    }

    public function down(Schema $schema): void
    {
        // Nicht zurueckrollbar, sobald Geometriespalten daran haengen: DROP
        // EXTENSION braeuchte CASCADE und nimmt die Spalten mit den Daten mit.
        throw new \LogicException(
            'PostGIS wird nicht wieder abgeschaltet — ein DROP EXTENSION CASCADE '
            . 'nimmt jede Geometriespalte samt Inhalt mit.'
        );
    }
}
