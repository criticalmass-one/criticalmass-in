<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make track import candidates source-agnostic: add source/fileHash/trackFilename/originalName and allow uploads (nullable activityId and ride)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE track_candidate ADD source VARCHAR(255) DEFAULT 'CANDIDATE_SOURCE_STRAVA' NOT NULL, ADD fileHash VARCHAR(40) DEFAULT NULL, ADD trackFilename VARCHAR(255) DEFAULT NULL, ADD originalName VARCHAR(255) DEFAULT NULL, CHANGE activityId activityId BIGINT DEFAULT NULL, CHANGE ride_id ride_id INT DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track_candidate DROP source, DROP fileHash, DROP trackFilename, DROP originalName, CHANGE activityId activityId BIGINT NOT NULL, CHANGE ride_id ride_id INT NOT NULL');
    }
}
