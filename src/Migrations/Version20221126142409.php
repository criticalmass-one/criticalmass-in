<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221126142409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE subride');
        $this->addSql('ALTER TABLE social_network_profile DROP subride_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subride (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, dateTime DATETIME NOT NULL, createdAt DATETIME NOT NULL, location VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, updatedAt DATETIME DEFAULT \'NULL\', INDEX IDX_42880E5BA76ED395 (user_id), INDEX IDX_42880E5B302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subride ADD CONSTRAINT FK_42880E5B302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE subride ADD CONSTRAINT FK_42880E5BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD subride_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE city_id city_id INT DEFAULT NULL, CHANGE ride_id ride_id INT DEFAULT NULL, CHANGE createdAt createdAt DATETIME DEFAULT \'NULL\', CHANGE lastFetchSuccessDateTime lastFetchSuccessDateTime DATETIME DEFAULT \'NULL\', CHANGE lastFetchFailureDateTime lastFetchFailureDateTime DATETIME DEFAULT \'NULL\', CHANGE createdBy_id createdBy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE67B4822BF FOREIGN KEY (subride_id) REFERENCES subride (id)');
        $this->addSql('CREATE INDEX IDX_3AC92AE67B4822BF ON social_network_profile (subride_id)');
    }
}
