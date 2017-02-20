<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161024224038 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE position CHANGE accuracy accuracy DOUBLE PRECISION DEFAULT NULL, CHANGE altitude altitude DOUBLE PRECISION DEFAULT NULL, CHANGE altitudeAccuracy altitudeAccuracy DOUBLE PRECISION DEFAULT NULL, CHANGE heading heading DOUBLE PRECISION DEFAULT NULL, CHANGE speed speed DOUBLE PRECISION DEFAULT NULL, CHANGE timestamp timestamp BIGINT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE position CHANGE accuracy accuracy DOUBLE PRECISION NOT NULL, CHANGE altitude altitude DOUBLE PRECISION NOT NULL, CHANGE altitudeAccuracy altitudeAccuracy DOUBLE PRECISION NOT NULL, CHANGE heading heading DOUBLE PRECISION NOT NULL, CHANGE speed speed DOUBLE PRECISION NOT NULL, CHANGE timestamp timestamp BIGINT NOT NULL');
    }
}
