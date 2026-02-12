<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create track_polyline table and drop legacy polyline columns from track';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE track_polyline (id INT AUTO_INCREMENT NOT NULL, track_id INT NOT NULL, resolution SMALLINT NOT NULL, polyline LONGTEXT NOT NULL, num_points INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_TRACK_POLYLINE_TRACK (track_id), UNIQUE INDEX UNIQ_TRACK_RESOLUTION (track_id, resolution), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE track_polyline ADD CONSTRAINT FK_TRACK_POLYLINE_TRACK_ID FOREIGN KEY (track_id) REFERENCES track (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track DROP polyline, DROP reducedPolyline');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track ADD polyline LONGTEXT DEFAULT NULL, ADD reducedPolyline LONGTEXT DEFAULT NULL');
        $this->addSql('DROP TABLE track_polyline');
    }
}
