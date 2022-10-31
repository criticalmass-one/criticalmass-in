<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171005204847 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP isStandardable, DROP standardDayOfWeek, DROP standardWeekOfMonth, DROP standardTime, DROP standardLocation, DROP standardLatitude, DROP standardLongitude, DROP isStandardableTime, DROP isStandardableLocation');
        $this->addSql('ALTER TABLE city_audit DROP isStandardable, DROP standardDayOfWeek, DROP standardWeekOfMonth, DROP isStandardableTime, DROP standardTime, DROP isStandardableLocation, DROP standardLocation, DROP standardLatitude, DROP standardLongitude');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city ADD isStandardable TINYINT(1) NOT NULL, ADD standardDayOfWeek SMALLINT DEFAULT NULL, ADD standardWeekOfMonth SMALLINT DEFAULT NULL, ADD standardTime TIME DEFAULT NULL COMMENT \'(DC2Type:time)\', ADD standardLocation VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD standardLatitude DOUBLE PRECISION DEFAULT NULL, ADD standardLongitude DOUBLE PRECISION DEFAULT NULL, ADD isStandardableTime TINYINT(1) NOT NULL, ADD isStandardableLocation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE city_audit ADD isStandardable TINYINT(1) DEFAULT NULL, ADD standardDayOfWeek SMALLINT DEFAULT NULL, ADD standardWeekOfMonth SMALLINT DEFAULT NULL, ADD isStandardableTime TINYINT(1) DEFAULT NULL, ADD standardTime TIME DEFAULT NULL COMMENT \'(DC2Type:time)\', ADD isStandardableLocation TINYINT(1) DEFAULT NULL, ADD standardLocation VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD standardLatitude DOUBLE PRECISION DEFAULT NULL, ADD standardLongitude DOUBLE PRECISION DEFAULT NULL');
    }
}
