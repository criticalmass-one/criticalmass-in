<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Benennt die Tabelle user in app_user um.
 *
 * In PostgreSQL ist user ein reserviertes Wort — es steht dort fuer den
 * angemeldeten Datenbanknutzer. Doctrine quotet Tabellennamen nicht von sich
 * aus, und jedes handgeschriebene SQL auf dieser Tabelle waere nach einem
 * Plattformwechsel eine stille Falle. Der Schritt geschieht deshalb jetzt,
 * unter MySQL, wo er folgenlos ist, und nicht mitten im Umzug.
 *
 * Die Fremdschluessel der 14 verweisenden Tabellen behalten ihre Namen; nur das
 * Ziel des Verweises aendert sich.
 */
final class Version20260904130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table user to app_user, because user is reserved in PostgreSQL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE user TO app_user');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE app_user TO user');
    }
}
