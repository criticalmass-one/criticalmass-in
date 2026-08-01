<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create the photo_candidate table: staged photos awaiting review in the
 * unified upload (parallel to track_candidate for the track side).
 */
final class Version20260618171157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create photo_candidate table (staged photos for the unified upload review)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE photo_candidate (id INT AUTO_INCREMENT NOT NULL, fileHash VARCHAR(40) NOT NULL, stagedFilename VARCHAR(255) NOT NULL, originalName VARCHAR(255) DEFAULT NULL, mimeType VARCHAR(255) DEFAULT NULL, exifCreationDate DATETIME DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, createdAt DATETIME NOT NULL, rejected TINYINT DEFAULT 0 NOT NULL, user_id INT NOT NULL, ride_id INT DEFAULT NULL, INDEX IDX_C54CD865A76ED395 (user_id), INDEX IDX_C54CD865302A8A70 (ride_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE photo_candidate ADD CONSTRAINT FK_C54CD865A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photo_candidate ADD CONSTRAINT FK_C54CD865302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE photo_candidate DROP FOREIGN KEY FK_C54CD865A76ED395');
        $this->addSql('ALTER TABLE photo_candidate DROP FOREIGN KEY FK_C54CD865302A8A70');
        $this->addSql('DROP TABLE photo_candidate');
    }
}
