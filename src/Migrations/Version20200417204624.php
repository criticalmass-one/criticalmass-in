<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200417204624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE page__page (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, target_id INT DEFAULT NULL, route_name VARCHAR(255) NOT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, decorate TINYINT(1) NOT NULL, edited TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, slug LONGTEXT DEFAULT NULL, url LONGTEXT DEFAULT NULL, custom_url LONGTEXT DEFAULT NULL, request_method VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, meta_keyword VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, javascript LONGTEXT DEFAULT NULL, stylesheet LONGTEXT DEFAULT NULL, raw_headers LONGTEXT DEFAULT NULL, template VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2FAE39EDF6BD1646 (site_id), INDEX IDX_2FAE39ED727ACA70 (parent_id), INDEX IDX_2FAE39ED158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page__block (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, page_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, settings JSON NOT NULL, enabled TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_66F58DA0727ACA70 (parent_id), INDEX IDX_66F58DA0C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page__site (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, relative_path VARCHAR(255) DEFAULT NULL, host VARCHAR(255) NOT NULL, enabled_from DATETIME DEFAULT NULL, enabled_to DATETIME DEFAULT NULL, is_default TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, locale VARCHAR(7) DEFAULT NULL, title VARCHAR(64) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page__snapshot (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, page_id INT DEFAULT NULL, route_name VARCHAR(255) NOT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, decorate TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, url LONGTEXT DEFAULT NULL, parent_id INT DEFAULT NULL, target_id INT DEFAULT NULL, content JSON DEFAULT NULL, publication_date_start DATETIME DEFAULT NULL, publication_date_end DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3963EF9AF6BD1646 (site_id), INDEX IDX_3963EF9AC4663E4 (page_id), INDEX idx_snapshot_dates_enabled (publication_date_start, publication_date_end, enabled), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39EDF6BD1646 FOREIGN KEY (site_id) REFERENCES page__site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39ED727ACA70 FOREIGN KEY (parent_id) REFERENCES page__page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39ED158E0B66 FOREIGN KEY (target_id) REFERENCES page__page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__block ADD CONSTRAINT FK_66F58DA0727ACA70 FOREIGN KEY (parent_id) REFERENCES page__block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__block ADD CONSTRAINT FK_66F58DA0C4663E4 FOREIGN KEY (page_id) REFERENCES page__page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__snapshot ADD CONSTRAINT FK_3963EF9AF6BD1646 FOREIGN KEY (site_id) REFERENCES page__site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page__snapshot ADD CONSTRAINT FK_3963EF9AC4663E4 FOREIGN KEY (page_id) REFERENCES page__page (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39ED727ACA70');
        $this->addSql('ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39ED158E0B66');
        $this->addSql('ALTER TABLE page__block DROP FOREIGN KEY FK_66F58DA0C4663E4');
        $this->addSql('ALTER TABLE page__snapshot DROP FOREIGN KEY FK_3963EF9AC4663E4');
        $this->addSql('ALTER TABLE page__block DROP FOREIGN KEY FK_66F58DA0727ACA70');
        $this->addSql('ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39EDF6BD1646');
        $this->addSql('ALTER TABLE page__snapshot DROP FOREIGN KEY FK_3963EF9AF6BD1646');
        $this->addSql('DROP TABLE page__page');
        $this->addSql('DROP TABLE page__block');
        $this->addSql('DROP TABLE page__site');
        $this->addSql('DROP TABLE page__snapshot');
    }
}
