<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Track edits of forum posts: when a post was last changed and by whom.
 */
final class Version20260901210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updatedAt and updated_by_user_id to post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD updatedAt DATETIME DEFAULT NULL, ADD updated_by_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D2793CC5E FOREIGN KEY (updated_by_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D2793CC5E ON post (updated_by_user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D2793CC5E');
        $this->addSql('DROP INDEX IDX_5A8A6C8D2793CC5E ON post');
        $this->addSql('ALTER TABLE post DROP updatedAt, DROP updated_by_user_id');
    }
}
