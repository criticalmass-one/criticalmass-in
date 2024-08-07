<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171031224954 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE help_category ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help_category ADD CONSTRAINT FK_89779DC1727ACA70 FOREIGN KEY (parent_id) REFERENCES help_category (id)');
        $this->addSql('CREATE INDEX IDX_89779DC1727ACA70 ON help_category (parent_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE help_category DROP FOREIGN KEY FK_89779DC1727ACA70');
        $this->addSql('DROP INDEX IDX_89779DC1727ACA70 ON help_category');
        $this->addSql('ALTER TABLE help_category DROP parent_id');
    }
}
