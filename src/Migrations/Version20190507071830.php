<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190507071830 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE city_audit ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ride ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ride_audit ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL, ADD imageGoogleCloudHash VARCHAR(255) DEFAULT NULL, ADD backupSize INT DEFAULT NULL, ADD backupMimeType VARCHAR(255) DEFAULT NULL, ADD backupGoogleCloudHash VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user_user ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD trackSize INT DEFAULT NULL, ADD trackMimeType VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE frontpage_teaser ADD imageSize INT DEFAULT NULL, ADD imageMimeType VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE city_audit DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE fos_user_user DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE frontpage_teaser DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE photo DROP imageSize, DROP imageMimeType, DROP imageGoogleCloudHash, DROP backupSize, DROP backupMimeType, DROP backupGoogleCloudHash');
        $this->addSql('ALTER TABLE ride DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE ride_audit DROP imageSize, DROP imageMimeType');
        $this->addSql('ALTER TABLE track ADD previewPolyline LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP trackSize, DROP trackMimeType');
    }
}
