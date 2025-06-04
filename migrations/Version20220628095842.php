<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628095842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP shorturl');
        $this->addSql('ALTER TABLE photo DROP shorturl');
        $this->addSql('ALTER TABLE ride DROP shorturl');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city ADD shorturl VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE photo ADD shorturl VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`');
        $this->addSql('ALTER TABLE ride ADD shorturl VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`');
    }
}
