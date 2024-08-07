<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205184904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE help_category DROP FOREIGN KEY FK_89779DC1727ACA70');
        $this->addSql('ALTER TABLE help_item DROP FOREIGN KEY FK_5A91108512469DE2');
        $this->addSql('DROP TABLE help_category');
        $this->addSql('DROP TABLE help_item');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE help_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, user_id INT DEFAULT NULL, language VARCHAR(16) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, intro LONGTEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, side VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, position SMALLINT NOT NULL, INDEX IDX_89779DC1727ACA70 (parent_id), INDEX IDX_89779DC1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE help_item (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, text LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, position SMALLINT NOT NULL, INDEX IDX_5A91108512469DE2 (category_id), INDEX IDX_5A911085A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE help_category ADD CONSTRAINT FK_89779DC1727ACA70 FOREIGN KEY (parent_id) REFERENCES help_category (id)');
        $this->addSql('ALTER TABLE help_category ADD CONSTRAINT FK_89779DC1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE help_item ADD CONSTRAINT FK_5A91108512469DE2 FOREIGN KEY (category_id) REFERENCES help_category (id)');
        $this->addSql('ALTER TABLE help_item ADD CONSTRAINT FK_5A911085A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
    }
}
