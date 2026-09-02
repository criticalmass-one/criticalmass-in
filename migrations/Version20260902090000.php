<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Forum subscriptions on four levels (thread, board, city, everything) plus the
 * per-user master switch for notification mails.
 */
final class Version20260902090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create forum_subscription table and add user.forum_notifications';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE forum_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, thread_id INT DEFAULT NULL, board_id INT DEFAULT NULL, city_id INT DEFAULT NULL, global_scope TINYINT(1) DEFAULT 0 NOT NULL, createdAt DATETIME NOT NULL, INDEX forum_subscription_user_index (user_id), INDEX IDX_9B189294E2904019 (thread_id), INDEX IDX_9B189294E7EC5785 (board_id), INDEX IDX_9B1892948BAC62AF (city_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE forum_subscription ADD CONSTRAINT FK_9B189294A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forum_subscription ADD CONSTRAINT FK_9B189294E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE forum_subscription ADD CONSTRAINT FK_9B189294E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE forum_subscription ADD CONSTRAINT FK_9B1892948BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user ADD forum_notifications TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE forum_subscription');
        $this->addSql('ALTER TABLE user DROP forum_notifications');
    }
}
