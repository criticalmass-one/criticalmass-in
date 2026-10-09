<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Merkt sich am Konto, ob jemand die neue Ansicht testet. Bewusst ohne Vorgabewert:
 * NULL heißt „nie gewählt“, dann entscheidet das Cookie des Geräts. So geht die Wahl,
 * die jemand als Gast getroffen hat, beim ersten Anmelden nicht verloren.
 *
 * Fällt weg, sobald die neue Ansicht für alle gilt.
 */
final class Version20261009120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add app_user.design_v2 for the opt-in to the new design';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user ADD design_v2 BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user DROP design_v2');
    }
}
