<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171006100702 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE frontpage_teaser (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, headline LONGTEXT DEFAULT NULL, text DOUBLE PRECISION DEFAULT NULL, imageName VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validFrom DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', validUntil DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_26FF0B97A76ED395 (user_id), INDEX IDX_26FF0B978BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE frontpage_teaser ADD CONSTRAINT FK_26FF0B97A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE frontpage_teaser ADD CONSTRAINT FK_26FF0B978BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE frontpage_teaser');
    }
}
