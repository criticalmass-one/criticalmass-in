<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180221194347 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile ADD subride_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE67B4822BF FOREIGN KEY (subride_id) REFERENCES subride (id)');
        $this->addSql('CREATE INDEX IDX_3AC92AE67B4822BF ON social_network_profile (subride_id)');
        $this->addSql('ALTER TABLE social_network_profile_audit ADD subride_id INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE67B4822BF');
        $this->addSql('DROP INDEX IDX_3AC92AE67B4822BF ON social_network_profile');
        $this->addSql('ALTER TABLE social_network_profile DROP subride_id');
        $this->addSql('ALTER TABLE social_network_profile_audit DROP subride_id');
    }
}
