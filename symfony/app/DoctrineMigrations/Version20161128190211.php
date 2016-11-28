<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161128120211 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subride CHANGE archiveMessage archiveMessage LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE city CHANGE archiveMessage archiveMessage LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE archiveMessage archiveMessage LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE ride CHANGE archiveMessage archiveMessage LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE content CHANGE archiveMessage archiveMessage LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city CHANGE archiveMessage archiveMessage LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE content CHANGE archiveMessage archiveMessage LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE event CHANGE archiveMessage archiveMessage LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ride CHANGE archiveMessage archiveMessage LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE subride CHANGE archiveMessage archiveMessage LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
    }
}
