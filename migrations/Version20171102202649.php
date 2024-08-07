<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171102202649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE help_item_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, position SMALLINT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_40bf9c8a67037c0f95d912e0444a980b_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_category_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, language VARCHAR(16) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, side VARCHAR(255) DEFAULT NULL, position SMALLINT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_0562db2c47a4725efbfa05c58008cbef_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE help_item_audit');
        $this->addSql('DROP TABLE help_category_audit');
    }
}
