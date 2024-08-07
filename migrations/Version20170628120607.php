<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20170628120607 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user DROP FOREIGN KEY FK_C560D7618BAC62AF');
        $this->addSql('DROP INDEX IDX_C560D7618BAC62AF ON fos_user_user');
        $this->addSql('ALTER TABLE fos_user_user DROP city_id, DROP description, DROP token, DROP phoneNumber, DROP phoneNumberVerified, DROP phoneNumberVerificationDateTime, DROP phoneNumberVerificationToken, DROP pushoverToken');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user ADD city_id INT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD token VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, ADD phoneNumber VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, ADD phoneNumberVerified TINYINT(1) DEFAULT NULL, ADD phoneNumberVerificationDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD phoneNumberVerificationToken VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, ADD pushoverToken VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE fos_user_user ADD CONSTRAINT FK_C560D7618BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_C560D7618BAC62AF ON fos_user_user (city_id)');
    }
}
