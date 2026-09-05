<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Benennt die Tabelle user in app_user um.
 *
 * In PostgreSQL ist user ein reserviertes Wort — es steht dort fuer den
 * angemeldeten Datenbanknutzer. Doctrine quotet Tabellennamen nicht von sich
 * aus, und jedes handgeschriebene SQL auf dieser Tabelle waere eine stille
 * Falle. Der Schritt geschah deshalb noch unter MySQL, vor dem Umzug.
 *
 * Auf einer frisch angelegten Datenbank gibt es nichts zu tun: Die Baseline
 * legt die Tabelle inzwischen gleich als app_user an. Die Migration bleibt
 * trotzdem stehen, weil sie auf der Produktion als ausgefuehrt vermerkt ist.
 */
final class Version20260904130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table user to app_user, because user is reserved in PostgreSQL';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            !$schema->hasTable('user'),
            'Die Tabelle heisst bereits app_user — die Baseline legt sie so an.'
        );

        $this->addSql($this->umbenennen('user', 'app_user'));
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(!$schema->hasTable('app_user'), 'Es gibt keine Tabelle app_user.');

        $this->addSql($this->umbenennen('app_user', 'user'));
    }

    /**
     * MySQL kennt RENAME TABLE, PostgreSQL nur ALTER TABLE ... RENAME TO — und
     * dort muss "user" gequotet werden, weil es ein reserviertes Wort ist.
     */
    private function umbenennen(string $von, string $nach): string
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            return sprintf(
                'ALTER TABLE %s RENAME TO %s',
                $platform->quoteSingleIdentifier($von),
                $platform->quoteSingleIdentifier($nach)
            );
        }

        return sprintf('RENAME TABLE %s TO %s', $von, $nach);
    }
}
