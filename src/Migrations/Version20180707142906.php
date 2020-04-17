<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180707142906 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track ADD estimate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A685F23082 FOREIGN KEY (estimate_id) REFERENCES ride_estimate (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D6E3F8A685F23082 ON track (estimate_id)');
        $this->addSql('UPDATE track AS t SET t.estimate_id = (SELECT re.id FROM ride_estimate AS re WHERE re.track_id = t.id);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A685F23082');
        $this->addSql('DROP INDEX UNIQ_D6E3F8A685F23082 ON track');
        $this->addSql('ALTER TABLE track DROP estimate_id');
    }
}
