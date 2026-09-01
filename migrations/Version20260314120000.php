<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert rideType column from ENUM to VARCHAR(255)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE ride MODIFY rideType VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
    }
}
