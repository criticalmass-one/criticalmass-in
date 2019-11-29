<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190707223721 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile ADD createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD createdBy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE63174800F FOREIGN KEY (createdBy_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_3AC92AE63174800F ON social_network_profile (createdBy_id)');
        $this->addSql('ALTER TABLE social_network_profile_audit ADD createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD createdBy_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE63174800F');
        $this->addSql('DROP INDEX IDX_3AC92AE63174800F ON social_network_profile');
        $this->addSql('ALTER TABLE social_network_profile DROP createdAt, DROP createdBy_id');
        $this->addSql('ALTER TABLE social_network_profile_audit DROP createdAt, DROP createdBy_id');
    }
}
