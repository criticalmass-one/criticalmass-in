<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160817175805 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post_city (blog_post_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_41708D70A77FBEAF (blog_post_id), INDEX IDX_41708D708BAC62AF (city_id), PRIMARY KEY(blog_post_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_ride (blog_post_id INT NOT NULL, ride_id INT NOT NULL, INDEX IDX_F716F394A77FBEAF (blog_post_id), INDEX IDX_F716F394302A8A70 (ride_id), PRIMARY KEY(blog_post_id, ride_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post_city ADD CONSTRAINT FK_41708D70A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_city ADD CONSTRAINT FK_41708D708BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE blog_post_ride ADD CONSTRAINT FK_F716F394A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_ride ADD CONSTRAINT FK_F716F394302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog_post_city');
        $this->addSql('DROP TABLE blog_post_ride');
    }
}
