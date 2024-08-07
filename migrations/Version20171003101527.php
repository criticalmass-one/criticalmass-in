<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171003101527 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subride ADD updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE creationdatetime createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE subride_audit ADD updatedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE creationdatetime createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subride DROP updatedAt, CHANGE createdat creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE subride_audit ADD creationDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', DROP createdAt, DROP updatedAt');
    }
}
