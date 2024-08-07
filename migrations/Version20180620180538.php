<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620180538 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE social_network_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, subride_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, network VARCHAR(255) NOT NULL, mainNetwork TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_3AC92AE6A76ED395 (user_id), INDEX IDX_3AC92AE68BAC62AF (city_id), INDEX IDX_3AC92AE6302A8A70 (ride_id), INDEX IDX_3AC92AE67B4822BF (subride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_network_profile_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, subride_id INT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, network VARCHAR(255) DEFAULT NULL, mainNetwork TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_007a511a50694d1018203227f808b40b_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_network_feed_item (id INT AUTO_INCREMENT NOT NULL, social_network_profile_id INT DEFAULT NULL, uniqueIdentifier VARCHAR(255) NOT NULL, permalink LONGTEXT DEFAULT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_FD88BE5246FDDF24 (social_network_profile_id), UNIQUE INDEX unique_feed_item (social_network_profile_id, uniqueIdentifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_network_feed_item_audit (id INT NOT NULL, rev INT NOT NULL, social_network_profile_id INT DEFAULT NULL, uniqueIdentifier VARCHAR(255) DEFAULT NULL, permalink LONGTEXT DEFAULT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, dateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', hidden TINYINT(1) DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', revtype VARCHAR(4) NOT NULL, INDEX rev_6da6328f39e9d0c4d22ef88de03720d9_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE6A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE6302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE67B4822BF FOREIGN KEY (subride_id) REFERENCES subride (id)');
        $this->addSql('ALTER TABLE social_network_feed_item ADD CONSTRAINT FK_FD88BE5246FDDF24 FOREIGN KEY (social_network_profile_id) REFERENCES social_network_profile (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_feed_item DROP FOREIGN KEY FK_FD88BE5246FDDF24');
        $this->addSql('DROP TABLE social_network_profile');
        $this->addSql('DROP TABLE social_network_profile_audit');
        $this->addSql('DROP TABLE social_network_feed_item');
        $this->addSql('DROP TABLE social_network_feed_item_audit');
    }
}
