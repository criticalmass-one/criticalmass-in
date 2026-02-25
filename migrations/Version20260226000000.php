<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260226000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unused columns md5_hash and geo_json from track table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track DROP COLUMN md5_hash');
        $this->addSql('ALTER TABLE track DROP COLUMN geo_json');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track ADD COLUMN md5_hash VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD COLUMN geo_json LONGTEXT DEFAULT NULL');
    }
}
