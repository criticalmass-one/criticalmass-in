<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304040609 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feed_item CHANGE uniqueIdentifier uniqueIdentifier VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_feed_item ON feed_item (social_network_profile_id, uniqueIdentifier)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX unique_feed_item ON feed_item');
        $this->addSql('ALTER TABLE feed_item CHANGE uniqueIdentifier uniqueIdentifier LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
    }
}
