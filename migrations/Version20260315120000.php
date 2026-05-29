<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add feeds_profile_id to social_network_profile and drop social_network_feed_item table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social_network_profile ADD feeds_profile_id INT DEFAULT NULL');

        $this->addSql('DROP TABLE IF EXISTS social_network_feed_item');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social_network_profile DROP COLUMN feeds_profile_id');

        $this->addSql('CREATE TABLE social_network_feed_item (
            id INT AUTO_INCREMENT NOT NULL,
            social_network_profile_id INT NOT NULL,
            unique_identifier VARCHAR(255) NOT NULL,
            permalink LONGTEXT DEFAULT NULL,
            title LONGTEXT DEFAULT NULL,
            text LONGTEXT NOT NULL,
            date_time DATETIME NOT NULL,
            hidden TINYINT(1) NOT NULL,
            deleted TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            raw LONGTEXT DEFAULT NULL,
            INDEX IDX_social_network_feed_item_date_time (date_time),
            INDEX IDX_social_network_feed_item_created_at (created_at),
            UNIQUE INDEX unique_feed_item (social_network_profile_id, unique_identifier),
            INDEX IDX_social_network_profile_id (social_network_profile_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE social_network_feed_item ADD CONSTRAINT FK_social_network_feed_item_profile FOREIGN KEY (social_network_profile_id) REFERENCES social_network_profile (id)');
    }
}
