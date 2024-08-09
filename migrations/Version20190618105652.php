<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190618105652 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride ADD enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD disabledReason ENUM(\'DUPLICATE\', \'WRONG_WEBSITE_HANDLING\', \'WRONG_AUTO_GNERATION\', \'CANCELLED_WEATHER\', \'CANCELLED_AUTHORITIES\', \'CANCELLED\') DEFAULT NULL COMMENT \'(DC2Type:RideDisabledReasonType)\'');
        $this->addSql('ALTER TABLE ride_audit ADD enabled TINYINT(1) DEFAULT \'1\', ADD disabledReason ENUM(\'DUPLICATE\', \'WRONG_WEBSITE_HANDLING\', \'WRONG_AUTO_GNERATION\', \'CANCELLED_WEATHER\', \'CANCELLED_AUTHORITIES\', \'CANCELLED\') DEFAULT NULL COMMENT \'(DC2Type:RideDisabledReasonType)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ride DROP enabled, DROP disabledReason');
        $this->addSql('ALTER TABLE ride_audit DROP enabled, DROP disabledReason');
    }
}
