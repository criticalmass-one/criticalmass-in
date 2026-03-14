<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260310130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase ride location column length from 255 to 512';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ride MODIFY location VARCHAR(512) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ride MODIFY location VARCHAR(255) DEFAULT NULL');
    }
}
