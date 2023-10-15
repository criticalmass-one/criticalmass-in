<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230922182548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_C560D76192FC23A8 ON fos_user_user');
        $this->addSql('DROP INDEX UNIQ_C560D761A0D96FBF ON fos_user_user');
        $this->addSql('DROP INDEX UNIQ_C560D761C05FB297 ON fos_user_user');
        $this->addSql('UPDATE fos_user_user SET roles = \'[]\'');
        $this->addSql('ALTER TABLE fos_user_user DROP username_canonical, DROP email_canonical, DROP enabled, DROP last_login, DROP confirmation_token, DROP password_requested_at, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE roles roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE colorRed colorRed SMALLINT DEFAULT NULL, CHANGE colorGreen colorGreen SMALLINT DEFAULT NULL, CHANGE colorBlue colorBlue SMALLINT DEFAULT NULL, CHANGE updatedAt updatedAt DATETIME DEFAULT NULL, CHANGE createdAt createdAt DATETIME DEFAULT NULL, CHANGE blurGalleries blurGalleries TINYINT(1) DEFAULT \'0\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user ADD username_canonical VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, ADD email_canonical VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, ADD enabled TINYINT(1) NOT NULL, ADD last_login DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(180) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, ADD password_requested_at DATETIME DEFAULT NULL, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE email email VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE username username VARCHAR(180) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, CHANGE colorRed colorRed SMALLINT NOT NULL, CHANGE colorGreen colorGreen SMALLINT NOT NULL, CHANGE colorBlue colorBlue SMALLINT NOT NULL, CHANGE blurGalleries blurGalleries TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE updatedAt updatedAt DATETIME NOT NULL, CHANGE createdAt createdAt DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D76192FC23A8 ON fos_user_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D761A0D96FBF ON fos_user_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D761C05FB297 ON fos_user_user (confirmation_token)');
    }
}
