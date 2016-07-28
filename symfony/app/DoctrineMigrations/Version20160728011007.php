<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160728011007 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user CHANGE token token VARCHAR(32) DEFAULT NULL, CHANGE phoneNumber phoneNumber VARCHAR(32) DEFAULT NULL, CHANGE phoneNumberVerified phoneNumberVerified TINYINT(1) DEFAULT NULL, CHANGE phoneNumberVerificationDateTime phoneNumberVerificationDateTime DATETIME DEFAULT NULL, CHANGE phoneNumberVerificationToken phoneNumberVerificationToken VARCHAR(32) DEFAULT NULL, CHANGE pushoverToken pushoverToken VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user CHANGE token token VARCHAR(32) NOT NULL, CHANGE phoneNumber phoneNumber VARCHAR(32) NOT NULL, CHANGE phoneNumberVerified phoneNumberVerified TINYINT(1) NOT NULL, CHANGE phoneNumberVerificationDateTime phoneNumberVerificationDateTime DATETIME NOT NULL, CHANGE phoneNumberVerificationToken phoneNumberVerificationToken VARCHAR(32) NOT NULL, CHANGE pushoverToken pushoverToken VARCHAR(255) NOT NULL');
    }
}
