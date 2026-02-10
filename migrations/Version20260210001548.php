<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260210001548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove ViewStorage tables and views columns (PR #1180)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS city_view');
        $this->addSql('DROP TABLE IF EXISTS photo_view');
        $this->addSql('DROP TABLE IF EXISTS promotion_view');
        $this->addSql('DROP TABLE IF EXISTS ride_view');
        $this->addSql('DROP TABLE IF EXISTS thread_view');

        $this->addSql('ALTER TABLE city DROP views');
        $this->addSql('ALTER TABLE photo DROP views');
        $this->addSql('ALTER TABLE promotion DROP views');
        $this->addSql('ALTER TABLE ride DROP views');
        $this->addSql('ALTER TABLE thread DROP views');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE city ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE photo ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE promotion ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE ride ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE thread ADD views INT NOT NULL DEFAULT 0');

        $this->addSql('CREATE TABLE city_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, dateTime DATETIME DEFAULT NULL, INDEX IDX_613A48FBA76ED395 (user_id), INDEX IDX_613A48FB8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, photo_id INT DEFAULT NULL, dateTime DATETIME DEFAULT NULL, INDEX IDX_5765B3D5A76ED395 (user_id), INDEX IDX_5765B3D57E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, promotion_id INT DEFAULT NULL, dateTime DATETIME DEFAULT NULL, INDEX IDX_B964E783A76ED395 (user_id), INDEX IDX_B964E783139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ride_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, dateTime DATETIME DEFAULT NULL, INDEX IDX_4E954B2CA76ED395 (user_id), INDEX IDX_4E954B2C302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, thread_id INT DEFAULT NULL, dateTime DATETIME DEFAULT NULL, INDEX IDX_75F2AB9BA76ED395 (user_id), INDEX IDX_75F2AB9BE2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE city_view ADD CONSTRAINT FK_613A48FBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE city_view ADD CONSTRAINT FK_613A48FB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE photo_view ADD CONSTRAINT FK_5765B3D5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE photo_view ADD CONSTRAINT FK_5765B3D57E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE promotion_view ADD CONSTRAINT FK_B964E783A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE promotion_view ADD CONSTRAINT FK_B964E783139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE ride_view ADD CONSTRAINT FK_4E954B2CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ride_view ADD CONSTRAINT FK_4E954B2C302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE thread_view ADD CONSTRAINT FK_75F2AB9BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE thread_view ADD CONSTRAINT FK_75F2AB9BE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
    }
}
