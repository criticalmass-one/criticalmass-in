<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160530214817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, dateTime DATETIME NOT NULL, INDEX IDX_1DB396E6A76ED395 (user_id), INDEX IDX_1DB396E68BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, dateTime DATETIME NOT NULL, INDEX IDX_E136E725A76ED395 (user_id), INDEX IDX_E136E72571F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ride_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, dateTime DATETIME NOT NULL, INDEX IDX_B5C29EFAA76ED395 (user_id), INDEX IDX_B5C29EFA302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_view ADD CONSTRAINT FK_1DB396E6A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE city_view ADD CONSTRAINT FK_1DB396E68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE event_view ADD CONSTRAINT FK_E136E725A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE event_view ADD CONSTRAINT FK_E136E72571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE ride_view ADD CONSTRAINT FK_B5C29EFAA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE ride_view ADD CONSTRAINT FK_B5C29EFA302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE thread CHANGE viewnumber views INT NOT NULL');
        $this->addSql('ALTER TABLE city ADD views INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD views INT NOT NULL');
        $this->addSql('ALTER TABLE ride ADD views INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city_view');
        $this->addSql('DROP TABLE event_view');
        $this->addSql('DROP TABLE ride_view');
        $this->addSql('ALTER TABLE city DROP views');
        $this->addSql('ALTER TABLE event DROP views');
        $this->addSql('ALTER TABLE ride DROP views');
        $this->addSql('ALTER TABLE thread CHANGE views viewNumber INT NOT NULL');
    }
}
