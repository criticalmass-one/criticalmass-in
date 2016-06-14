<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160614203120 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, notifyByMail TINYINT(1) NOT NULL, notifyByPushover TINYINT(1) NOT NULL, notifyByShortmessage TINYINT(1) NOT NULL, notifyOnChange TINYINT(1) NOT NULL, notifyOnCreate TINYINT(1) NOT NULL, notifyOnSpecial TINYINT(1) NOT NULL, notifyOnActivity TINYINT(1) NOT NULL, event_id INT DEFAULT NULL, INDEX IDX_BF5476CA8BAC62AF (city_id), INDEX IDX_BF5476CA302A8A70 (ride_id), INDEX IDX_BF5476CA71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE notification');
    }
}
