<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907161536 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE track_candidate (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ride_id INT NOT NULL, activityId BIGINT NOT NULL, name VARCHAR(255) NOT NULL, distance DOUBLE PRECISION NOT NULL, elapsedTime INT NOT NULL, type VARCHAR(255) NOT NULL, startDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', startLatitude DOUBLE PRECISION NOT NULL, startLongitude DOUBLE PRECISION NOT NULL, endLatitude DOUBLE PRECISION NOT NULL, endLongitude DOUBLE PRECISION NOT NULL, polyline TEXT NOT NULL, createdAt DATETIME NOT NULL, rejected TINYINT(1) NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_E48215EBA76ED395 (user_id), INDEX IDX_E48215EB302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE track_candidate ADD CONSTRAINT FK_E48215EBA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE track_candidate ADD CONSTRAINT FK_E48215EB302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE track_candidate');
    }
}
