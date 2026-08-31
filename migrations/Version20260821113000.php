<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Grundlage für Passkeys: Tabelle für die registrierten Credentials und ein stabiler
 * WebAuthn-User-Handle am Benutzerkonto.
 *
 * Der Handle wird bewusst nicht befüllt. Bestandskonten bekommen ihn beim ersten Kontakt
 * mit WebAuthn, damit diese Migration nicht über die gesamte Nutzertabelle laufen muss.
 */
final class Version20260821113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create webauthn_credential table and add user.webauthnUserHandle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE webauthn_credential (publicKeyCredentialId LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, transports JSON NOT NULL, attestationType VARCHAR(255) NOT NULL, trustPath JSON NOT NULL, aaguid TINYTEXT NOT NULL, credentialPublicKey LONGTEXT NOT NULL, userHandle VARCHAR(255) NOT NULL, counter INT NOT NULL, otherUI JSON DEFAULT NULL, backupEligible TINYINT DEFAULT NULL, backupStatus TINYINT DEFAULT NULL, uvInitialized TINYINT DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, lastUsedAt DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_850123F9A76ED395 (user_id), INDEX idx_webauthn_user_handle (userHandle), UNIQUE INDEX uniq_webauthn_credential_id (publicKeyCredentialId(255)), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE webauthn_credential ADD CONSTRAINT FK_850123F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE user ADD webauthnUserHandle VARCHAR(36) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E74EE973 ON user (webauthnUserHandle)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_8D93D649E74EE973 ON user');
        $this->addSql('ALTER TABLE user DROP webauthnUserHandle');

        $this->addSql('ALTER TABLE webauthn_credential DROP FOREIGN KEY FK_850123F9A76ED395');
        $this->addSql('DROP TABLE webauthn_credential');
    }
}
