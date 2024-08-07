<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20171003102749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE ride SET archive_parent_id = NULL');
        $this->addSql('UPDATE photo AS p JOIN ride AS r ON r.id = p.ride_id SET p.ride_id = NULL WHERE r.isArchived = 1');
        $this->addSql('DELETE rv.* FROM ride AS r JOIN ride_view AS rv ON r.id = rv.ride_id WHERE r.isArchived = 1');
        $this->addSql('DELETE w.* FROM ride AS r JOIN weather AS w ON r.id = w.ride_id WHERE r.isArchived = 1');
        $this->addSql('DELETE frp.* FROM ride AS r JOIN facebook_ride_properties AS frp ON r.id = frp.ride_id WHERE r.isArchived = 1');
        $this->addSql('DELETE p.* FROM ride AS r JOIN participation AS p ON r.id = p.ride_id WHERE r.isArchived = 1');
        $this->addSql('DELETE re.* FROM ride AS r JOIN ride_estimate AS re ON r.id = re.ride_id WHERE r.isArchived = 1');
        $this->addSql('DELETE FROM city WHERE isArchived = 1');
        $this->addSql('DELETE FROM ride WHERE isArchived = 1');
        $this->addSql('DELETE FROM subride WHERE isArchived = 1');

        $this->addSql('ALTER TABLE subride DROP FOREIGN KEY FK_42880E5B365388CC');
        $this->addSql('ALTER TABLE subride DROP FOREIGN KEY FK_42880E5BCA4E326A');
        $this->addSql('DROP INDEX IDX_42880E5B365388CC ON subride');
        $this->addSql('DROP INDEX IDX_42880E5BCA4E326A ON subride');
        $this->addSql('ALTER TABLE subride DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
        $this->addSql('ALTER TABLE subride_audit DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234365388CC');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234CA4E326A');
        $this->addSql('DROP INDEX IDX_2D5B0234365388CC ON city');
        $this->addSql('DROP INDEX IDX_2D5B0234CA4E326A ON city');
        $this->addSql('ALTER TABLE city DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
        $this->addSql('ALTER TABLE city_audit DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
        $this->addSql('ALTER TABLE ride DROP FOREIGN KEY FK_9B3D7CD0365388CC');
        $this->addSql('ALTER TABLE ride DROP FOREIGN KEY FK_9B3D7CD0CA4E326A');
        $this->addSql('DROP INDEX IDX_9B3D7CD0365388CC ON ride');
        $this->addSql('DROP INDEX IDX_9B3D7CD0CA4E326A ON ride');
        $this->addSql('ALTER TABLE ride DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
        $this->addSql('ALTER TABLE ride_audit DROP archive_parent_id, DROP archive_user_id, DROP isArchived, DROP archiveDateTime, DROP archiveMessage');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) NOT NULL, ADD archiveDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234365388CC FOREIGN KEY (archive_parent_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234CA4E326A FOREIGN KEY (archive_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_2D5B0234365388CC ON city (archive_parent_id)');
        $this->addSql('CREATE INDEX IDX_2D5B0234CA4E326A ON city (archive_user_id)');
        $this->addSql('ALTER TABLE city_audit ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) DEFAULT NULL, ADD archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ride ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) NOT NULL, ADD archiveDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ride ADD CONSTRAINT FK_9B3D7CD0365388CC FOREIGN KEY (archive_parent_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE ride ADD CONSTRAINT FK_9B3D7CD0CA4E326A FOREIGN KEY (archive_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_9B3D7CD0365388CC ON ride (archive_parent_id)');
        $this->addSql('CREATE INDEX IDX_9B3D7CD0CA4E326A ON ride (archive_user_id)');
        $this->addSql('ALTER TABLE ride_audit ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) DEFAULT NULL, ADD archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE subride ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) NOT NULL, ADD archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE subride ADD CONSTRAINT FK_42880E5B365388CC FOREIGN KEY (archive_parent_id) REFERENCES subride (id)');
        $this->addSql('ALTER TABLE subride ADD CONSTRAINT FK_42880E5BCA4E326A FOREIGN KEY (archive_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_42880E5B365388CC ON subride (archive_parent_id)');
        $this->addSql('CREATE INDEX IDX_42880E5BCA4E326A ON subride (archive_user_id)');
        $this->addSql('ALTER TABLE subride_audit ADD archive_parent_id INT DEFAULT NULL, ADD archive_user_id INT DEFAULT NULL, ADD isArchived TINYINT(1) DEFAULT NULL, ADD archiveDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
