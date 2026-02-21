<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204225735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync database schema with entities: remove unused tables and columns, update nullable settings';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blacklisted_website');
        $this->addSql('DROP TABLE crawled_website');
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE board CHANGE title title LONGTEXT DEFAULT NULL, CHANGE threadNumber threadNumber INT DEFAULT NULL, CHANGE postNumber postNumber INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE city_blocked CHANGE blockStart blockStart DATETIME DEFAULT NULL, CHANGE blockEnd blockEnd DATETIME DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE photosLink photosLink TINYINT(1) DEFAULT NULL, CHANGE rideListLink rideListLink TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE city_view CHANGE dateTime dateTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE cityslug CHANGE slug slug VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE frontpage_teaser CHANGE position position SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE frontpage_teaser_button CHANGE position position SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE participation CHANGE dateTime dateTime DATETIME DEFAULT NULL, CHANGE goingYes goingYes TINYINT(1) DEFAULT NULL, CHANGE goingMaybe goingMaybe TINYINT(1) DEFAULT NULL, CHANGE goingNo goingNo TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_view CHANGE dateTime dateTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP crawled');
        $this->addSql('ALTER TABLE region CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ride CHANGE dateTime dateTime DATETIME DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE participationsNumberYes participationsNumberYes INT DEFAULT NULL, CHANGE participationsNumberMaybe participationsNumberMaybe INT DEFAULT NULL, CHANGE participationsNumberNo participationsNumberNo INT DEFAULT NULL, CHANGE views views INT DEFAULT NULL, CHANGE restrictedPhotoAccess restrictedPhotoAccess TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ride_estimate CHANGE dateTime dateTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ride_view CHANGE dateTime dateTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE social_network_feed_item CHANGE text text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE subride CHANGE dateTime dateTime DATETIME DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE thread CHANGE title title LONGTEXT DEFAULT NULL, CHANGE views views INT DEFAULT NULL, CHANGE postNumber postNumber INT DEFAULT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE thread_view CHANGE dateTime dateTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE track DROP previewPolyline, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE creationDateTime creationDateTime DATETIME DEFAULT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE trackFilename trackFilename VARCHAR(255) DEFAULT NULL, CHANGE deleted deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE track_candidate CHANGE polyline polyline LONGTEXT NOT NULL, CHANGE rejected rejected TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE track_candidate RENAME INDEX idx_e48215eba76ed395 TO IDX_C90A20B0A76ED395');
        $this->addSql('ALTER TABLE track_candidate RENAME INDEX idx_e48215eb302a8a70 TO IDX_C90A20B0302A8A70');
        $this->addSql('ALTER TABLE user DROP salt, DROP password, CHANGE enabled enabled TINYINT(1) DEFAULT 0, CHANGE roles roles JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE weather CHANGE weatherDateTime weatherDateTime DATETIME DEFAULT NULL, CHANGE creationDateTime creationDateTime DATETIME DEFAULT NULL, CHANGE temperatureMin temperatureMin DOUBLE PRECISION DEFAULT NULL, CHANGE temperatureMax temperatureMax DOUBLE PRECISION DEFAULT NULL, CHANGE temperatureMorning temperatureMorning DOUBLE PRECISION DEFAULT NULL, CHANGE temperatureDay temperatureDay DOUBLE PRECISION DEFAULT NULL, CHANGE temperatureEvening temperatureEvening DOUBLE PRECISION DEFAULT NULL, CHANGE temperatureNight temperatureNight DOUBLE PRECISION DEFAULT NULL, CHANGE pressure pressure DOUBLE PRECISION DEFAULT NULL, CHANGE humidity humidity DOUBLE PRECISION DEFAULT NULL, CHANGE weatherCode weatherCode INT DEFAULT NULL, CHANGE weatherDescription weatherDescription VARCHAR(255) DEFAULT NULL, CHANGE weatherIcon weatherIcon VARCHAR(5) DEFAULT NULL, CHANGE windSpeed windSpeed DOUBLE PRECISION DEFAULT NULL, CHANGE clouds clouds DOUBLE PRECISION DEFAULT NULL, CHANGE windDirection windDirection DOUBLE PRECISION DEFAULT NULL, CHANGE precipitation precipitation DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blacklisted_website (id INT AUTO_INCREMENT NOT NULL, pattern VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE crawled_website (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, imageUrl VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, title LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, executed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE board CHANGE title title LONGTEXT NOT NULL, CHANGE threadNumber threadNumber INT NOT NULL, CHANGE postNumber postNumber INT NOT NULL, CHANGE position position INT NOT NULL, CHANGE enabled enabled TINYINT(1) NOT NULL, CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE city_blocked CHANGE blockStart blockStart DATETIME NOT NULL, CHANGE blockEnd blockEnd DATETIME NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE photosLink photosLink TINYINT(1) NOT NULL, CHANGE rideListLink rideListLink TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE city_view CHANGE dateTime dateTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE cityslug CHANGE slug slug VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE frontpage_teaser CHANGE position position SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE frontpage_teaser_button CHANGE position position SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE location CHANGE slug slug VARCHAR(255) NOT NULL, CHANGE title title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE participation CHANGE dateTime dateTime DATETIME NOT NULL, CHANGE goingYes goingYes TINYINT(1) NOT NULL, CHANGE goingMaybe goingMaybe TINYINT(1) NOT NULL, CHANGE goingNo goingNo TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE photo_view CHANGE dateTime dateTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post ADD crawled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE region CHANGE slug slug VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ride CHANGE description description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE dateTime dateTime DATETIME NOT NULL, CHANGE participationsNumberYes participationsNumberYes INT NOT NULL, CHANGE participationsNumberMaybe participationsNumberMaybe INT NOT NULL, CHANGE participationsNumberNo participationsNumberNo INT NOT NULL, CHANGE views views INT NOT NULL, CHANGE restrictedPhotoAccess restrictedPhotoAccess TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ride_estimate CHANGE dateTime dateTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE ride_view CHANGE dateTime dateTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE social_network_feed_item CHANGE text text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE subride CHANGE dateTime dateTime DATETIME NOT NULL, CHANGE latitude latitude DOUBLE PRECISION NOT NULL, CHANGE longitude longitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE thread CHANGE title title LONGTEXT NOT NULL, CHANGE slug slug VARCHAR(255) NOT NULL, CHANGE views views INT NOT NULL, CHANGE postNumber postNumber INT NOT NULL, CHANGE enabled enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE thread_view CHANGE dateTime dateTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE track ADD previewPolyline LONGTEXT DEFAULT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE creationDateTime creationDateTime DATETIME NOT NULL, CHANGE enabled enabled TINYINT(1) NOT NULL, CHANGE deleted deleted TINYINT(1) NOT NULL, CHANGE trackFilename trackFilename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE track_candidate CHANGE polyline polyline TEXT NOT NULL, CHANGE rejected rejected DATETIME NOT NULL');
        $this->addSql('ALTER TABLE track_candidate RENAME INDEX idx_c90a20b0a76ed395 TO IDX_E48215EBA76ED395');
        $this->addSql('ALTER TABLE track_candidate RENAME INDEX idx_c90a20b0302a8a70 TO IDX_E48215EB302A8A70');
        $this->addSql('ALTER TABLE user ADD salt VARCHAR(255) DEFAULT NULL, ADD password VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE enabled enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE weather CHANGE weatherDateTime weatherDateTime DATETIME NOT NULL, CHANGE creationDateTime creationDateTime DATETIME NOT NULL, CHANGE temperatureMin temperatureMin DOUBLE PRECISION NOT NULL, CHANGE temperatureMax temperatureMax DOUBLE PRECISION NOT NULL, CHANGE temperatureMorning temperatureMorning DOUBLE PRECISION NOT NULL, CHANGE temperatureDay temperatureDay DOUBLE PRECISION NOT NULL, CHANGE temperatureEvening temperatureEvening DOUBLE PRECISION NOT NULL, CHANGE temperatureNight temperatureNight DOUBLE PRECISION NOT NULL, CHANGE pressure pressure DOUBLE PRECISION NOT NULL, CHANGE humidity humidity DOUBLE PRECISION NOT NULL, CHANGE weatherCode weatherCode INT NOT NULL, CHANGE weatherDescription weatherDescription VARCHAR(255) NOT NULL, CHANGE weatherIcon weatherIcon VARCHAR(5) NOT NULL, CHANGE windSpeed windSpeed DOUBLE PRECISION NOT NULL, CHANGE windDirection windDirection DOUBLE PRECISION NOT NULL, CHANGE clouds clouds DOUBLE PRECISION NOT NULL, CHANGE precipitation precipitation DOUBLE PRECISION NOT NULL');
    }
}
