<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304131219 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE68BAC62AF');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE social_network_profile DROP FOREIGN KEY FK_3AC92AE68BAC62AF');
        $this->addSql('ALTER TABLE social_network_profile ADD CONSTRAINT FK_3AC92AE68BAC62AF FOREIGN KEY (city_id) REFERENCES photo (id)');
    }
}
