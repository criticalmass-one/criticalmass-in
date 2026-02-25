<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260224160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename track_polyline columns from snake_case to camelCase to match Doctrine naming strategy';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track_polyline CHANGE num_points numPoints INT NOT NULL, CHANGE created_at createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track_polyline CHANGE numPoints num_points INT NOT NULL, CHANGE createdAt created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
