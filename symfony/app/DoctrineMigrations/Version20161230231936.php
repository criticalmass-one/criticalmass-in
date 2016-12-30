<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161230231936 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DDAE07E97');
        $this->addSql('ALTER TABLE blog_post_city DROP FOREIGN KEY FK_41708D70A77FBEAF');
        $this->addSql('ALTER TABLE blog_post_ride DROP FOREIGN KEY FK_F716F394A77FBEAF');
        $this->addSql('ALTER TABLE blog_post_view DROP FOREIGN KEY FK_92D624CAA77FBEAF');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA77FBEAF');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_post_city');
        $this->addSql('DROP TABLE blog_post_ride');
        $this->addSql('DROP TABLE blog_post_view');
        $this->addSql('ALTER TABLE post DROP blog_post_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, hostname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, featured_photo INT DEFAULT NULL, user_id INT DEFAULT NULL, blog_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, abstract LONGTEXT NOT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, views INT NOT NULL, showCoffeeButton TINYINT(1) NOT NULL, commentsEnabled TINYINT(1) NOT NULL, claim VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_BA5AE01DA76ED395 (user_id), INDEX IDX_BA5AE01DDAE07E97 (blog_id), INDEX IDX_BA5AE01D4DFE7F82 (featured_photo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_city (blog_post_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_41708D70A77FBEAF (blog_post_id), INDEX IDX_41708D708BAC62AF (city_id), PRIMARY KEY(blog_post_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_ride (blog_post_id INT NOT NULL, ride_id INT NOT NULL, INDEX IDX_F716F394A77FBEAF (blog_post_id), INDEX IDX_F716F394302A8A70 (ride_id), PRIMARY KEY(blog_post_id, ride_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, blog_post_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_92D624CAA76ED395 (user_id), INDEX IDX_92D624CAA77FBEAF (blog_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D4DFE7F82 FOREIGN KEY (featured_photo) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_post_city ADD CONSTRAINT FK_41708D708BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE blog_post_city ADD CONSTRAINT FK_41708D70A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_ride ADD CONSTRAINT FK_F716F394302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE blog_post_ride ADD CONSTRAINT FK_F716F394A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_view ADD CONSTRAINT FK_92D624CAA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE blog_post_view ADD CONSTRAINT FK_92D624CAA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE opening_time CHANGE weekday weekday SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE post ADD blog_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA77FBEAF ON post (blog_post_id)');
    }
}
