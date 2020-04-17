<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180711163218 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride ADD shorturl VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ride_audit ADD shorturl VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD shorturl VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE city ADD shorturl VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE city_audit ADD shorturl VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP shorturl');
        $this->addSql('ALTER TABLE city_audit DROP shorturl');
        $this->addSql('ALTER TABLE photo DROP shorturl');
        $this->addSql('ALTER TABLE ride DROP shorturl');
        $this->addSql('ALTER TABLE ride_audit DROP shorturl');
    }
}
