<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220429170618 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth_accesstoken DROP FOREIGN KEY FK_A8EA1B8D19EB6921');
        $this->addSql('ALTER TABLE oauth_authcode DROP FOREIGN KEY FK_88EF9F0319EB6921');
        $this->addSql('ALTER TABLE oauth_refreshbintoken DROP FOREIGN KEY FK_4F1710EB19EB6921');
        $this->addSql('DROP TABLE oauth_accesstoken');
        $this->addSql('DROP TABLE oauth_authcode');
        $this->addSql('DROP TABLE oauth_client');
        $this->addSql('DROP TABLE oauth_refreshbintoken');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_accesstoken (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, INDEX IDX_A8EA1B8DA76ED395 (user_id), UNIQUE INDEX UNIQ_A8EA1B8D5F37A13B (token), INDEX IDX_A8EA1B8D19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE oauth_authcode (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, redirect_uri LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, INDEX IDX_88EF9F03A76ED395 (user_id), UNIQUE INDEX UNIQ_88EF9F035F37A13B (token), INDEX IDX_88EF9F0319EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE oauth_client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, redirect_uris LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci` COMMENT \'(DC2Type:array)\', secret VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, allowed_grant_types LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci` COMMENT \'(DC2Type:array)\', name VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, createdAt DATETIME DEFAULT \'NULL\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE oauth_refreshbintoken (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT \'NULL\' COLLATE `utf8mb3_unicode_ci`, INDEX IDX_4F1710EBA76ED395 (user_id), UNIQUE INDEX UNIQ_4F1710EB5F37A13B (token), INDEX IDX_4F1710EB19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE oauth_accesstoken ADD CONSTRAINT FK_A8EA1B8D19EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_accesstoken ADD CONSTRAINT FK_A8EA1B8DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth_authcode ADD CONSTRAINT FK_88EF9F0319EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_authcode ADD CONSTRAINT FK_88EF9F03A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth_refreshbintoken ADD CONSTRAINT FK_4F1710EB19EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_refreshbintoken ADD CONSTRAINT FK_4F1710EBA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE');
    }
}
