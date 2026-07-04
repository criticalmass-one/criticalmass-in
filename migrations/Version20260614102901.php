<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260614102901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create OAuth2 server tables (league/oauth2-server-bundle): client, access token, refresh token, authorization code';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, expiry DATETIME NOT NULL, userIdentifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked TINYINT NOT NULL, client VARCHAR(32) NOT NULL, INDEX IDX_454D9673C7440455 (client), PRIMARY KEY (identifier)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, expiry DATETIME NOT NULL, userIdentifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked TINYINT NOT NULL, client VARCHAR(32) NOT NULL, INDEX IDX_509FEF5FC7440455 (client), PRIMARY KEY (identifier)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oauth2_client (name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirectUris TEXT DEFAULT NULL, grants TEXT DEFAULT NULL, scopes TEXT DEFAULT NULL, active TINYINT NOT NULL, allowPlainTextPkce TINYINT DEFAULT 0 NOT NULL, identifier VARCHAR(32) NOT NULL, PRIMARY KEY (identifier)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, expiry DATETIME NOT NULL, revoked TINYINT NOT NULL, access_token CHAR(80) DEFAULT NULL, INDEX IDX_4DD90732B6A2DD68 (access_token), PRIMARY KEY (identifier)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_authorization_code ADD CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673C7440455');
        $this->addSql('ALTER TABLE oauth2_authorization_code DROP FOREIGN KEY FK_509FEF5FC7440455');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732B6A2DD68');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_authorization_code');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE oauth2_refresh_token');
    }
}
