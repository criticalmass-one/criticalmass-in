<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304032527 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feed_item (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, subride_id INT DEFAULT NULL, social_network_profile_id INT DEFAULT NULL, uniqueIdentifier LONGTEXT NOT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_9F8CCE49A76ED395 (user_id), INDEX IDX_9F8CCE498BAC62AF (city_id), INDEX IDX_9F8CCE49302A8A70 (ride_id), INDEX IDX_9F8CCE497B4822BF (subride_id), INDEX IDX_9F8CCE4946FDDF24 (social_network_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE49A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE49302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE497B4822BF FOREIGN KEY (subride_id) REFERENCES subride (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE4946FDDF24 FOREIGN KEY (social_network_profile_id) REFERENCES social_network_profile (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE feed_item');
    }
}
