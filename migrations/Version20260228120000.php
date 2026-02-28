<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260228120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make social_network_profile_id NOT NULL on social_network_feed_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM social_network_feed_item WHERE social_network_profile_id IS NULL');
        $this->addSql('ALTER TABLE social_network_feed_item CHANGE social_network_profile_id social_network_profile_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE social_network_feed_item CHANGE social_network_profile_id social_network_profile_id INT DEFAULT NULL');
    }
}
