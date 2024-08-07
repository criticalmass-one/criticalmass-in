<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171006000400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride ADD cycle_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ride ADD CONSTRAINT FK_9B3D7CD05EC1162 FOREIGN KEY (cycle_id) REFERENCES city_cycle (id)');
        $this->addSql('CREATE INDEX IDX_9B3D7CD05EC1162 ON ride (cycle_id)');
        $this->addSql('ALTER TABLE ride_audit ADD cycle_id INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride DROP FOREIGN KEY FK_9B3D7CD05EC1162');
        $this->addSql('DROP INDEX IDX_9B3D7CD05EC1162 ON ride');
        $this->addSql('ALTER TABLE ride DROP cycle_id');
        $this->addSql('ALTER TABLE ride_audit DROP cycle_id');
    }
}
