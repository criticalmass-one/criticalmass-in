<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171005200315 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city_cycle CHANGE validFrom validFrom DATE DEFAULT NULL COMMENT \'(DC2Type:date)\', CHANGE validUntil validUntil DATE DEFAULT NULL COMMENT \'(DC2Type:date)\'');
        $this->addSql('ALTER TABLE city_cycle_audit CHANGE validFrom validFrom DATE DEFAULT NULL COMMENT \'(DC2Type:date)\', CHANGE validUntil validUntil DATE DEFAULT NULL COMMENT \'(DC2Type:date)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city_cycle CHANGE validFrom validFrom DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE validUntil validUntil DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE city_cycle_audit CHANGE validFrom validFrom DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE validUntil validUntil DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
    }
}
