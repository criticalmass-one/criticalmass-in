<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260310120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert disabledReason from ENUM to VARCHAR and fix typo WRONG_AUTO_GNERATION -> WRONG_AUTO_GENERATION';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE ride MODIFY disabledReason VARCHAR(255) DEFAULT NULL");
        $this->addSql("UPDATE ride SET disabledReason = 'WRONG_AUTO_GENERATION' WHERE disabledReason = 'WRONG_AUTO_GNERATION'");
    }

    public function down(Schema $schema): void
    {
    }
}
