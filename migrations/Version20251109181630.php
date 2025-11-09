<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109181630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE track ADD app VARCHAR(255) DEFAULT NULL, DROP previewPolyline, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE creationDateTime creationDateTime DATETIME DEFAULT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE trackFilename trackFilename VARCHAR(255) DEFAULT NULL, CHANGE deleted deleted TINYINT(1) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE track ADD previewPolyline LONGTEXT DEFAULT NULL, DROP app, CHANGE username username VARCHAR(255) NOT NULL, CHANGE creationDateTime creationDateTime DATETIME NOT NULL, CHANGE enabled enabled TINYINT(1) NOT NULL, CHANGE deleted deleted TINYINT(1) NOT NULL, CHANGE trackFilename trackFilename VARCHAR(255) NOT NULL
        SQL);
    }
}
