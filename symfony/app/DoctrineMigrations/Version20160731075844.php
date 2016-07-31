<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160731075844 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track CHANGE startDateTime startDateTime DATETIME DEFAULT NULL, CHANGE endDateTime endDateTime DATETIME DEFAULT NULL, CHANGE distance distance DOUBLE PRECISION DEFAULT NULL, CHANGE points points INT DEFAULT NULL, CHANGE md5Hash md5Hash VARCHAR(32) DEFAULT NULL, CHANGE startPoint startPoint INT DEFAULT NULL, CHANGE endPoint endPoint INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track CHANGE startDateTime startDateTime DATETIME NOT NULL, CHANGE endDateTime endDateTime DATETIME NOT NULL, CHANGE distance distance DOUBLE PRECISION NOT NULL, CHANGE points points INT NOT NULL, CHANGE startPoint startPoint INT NOT NULL, CHANGE endPoint endPoint INT NOT NULL, CHANGE md5Hash md5Hash VARCHAR(32) NOT NULL');
    }
}
