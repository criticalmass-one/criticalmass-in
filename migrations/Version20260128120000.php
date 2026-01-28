<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260128120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create OAuth2 tables for league/oauth2-server-bundle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_client (
                identifier VARCHAR(32) NOT NULL,
                name VARCHAR(128) NOT NULL,
                secret VARCHAR(128) DEFAULT NULL,
                redirect_uris TEXT DEFAULT NULL COMMENT '(DC2Type:oauth2_redirect_uri)',
                grants TEXT DEFAULT NULL COMMENT '(DC2Type:oauth2_grant)',
                scopes TEXT DEFAULT NULL COMMENT '(DC2Type:oauth2_scope)',
                active TINYINT(1) NOT NULL DEFAULT 1,
                allow_plain_text_pkce TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (identifier)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_access_token (
                identifier VARCHAR(80) NOT NULL,
                client VARCHAR(32) NOT NULL,
                expiry DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                user_identifier VARCHAR(128) DEFAULT NULL,
                scopes TEXT DEFAULT NULL COMMENT '(DC2Type:oauth2_scope)',
                revoked TINYINT(1) NOT NULL,
                PRIMARY KEY (identifier),
                INDEX IDX_454D9673C7440455 (client),
                CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_authorization_code (
                identifier VARCHAR(80) NOT NULL,
                client VARCHAR(32) NOT NULL,
                expiry DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                user_identifier VARCHAR(128) DEFAULT NULL,
                scopes TEXT DEFAULT NULL COMMENT '(DC2Type:oauth2_scope)',
                revoked TINYINT(1) NOT NULL,
                PRIMARY KEY (identifier),
                INDEX IDX_509FEF5FC7440455 (client),
                CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_refresh_token (
                identifier VARCHAR(80) NOT NULL,
                access_token VARCHAR(80) DEFAULT NULL,
                expiry DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                revoked TINYINT(1) NOT NULL,
                PRIMARY KEY (identifier),
                INDEX IDX_4DD90732B6A2DD68 (access_token),
                CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->addSql('DROP TABLE oauth2_authorization_code');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_client');
    }
}
