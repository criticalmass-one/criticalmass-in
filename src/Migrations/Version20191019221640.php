<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191019221640 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog_post_audit');
        $this->addSql('DROP TABLE city_audit');
        $this->addSql('DROP TABLE city_cycle_audit');
        $this->addSql('DROP TABLE help_category_audit');
        $this->addSql('DROP TABLE help_item_audit');
        $this->addSql('DROP TABLE location_audit');
        $this->addSql('DROP TABLE region_audit');
        $this->addSql('DROP TABLE ride_audit');
        $this->addSql('DROP TABLE social_network_feed_item_audit');
        $this->addSql('DROP TABLE social_network_profile_audit');
        $this->addSql('DROP TABLE subride_audit');
        $this->addSql('DROP TABLE revisions');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
