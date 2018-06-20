<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180221183729 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE social_network_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, network VARCHAR(255) NOT NULL, mainNetwork TINYINT(1) NOT NULL, INDEX IDX_3AC92AE6A76ED395 (user_id), INDEX IDX_3AC92AE68BAC62AF (city_id), INDEX IDX_3AC92AE6302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_network_profile_audit (id INT NOT NULL, rev INT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, network VARCHAR(255) DEFAULT NULL, mainNetwork TINYINT(1) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_007a511a50694d1018203227f808b40b_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE6A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE6302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE social_network_profile');
        $this->addSql('DROP TABLE social_network_profile_audit');
    }
}
