<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304044623 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feed_item DROP FOREIGN KEY FK_9F8CCE49302A8A70');
        $this->addSql('ALTER TABLE feed_item DROP FOREIGN KEY FK_9F8CCE497B4822BF');
        $this->addSql('ALTER TABLE feed_item DROP FOREIGN KEY FK_9F8CCE498BAC62AF');
        $this->addSql('ALTER TABLE feed_item DROP FOREIGN KEY FK_9F8CCE49A76ED395');
        $this->addSql('DROP INDEX IDX_9F8CCE49A76ED395 ON feed_item');
        $this->addSql('DROP INDEX IDX_9F8CCE498BAC62AF ON feed_item');
        $this->addSql('DROP INDEX IDX_9F8CCE49302A8A70 ON feed_item');
        $this->addSql('DROP INDEX IDX_9F8CCE497B4822BF ON feed_item');
        $this->addSql('ALTER TABLE feed_item DROP user_id, DROP city_id, DROP ride_id, DROP subride_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feed_item ADD user_id INT DEFAULT NULL, ADD city_id INT DEFAULT NULL, ADD ride_id INT DEFAULT NULL, ADD subride_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE49302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE497B4822BF FOREIGN KEY (subride_id) REFERENCES subride (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE feed_item ADD CONSTRAINT FK_9F8CCE49A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_9F8CCE49A76ED395 ON feed_item (user_id)');
        $this->addSql('CREATE INDEX IDX_9F8CCE498BAC62AF ON feed_item (city_id)');
        $this->addSql('CREATE INDEX IDX_9F8CCE49302A8A70 ON feed_item (ride_id)');
        $this->addSql('CREATE INDEX IDX_9F8CCE497B4822BF ON feed_item (subride_id)');
    }
}
