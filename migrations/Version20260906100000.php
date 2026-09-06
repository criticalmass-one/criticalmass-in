<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Nimmt die Nicht-Bilder aus der Fototabelle aus dem Verkehr.
 *
 * 17 Zeilen tragen kein Bild: dreizehn Videos (mp4, mov, avi), die ein
 * frueherer Upload noch durchliess, und vier leere Dateien
 * (application/x-empty) aus einem missglueckten Upload an derselben Tour.
 * Liip kann daraus keine Miniaturansicht bauen und wirft stattdessen — jeder
 * Abruf einer solchen Vorschau endet in einem 500er, seit dem 9. Juli 292 Mal
 * und weiterhin ein paar Mal am Tag.
 *
 * Die Menge kann nicht mehr wachsen: Der heutige Upload nimmt nur noch
 * jpg, jpeg, png, webp, gif, heic und heif an (UploadDispatcher), und die
 * juengste dieser Zeilen stammt vom 4. Dezember 2025.
 *
 * `deleted` ist die Kennzeichnung, auf die alle Galerie-Abfragen bereits
 * hoeren — nicht ganz woertlich gemeint, denn geloescht hat sie niemand, aber
 * anzeigbar sind sie auch nicht. Die Dateien selbst bleiben unangetastet, und
 * down() nimmt die Kennzeichnung fuer genau diese Zeilen wieder zurueck.
 */
final class Version20260906100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mark photos that are not images as deleted';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            UPDATE photo
            SET deleted = true
            WHERE deleted = false
              AND (imageMimeType IS NULL OR imageMimeType NOT LIKE 'image/%')
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            UPDATE photo
            SET deleted = false
            WHERE deleted = true
              AND (imageMimeType IS NULL OR imageMimeType NOT LIKE 'image/%')
        ");
    }
}
