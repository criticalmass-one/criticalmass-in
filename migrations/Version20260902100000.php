<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Carry a forum post counter on the user so the post view does not have to count
 * once per rendered post, and fill it from what is already in the database.
 */
final class Version20260902100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user.forum_post_count and backfill it from existing posts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD forum_post_count INT DEFAULT 0 NOT NULL');
        $this->addSql('UPDATE user u SET u.forum_post_count = (SELECT COUNT(*) FROM post p WHERE p.user_id = u.id AND p.thread_id IS NOT NULL AND p.enabled = 1)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP forum_post_count');
    }
}
