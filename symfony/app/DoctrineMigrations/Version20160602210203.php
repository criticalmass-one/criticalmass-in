<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160602210203 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE caldera_bikeshop DROP FOREIGN KEY FK_D8CB69CEBF396750');
        $this->addSql('ALTER TABLE caldera_event DROP FOREIGN KEY FK_586C224FBF396750');
        $this->addSql('ALTER TABLE content_class_city DROP FOREIGN KEY FK_1F8389B8B7E1FF');
        $this->addSql('ALTER TABLE content_item DROP FOREIGN KEY FK_D279C8DB8BAC62AF');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE plus_voucher_code DROP FOREIGN KEY FK_B3BA0733E417D410');
        $this->addSql('DROP TABLE caldera_baselocationentity');
        $this->addSql('DROP TABLE caldera_bikeshop');
        $this->addSql('DROP TABLE caldera_event');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE content_archived');
        $this->addSql('DROP TABLE content_class');
        $this->addSql('DROP TABLE content_class_city');
        $this->addSql('DROP TABLE content_item');
        $this->addSql('DROP TABLE cycleways_incident');
        $this->addSql('DROP TABLE cycleways_incident_type');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE plus_voucher_class');
        $this->addSql('DROP TABLE plus_voucher_code');
        $this->addSql('DROP TABLE poi');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE caldera_baselocationentity (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_FC1533A68BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_bikeshop (id INT NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_event (id INT NOT NULL, archive_parent_id INT DEFAULT NULL, user_id INT DEFAULT NULL, archive_user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL, hasTime TINYINT(1) NOT NULL, hasLocation TINYINT(1) NOT NULL, location VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, isArchived TINYINT(1) NOT NULL, archiveDateTime DATETIME NOT NULL, createdAt DATETIME DEFAULT NULL, participationsNumberYes INT NOT NULL, participationsNumberMaybe INT NOT NULL, participationsNumberNo INT NOT NULL, views INT NOT NULL, INDEX IDX_586C224FA76ED395 (user_id), INDEX IDX_586C224F365388CC (archive_parent_id), INDEX IDX_586C224FCA4E326A (archive_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, user_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, dateTime DATETIME NOT NULL, message LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, hasCoords TINYINT(1) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_archived (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, isPublicEditable TINYINT(1) NOT NULL, showInfobox TINYINT(1) NOT NULL, lastEditionDateTime DATETIME NOT NULL, isArchived TINYINT(1) NOT NULL, archiveDateTime DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_class (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_class_city (content_class_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_1F8389B8B7E1FF (content_class_id), INDEX IDX_1F83898BAC62AF (city_id), PRIMARY KEY(content_class_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_item (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, positionOrder INT NOT NULL, INDEX IDX_D279C8DB8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycleways_incident (id INT AUTO_INCREMENT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, creationDateTime DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_69C35C65A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycleways_incident_type (id INT AUTO_INCREMENT NOT NULL, caption VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_583D1F3E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plus_voucher_class (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, validSince DATETIME NOT NULL, validUntil DATETIME NOT NULL, codePrefix VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plus_voucher_code (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, voucher_code_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, activationDateTime DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B3BA073377153098 (code), UNIQUE INDEX UNIQ_B3BA0733A76ED395 (user_id), INDEX IDX_B3BA0733E417D410 (voucher_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poi (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, city_id INT DEFAULT NULL, user_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, visibleFrom DATETIME NOT NULL, visibleUntil DATETIME NOT NULL, creationDateTime DATETIME NOT NULL, INDEX IDX_7DBB1FD6A76ED395 (user_id), INDEX IDX_7DBB1FD68BAC62AF (city_id), INDEX IDX_7DBB1FD6302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE caldera_baselocationentity ADD CONSTRAINT FK_FC1533A68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE caldera_bikeshop ADD CONSTRAINT FK_D8CB69CEBF396750 FOREIGN KEY (id) REFERENCES caldera_baselocationentity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caldera_event ADD CONSTRAINT FK_586C224F365388CC FOREIGN KEY (archive_parent_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE caldera_event ADD CONSTRAINT FK_586C224FA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE caldera_event ADD CONSTRAINT FK_586C224FBF396750 FOREIGN KEY (id) REFERENCES caldera_baselocationentity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caldera_event ADD CONSTRAINT FK_586C224FCA4E326A FOREIGN KEY (archive_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE content_class_city ADD CONSTRAINT FK_1F83898BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE content_class_city ADD CONSTRAINT FK_1F8389B8B7E1FF FOREIGN KEY (content_class_id) REFERENCES content_class (id)');
        $this->addSql('ALTER TABLE content_item ADD CONSTRAINT FK_D279C8DB8BAC62AF FOREIGN KEY (city_id) REFERENCES content_class (id)');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plus_voucher_code ADD CONSTRAINT FK_B3BA0733A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE plus_voucher_code ADD CONSTRAINT FK_B3BA0733E417D410 FOREIGN KEY (voucher_code_id) REFERENCES plus_voucher_class (id)');
        $this->addSql('ALTER TABLE poi ADD CONSTRAINT FK_7DBB1FD6302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE poi ADD CONSTRAINT FK_7DBB1FD68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE poi ADD CONSTRAINT FK_7DBB1FD6A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
    }
}
