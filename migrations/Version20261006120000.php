<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gibt jedem Beitrag eine Art: Kommentar, Statusbeitrag oder Artikel.
 *
 * Bisher war jeder Post ein Kommentar zu etwas — einer Tour, einem Foto,
 * einem Forumsthema. Kuenftig sollen Leute auch frei schreiben koennen, und
 * spaeter sollen die Artikel aus criticalmass.blog hier landen. Am Kontext
 * laesst sich die Art nicht ablesen: Ein Statusbeitrag kann an einer Stadt
 * haengen wie ein Kommentar. Deshalb eine eigene Spalte.
 *
 * Der Bestand wird zu COMMENT — mit einer Ausnahme: 24 Beitraege von Maerz
 * bis September 2016 haengen an gar nichts, weder an Tour, Foto, Thema noch
 * Stadt. Das sind die freien Beitraege von damals, also Statusbeitraege.
 * Eine Antwort darauf (parent_id gesetzt) bliebe ein Kommentar; in der
 * Produktion gibt es keine.
 *
 * Der CHECK haelt die Werte deckungsgleich mit App\Enum\PostKindEnum. Doctrine
 * liest ihn beim Schemavergleich nicht, er stoert `schema:update` also nicht;
 * ein neuer Fall im Enum braucht aber eine Migration, die ihn erweitert.
 */
final class Version20261006120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add post.kind (COMMENT, STATUS, ARTICLE)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE post ADD kind VARCHAR(16) DEFAULT 'COMMENT' NOT NULL");

        $this->addSql("ALTER TABLE post ADD CONSTRAINT post_kind_check CHECK (kind IN ('COMMENT', 'STATUS', 'ARTICLE'))");

        $this->addSql(
            "UPDATE post SET kind = 'STATUS'
             WHERE ride_id IS NULL AND photo_id IS NULL AND thread_id IS NULL AND city_id IS NULL
               AND parent_id IS NULL"
        );
    }

    public function down(Schema $schema): void
    {
        // MySQL weigert sich, eine Spalte zu loeschen, auf die noch ein CHECK
        // verweist; PostgreSQL raeumt ihn mit ab. Ausdruecklich zuerst weg —
        // DROP CONSTRAINT verstehen PostgreSQL, MariaDB und MySQL >= 8.0.19.
        $this->addSql('ALTER TABLE post DROP CONSTRAINT post_kind_check');

        $this->addSql('ALTER TABLE post DROP kind');
    }
}
