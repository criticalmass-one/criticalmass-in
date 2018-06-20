<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304133226 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE social_network_feed_item (id INT AUTO_INCREMENT NOT NULL, social_network_profile_id INT DEFAULT NULL, uniqueIdentifier VARCHAR(255) NOT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_FD88BE5246FDDF24 (social_network_profile_id), UNIQUE INDEX unique_feed_item (social_network_profile_id, uniqueIdentifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE social_network_feed_item ADD CONSTRAINT FK_FD88BE5246FDDF24 FOREIGN KEY (social_network_profile_id) REFERENCES social_network_profile (id)');
        $this->addSql('DROP TABLE feed_item');
        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE68BAC62AF');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feed_item (id INT AUTO_INCREMENT NOT NULL, social_network_profile_id INT DEFAULT NULL, uniqueIdentifier VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX unique_feed_item (social_network_profile_id, uniqueIdentifier), INDEX IDX_9F8CCE4946FDDF24 (social_network_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE4946FDDF24 FOREIGN KEY (social_network_profile_id) REFERENCES social_network_profile (id)');
        $this->addSql('DROP TABLE social_network_feed_item');
        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE68BAC62AF');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES photo (id)');
    }
}
