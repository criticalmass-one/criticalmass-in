<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160806203554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride ADD featured_photo INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ride ADD CONSTRAINT FK_9B3D7CD04DFE7F82 FOREIGN KEY (featured_photo) REFERENCES photo (id)');
        $this->addSql('CREATE INDEX IDX_9B3D7CD04DFE7F82 ON ride (featured_photo)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride DROP FOREIGN KEY FK_9B3D7CD04DFE7F82');
        $this->addSql('DROP INDEX IDX_9B3D7CD04DFE7F82 ON ride');
        $this->addSql('ALTER TABLE ride DROP featured_photo');
    }
}
