<?php declare(strict_types=1);

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203211015 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user CHANGE facebook_access_token facebook_access_token LONGTEXT DEFAULT NULL, CHANGE strava_access_token strava_access_token LONGTEXT DEFAULT NULL, CHANGE twitter_access_token twitter_access_token LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_user CHANGE facebook_access_token facebook_access_token VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, CHANGE strava_access_token strava_access_token VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, CHANGE twitter_access_token twitter_access_token VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`');
    }
}
