<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161031214117 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subride ADD archiveMessage LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE city ADD archiveMessage LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE event ADD archiveMessage LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE ride ADD archiveMessage LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE content ADD archiveMessage LONGTEXT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP archiveMessage');
        $this->addSql('ALTER TABLE content DROP archiveMessage');
        $this->addSql('ALTER TABLE event DROP archiveMessage');
        $this->addSql('ALTER TABLE ride DROP archiveMessage');
        $this->addSql('ALTER TABLE subride DROP archiveMessage');
    }
}
