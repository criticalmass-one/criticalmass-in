<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171002202522 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subride_audit (id INT NOT NULL, rev INT NOT NULL, ride_id INT DEFAULT NULL, user_id INT DEFAULT NULL, archive_parent_id INT DEFAULT NULL, archive_user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, dateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', creationDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', location VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, isArchived TINYINT(1) DEFAULT NULL, archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', archiveMessage LONGTEXT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_99d30bd26344db373afaa44b935cedb0_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, region_id INT DEFAULT NULL, main_slug_id INT DEFAULT NULL, archive_parent_id INT DEFAULT NULL, archive_user_id INT DEFAULT NULL, lastthread_id INT DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, isStandardable TINYINT(1) DEFAULT NULL, standardDayOfWeek SMALLINT DEFAULT NULL, standardWeekOfMonth SMALLINT DEFAULT NULL, isStandardableTime TINYINT(1) DEFAULT NULL, standardTime TIME DEFAULT NULL COMMENT \'(DC2Type:time)\', isStandardableLocation TINYINT(1) DEFAULT NULL, standardLocation VARCHAR(255) DEFAULT NULL, standardLatitude DOUBLE PRECISION DEFAULT NULL, standardLongitude DOUBLE PRECISION DEFAULT NULL, cityPopulation INT DEFAULT NULL, punchLine VARCHAR(255) DEFAULT NULL, longDescription LONGTEXT DEFAULT NULL, isArchived TINYINT(1) DEFAULT NULL, archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', archiveMessage LONGTEXT DEFAULT NULL, imageName VARCHAR(255) DEFAULT NULL, updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', enableBoard TINYINT(1) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, threadNumber INT DEFAULT NULL, postNumber INT DEFAULT NULL, colorRed INT DEFAULT NULL, colorGreen INT DEFAULT NULL, colorBlue INT DEFAULT NULL, views INT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_8ca4fce52832174eea841ea82cfa5ce3_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region_audit (id INT NOT NULL, rev INT NOT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_5a3ea96b756c12a4116022704fb1beed_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ride_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, archive_parent_id INT DEFAULT NULL, archive_user_id INT DEFAULT NULL, featured_photo INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, socialDescription LONGTEXT DEFAULT NULL, dateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hasTime TINYINT(1) DEFAULT NULL, hasLocation TINYINT(1) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, estimatedParticipants SMALLINT DEFAULT NULL, estimatedDistance DOUBLE PRECISION DEFAULT NULL, estimatedDuration DOUBLE PRECISION DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, isArchived TINYINT(1) DEFAULT NULL, archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', archiveMessage LONGTEXT DEFAULT NULL, createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', participationsNumberYes INT DEFAULT NULL, participationsNumberMaybe INT DEFAULT NULL, participationsNumberNo INT DEFAULT NULL, views INT DEFAULT NULL, restrictedPhotoAccess TINYINT(1) DEFAULT NULL, imageName VARCHAR(255) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_7e0decbd0de112a11624c181adbca6b4_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE subride_audit');
        $this->addSql('DROP TABLE city_audit');
        $this->addSql('DROP TABLE region_audit');
        $this->addSql('DROP TABLE ride_audit');
    }
}
