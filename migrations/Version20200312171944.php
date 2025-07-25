<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312171944 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promotion_view (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, photo_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_48072311A76ED395 (user_id), INDEX IDX_480723117E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promotion_view ADD CONSTRAINT FK_48072311A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE promotion_view ADD CONSTRAINT FK_480723117E9E4C8C FOREIGN KEY (photo_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE promotion ADD views INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE promotion_view');
        $this->addSql('ALTER TABLE promotion DROP views');
    }
}
