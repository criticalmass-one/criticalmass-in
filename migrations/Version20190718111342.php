<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190718111342 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, blog_id INT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', enabled TINYINT(1) NOT NULL, text LONGTEXT DEFAULT NULL, intro LONGTEXT DEFAULT NULL, imageName VARCHAR(255) DEFAULT NULL, imageSize INT DEFAULT NULL, imageMimeType VARCHAR(255) DEFAULT NULL, INDEX IDX_BA5AE01DDAE07E97 (blog_id), INDEX IDX_BA5AE01DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, postNumber INT NOT NULL, enabled TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, intro LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE post ADD blogPost_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DE84878F2 FOREIGN KEY (blogPost_id) REFERENCES blog_post (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DE84878F2 ON post (blogPost_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DE84878F2');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DDAE07E97');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP INDEX IDX_5A8A6C8DE84878F2 ON post');
        $this->addSql('ALTER TABLE post DROP blogPost_id');
    }
}
