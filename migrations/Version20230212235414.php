<?php declare(strict_types=1);

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212235414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX city_created_at_index ON city (createdAt)');
        $this->addSql('CREATE INDEX photo_exif_creation_date_index ON photo (exifCreationDate)');
        $this->addSql('CREATE INDEX post_date_time_index ON post (dateTime)');
        $this->addSql('CREATE INDEX ride_date_time_index ON ride (dateTime)');
        $this->addSql('CREATE INDEX ride_created_at_index ON ride (createdAt)');
        $this->addSql('CREATE INDEX ride_updated_at_index ON ride (updatedAt)');
        $this->addSql('CREATE INDEX ride_estimate_date_time_index ON ride_estimate (dateTime)');
        $this->addSql('CREATE INDEX social_network_feed_item_date_time_index ON social_network_feed_item (dateTime)');
        $this->addSql('CREATE INDEX social_network_feed_item_created_at_index ON social_network_feed_item (createdAt)');
        $this->addSql('CREATE INDEX track_creation_date_time_index ON track (creationDateTime)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX city_created_at_index ON city');
        $this->addSql('DROP INDEX photo_exif_creation_date_index ON photo');
        $this->addSql('DROP INDEX post_date_time_index ON post');
        $this->addSql('DROP INDEX ride_date_time_index ON ride');
        $this->addSql('DROP INDEX ride_created_at_index ON ride');
        $this->addSql('DROP INDEX ride_updated_at_index ON ride');
        $this->addSql('DROP INDEX ride_estimate_date_time_index ON ride_estimate');
        $this->addSql('DROP INDEX social_network_feed_item_date_time_index ON social_network_feed_item');
        $this->addSql('DROP INDEX social_network_feed_item_created_at_index ON social_network_feed_item');
        $this->addSql('DROP INDEX track_creation_date_time_index ON track');
    }
}
