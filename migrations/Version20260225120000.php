<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert database and all tables from utf8 to utf8mb4 to support emoji characters';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER DATABASE DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $tables = [
            'alert',
            'board',
            'city',
            'city_activity',
            'city_blocked',
            'city_cycle',
            'cityslug',
            'frontpage_teaser',
            'frontpage_teaser_button',
            'location',
            'participation',
            'photo',
            'post',
            'promotion',
            'region',
            'ride',
            'ride_estimate',
            'social_network_feed_item',
            'social_network_profile',
            'subride',
            'thread',
            'track',
            'track_candidate',
            'track_polyline',
            'user',
            'weather',
        ];

        foreach ($tables as $table) {
            $this->addSql(sprintf(
                'ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                $table
            ));
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        $tables = [
            'alert',
            'board',
            'city',
            'city_activity',
            'city_blocked',
            'city_cycle',
            'cityslug',
            'frontpage_teaser',
            'frontpage_teaser_button',
            'location',
            'participation',
            'photo',
            'post',
            'promotion',
            'region',
            'ride',
            'ride_estimate',
            'social_network_feed_item',
            'social_network_profile',
            'subride',
            'thread',
            'track',
            'track_candidate',
            'track_polyline',
            'user',
            'weather',
        ];

        foreach ($tables as $table) {
            $this->addSql(sprintf(
                'ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci',
                $table
            ));
        }
    }
}
