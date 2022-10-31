<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171031230637 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE help_item ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help_item ADD CONSTRAINT FK_5A911085A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_5A911085A76ED395 ON help_item (user_id)');
        $this->addSql('ALTER TABLE help_category ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help_category ADD CONSTRAINT FK_89779DC1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_89779DC1A76ED395 ON help_category (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE help_category DROP FOREIGN KEY FK_89779DC1A76ED395');
        $this->addSql('DROP INDEX IDX_89779DC1A76ED395 ON help_category');
        $this->addSql('ALTER TABLE help_category DROP user_id');
        $this->addSql('ALTER TABLE help_item DROP FOREIGN KEY FK_5A911085A76ED395');
        $this->addSql('DROP INDEX IDX_5A911085A76ED395 ON help_item');
        $this->addSql('ALTER TABLE help_item DROP user_id');
    }
}
