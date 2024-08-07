<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023182438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE heatmap_track DROP FOREIGN KEY FK_35919BD7D12F42C3');
        $this->addSql('DROP TABLE heatmap');
        $this->addSql('DROP TABLE heatmap_track');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE heatmap (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, city_id INT DEFAULT NULL, identifier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_51EF598AA76ED395 (user_id), UNIQUE INDEX UNIQ_51EF598A302A8A70 (ride_id), UNIQUE INDEX UNIQ_51EF598A8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE heatmap_track (id INT AUTO_INCREMENT NOT NULL, heatmap_id INT NOT NULL, track_id INT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_35919BD7D12F42C3 (heatmap_id), INDEX IDX_35919BD75ED23C43 (track_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE heatmap ADD CONSTRAINT FK_51EF598A302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE heatmap ADD CONSTRAINT FK_51EF598A8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE heatmap ADD CONSTRAINT FK_51EF598AA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE heatmap_track ADD CONSTRAINT FK_35919BD75ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE heatmap_track ADD CONSTRAINT FK_35919BD7D12F42C3 FOREIGN KEY (heatmap_id) REFERENCES heatmap (id)');
    }
}
