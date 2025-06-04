<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805192317 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post_audit (id INT NOT NULL, rev INT NOT NULL, blog_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', enabled TINYINT(1) DEFAULT NULL, text LONGTEXT DEFAULT NULL, intro LONGTEXT DEFAULT NULL, imageName VARCHAR(255) DEFAULT NULL, imageSize INT DEFAULT NULL, imageMimeType VARCHAR(255) DEFAULT NULL, views INT DEFAULT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_06391e1192016e4cfc173d0e7187cca2_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog_post_audit');
    }
}
