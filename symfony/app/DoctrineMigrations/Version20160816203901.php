<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160816203901 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, blog_id INT DEFAULT NULL, dateTime DATETIME NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, abstract LONGTEXT NOT NULL, text LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_BA5AE01DA76ED395 (user_id), INDEX IDX_BA5AE01DDAE07E97 (blog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('DROP TABLE article');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DDAE07E97');
        $this->addSql('DROP INDEX IDX_5A8A6C8DDAE07E97 ON post');
        $this->addSql('ALTER TABLE post DROP blog_id, DROP title, DROP abstract');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, abstract LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, dateTime DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_23A0E66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('ALTER TABLE post ADD blog_id INT DEFAULT NULL, ADD title LONGTEXT NOT NULL, ADD abstract LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DDAE07E97 ON post (blog_id)');
    }
}
