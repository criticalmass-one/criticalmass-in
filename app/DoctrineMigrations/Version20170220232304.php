<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170220232304 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D841C2071');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5D8AA7396');
        $this->addSql('ALTER TABLE apicall DROP FOREIGN KEY FK_C26770F07987212D');
        $this->addSql('ALTER TABLE apiuser DROP FOREIGN KEY FK_837A89877987212D');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DDAE07E97');
        $this->addSql('ALTER TABLE blog_post_city DROP FOREIGN KEY FK_41708D70A77FBEAF');
        $this->addSql('ALTER TABLE blog_post_ride DROP FOREIGN KEY FK_F716F394A77FBEAF');
        $this->addSql('ALTER TABLE blog_post_view DROP FOREIGN KEY FK_92D624CAA77FBEAF');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C83E7EC5785');
        $this->addSql('ALTER TABLE caldera_bikeshop_openingtime DROP FOREIGN KEY FK_2754ACCAD88A1BAA');
        $this->addSql('ALTER TABLE caldera_bikeshop_tags DROP FOREIGN KEY FK_F6893CB8D88A1BAA');
        $this->addSql('ALTER TABLE tag_bikeshop DROP FOREIGN KEY FK_613C9E58D88A1BAA');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE2904019');
        $this->addSql('ALTER TABLE content_class_city DROP FOREIGN KEY FK_1F8389B8B7E1FF');
        $this->addSql('ALTER TABLE content_item DROP FOREIGN KEY FK_D279C8DB8BAC62AF');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F53CD5FE0A');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A6B0BC316C');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7365388CC');
        $this->addSql('ALTER TABLE event_view DROP FOREIGN KEY FK_E136E72571F7E88B');
        $this->addSql('ALTER TABLE notification_sent DROP FOREIGN KEY FK_2F8619371F7E88B');
        $this->addSql('ALTER TABLE notification_subscription DROP FOREIGN KEY FK_A2C88EE671F7E88B');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841871F7E88B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D71F7E88B');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5700047D2');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A6700047D2');
        $this->addSql('ALTER TABLE heatmap_city DROP FOREIGN KEY FK_3C3361BCD12F42C3');
        $this->addSql('ALTER TABLE heatmap_ride DROP FOREIGN KEY FK_8A551F58D12F42C3');
        $this->addSql('ALTER TABLE heatmap_track DROP FOREIGN KEY FK_35919BD7D12F42C3');
        $this->addSql('ALTER TABLE incident_view DROP FOREIGN KEY FK_14F1DC3159E53FB9');
        $this->addSql('ALTER TABLE caldera_bikeshop_openingtime DROP FOREIGN KEY FK_2754ACCAA3AE46D1');
        $this->addSql('ALTER TABLE plus_voucher_code DROP FOREIGN KEY FK_B3BA0733E417D410');
        $this->addSql('ALTER TABLE caldera_bikeshop_tags DROP FOREIGN KEY FK_F6893CB8BAD26311');
        $this->addSql('ALTER TABLE tag_bikeshop DROP FOREIGN KEY FK_613C9E58BAD26311');
        $this->addSql('DROP TABLE acl_classes');
        $this->addSql('DROP TABLE acl_entries');
        $this->addSql('DROP TABLE acl_object_identities');
        $this->addSql('DROP TABLE acl_object_identity_ancestors');
        $this->addSql('DROP TABLE acl_security_identities');
        $this->addSql('DROP TABLE anonymous_name');
        $this->addSql('DROP TABLE apicall');
        $this->addSql('DROP TABLE apiuser');
        $this->addSql('DROP TABLE app');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_post_city');
        $this->addSql('DROP TABLE blog_post_ride');
        $this->addSql('DROP TABLE blog_post_view');
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE caldera_baselocationentity');
        $this->addSql('DROP TABLE caldera_bikeshop');
        $this->addSql('DROP TABLE caldera_bikeshop_openingtime');
        $this->addSql('DROP TABLE caldera_bikeshop_tags');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_thread');
        $this->addSql('DROP TABLE content_archived');
        $this->addSql('DROP TABLE content_class');
        $this->addSql('DROP TABLE content_class_city');
        $this->addSql('DROP TABLE content_item');
        $this->addSql('DROP TABLE content_view');
        $this->addSql('DROP TABLE criticalmaps_user');
        $this->addSql('DROP TABLE cycleways_incident');
        $this->addSql('DROP TABLE cycleways_incident_type');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_view');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE glympse_ticket');
        $this->addSql('DROP TABLE heatmap');
        $this->addSql('DROP TABLE heatmap_city');
        $this->addSql('DROP TABLE heatmap_ride');
        $this->addSql('DROP TABLE heatmap_track');
        $this->addSql('DROP TABLE incident');
        $this->addSql('DROP TABLE incident_view');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_sent');
        $this->addSql('DROP TABLE notification_subscription');
        $this->addSql('DROP TABLE opening_time');
        $this->addSql('DROP TABLE plus_voucher_class');
        $this->addSql('DROP TABLE plus_voucher_code');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_bikeshop');
        $this->addSql('DROP TABLE tilelayer');
        $this->addSql('DROP TABLE tweet');
        $this->addSql('DROP INDEX IDX_14B7841871F7E88B ON photo');
        $this->addSql('ALTER TABLE photo DROP event_id');
        $this->addSql('DROP INDEX IDX_D6E3F8A6700047D2 ON track');
        $this->addSql('DROP INDEX IDX_D6E3F8A6B0BC316C ON track');
        $this->addSql('ALTER TABLE track DROP criticalmapsuser_id, DROP ticket_id, CHANGE source source ENUM(\'TRACK_SOURCE_GPX\', \'TRACK_SOURCE_STRAVA\', \'TRACK_SOURCE_RUNKEEPER\', \'TRACK_SOURCE_RUNTASTIC\', \'TRACK_SOURCE_DRAW\', \'TRACK_SOURCE_GLYMPSE\', \'TRACK_SOURCE_CRITICALMAPS\', \'TRACK_SOURCE_UNKNOWN\')');
        $this->addSql('DROP INDEX IDX_31204C83E7EC5785 ON thread');
        $this->addSql('ALTER TABLE thread DROP board_id');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D84A0A3ED');
        $this->addSql('DROP INDEX IDX_5A8A6C8D84A0A3ED ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8D71F7E88B ON post');
        $this->addSql('DROP INDEX IDX_5A8A6C8D841C2071 ON post');
        $this->addSql('ALTER TABLE post DROP anonymous_name_id, DROP event_id, DROP content_id, DROP chat, DROP colorRed, DROP colorGreen, DROP colorBlue, DROP obfuscated');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, class_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anonymous_name (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, gender VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apicall (id INT AUTO_INCREMENT NOT NULL, app_id INT DEFAULT NULL, referer VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, request VARCHAR(256) NOT NULL COLLATE utf8_unicode_ci, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_C26770F07987212D (app_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apiuser (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, app_id INT NOT NULL, token VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', enabled TINYINT(1) NOT NULL, INDEX IDX_837A89877987212D (app_id), INDEX IDX_837A89878BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, apiCalls INT NOT NULL, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', title VARCHAR(256) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, restrictedAccess TINYINT(1) NOT NULL, allowedReferer LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, url VARCHAR(256) NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, approved TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_C96E70CFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, abstract LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_23A0E66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, hostname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, blog_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, abstract LONGTEXT NOT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, featured_photo INT DEFAULT NULL, views INT NOT NULL, claim VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, showCoffeeButton TINYINT(1) NOT NULL, commentsEnabled TINYINT(1) NOT NULL, INDEX IDX_BA5AE01DA76ED395 (user_id), INDEX IDX_BA5AE01DDAE07E97 (blog_id), INDEX IDX_BA5AE01D4DFE7F82 (featured_photo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_city (blog_post_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_41708D70A77FBEAF (blog_post_id), INDEX IDX_41708D708BAC62AF (city_id), PRIMARY KEY(blog_post_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_ride (blog_post_id INT NOT NULL, ride_id INT NOT NULL, INDEX IDX_F716F394A77FBEAF (blog_post_id), INDEX IDX_F716F394302A8A70 (ride_id), PRIMARY KEY(blog_post_id, ride_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post_view (id INT AUTO_INCREMENT NOT NULL, blog_post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_92D624CAA76ED395 (user_id), INDEX IDX_92D624CAA77FBEAF (blog_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE board (id INT AUTO_INCREMENT NOT NULL, lastthread_id INT DEFAULT NULL, title LONGTEXT NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, threadNumber INT NOT NULL, postNumber INT NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_58562B47B43140E7 (lastthread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_baselocationentity (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_FC1533A68BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_bikeshop (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, facebook VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, twitter VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, url VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, views INT NOT NULL, street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, zip VARCHAR(5) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_bikeshop_openingtime (bikeshop_id INT NOT NULL, openingtime_id INT NOT NULL, UNIQUE INDEX UNIQ_2754ACCAA3AE46D1 (openingtime_id), INDEX IDX_2754ACCAD88A1BAA (bikeshop_id), PRIMARY KEY(bikeshop_id, openingtime_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caldera_bikeshop_tags (bikeshop_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F6893CB8D88A1BAA (bikeshop_id), INDEX IDX_F6893CB8BAD26311 (tag_id), PRIMARY KEY(bikeshop_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, thread_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, body LONGTEXT NOT NULL COLLATE utf8_unicode_ci, ancestors VARCHAR(1024) NOT NULL COLLATE utf8_unicode_ci, depth INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', state INT NOT NULL, INDEX IDX_9474526CE2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_thread (id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, permalink VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, is_commentable TINYINT(1) NOT NULL, num_comments INT NOT NULL, last_comment_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_archived (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, isPublicEditable TINYINT(1) NOT NULL, showInfobox TINYINT(1) NOT NULL, lastEditionDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', isArchived TINYINT(1) NOT NULL, archiveDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_class (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_class_city (content_class_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_1F8389B8B7E1FF (content_class_id), INDEX IDX_1F83898BAC62AF (city_id), PRIMARY KEY(content_class_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_item (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, text LONGTEXT NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, positionOrder INT NOT NULL, INDEX IDX_D279C8DB8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_view (id INT AUTO_INCREMENT NOT NULL, content_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_339F464BA76ED395 (user_id), INDEX IDX_339F464B84A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE criticalmaps_user (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, city_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', colorRed SMALLINT NOT NULL, colorGreen SMALLINT NOT NULL, colorBlue SMALLINT NOT NULL, startDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', endDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', exported TINYINT(1) NOT NULL, INDEX IDX_3CD5FE0A8BAC62AF (city_id), INDEX IDX_3CD5FE0A302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycleways_incident (id INT AUTO_INCREMENT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', user_id INT DEFAULT NULL, INDEX IDX_69C35C65A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycleways_incident_type (id INT AUTO_INCREMENT NOT NULL, caption VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, archive_user_id INT DEFAULT NULL, archive_parent_id INT DEFAULT NULL, city_id INT DEFAULT NULL, user_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', hasTime TINYINT(1) NOT NULL, hasLocation TINYINT(1) NOT NULL, location VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, facebook VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, twitter VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, url VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, isArchived TINYINT(1) NOT NULL, archiveDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', archiveMessage LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, createdAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', participationsNumberYes INT NOT NULL, participationsNumberMaybe INT NOT NULL, participationsNumberNo INT NOT NULL, views INT NOT NULL, INDEX IDX_3BAE0AA7A76ED395 (user_id), INDEX IDX_3BAE0AA78BAC62AF (city_id), INDEX IDX_3BAE0AA7365388CC (archive_parent_id), INDEX IDX_3BAE0AA7CA4E326A (archive_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_view (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_E136E725A76ED395 (user_id), INDEX IDX_E136E72571F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_583D1F3E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE glympse_ticket (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, city_id INT DEFAULT NULL, inviteId VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', counter INT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, colorRed SMALLINT NOT NULL, colorGreen SMALLINT NOT NULL, colorBlue SMALLINT NOT NULL, startDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', endDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', displayName VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, message VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, active TINYINT(1) NOT NULL, exported TINYINT(1) NOT NULL, queried TINYINT(1) NOT NULL, INDEX IDX_DAD21ED88BAC62AF (city_id), INDEX IDX_DAD21ED8302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heatmap (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, identifier VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, public TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heatmap_city (heatmap_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_3C3361BCD12F42C3 (heatmap_id), INDEX IDX_3C3361BC8BAC62AF (city_id), PRIMARY KEY(heatmap_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heatmap_ride (heatmap_id INT NOT NULL, ride_id INT NOT NULL, INDEX IDX_8A551F58D12F42C3 (heatmap_id), INDEX IDX_8A551F58302A8A70 (ride_id), PRIMARY KEY(heatmap_id, ride_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heatmap_track (heatmap_id INT NOT NULL, track_id INT NOT NULL, INDEX IDX_35919BD7D12F42C3 (heatmap_id), INDEX IDX_35919BD75ED23C43 (track_id), PRIMARY KEY(heatmap_id, track_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, city_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, geometryType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, polyline LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, expires TINYINT(1) NOT NULL, visibleFrom DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', visibleTo DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', incidentType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, dangerLevel VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, address LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, houseNumber VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, zipCode VARCHAR(5) DEFAULT NULL COLLATE utf8_unicode_ci, suburb VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, district VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, dateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', permalink VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, views INT NOT NULL, streetviewLink LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_3D03A11AA76ED395 (user_id), INDEX IDX_3D03A11A8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident_view (id INT AUTO_INCREMENT NOT NULL, incident_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_14F1DC31A76ED395 (user_id), INDEX IDX_14F1DC3159E53FB9 (incident_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, notifyByMail TINYINT(1) NOT NULL, notifyByPushover TINYINT(1) NOT NULL, notifyByShortmessage TINYINT(1) NOT NULL, notifyOnChange TINYINT(1) NOT NULL, notifyOnCreate TINYINT(1) NOT NULL, notifyOnSpecial TINYINT(1) NOT NULL, notifyOnActivity TINYINT(1) NOT NULL, event_id INT DEFAULT NULL, INDEX IDX_BF5476CA8BAC62AF (city_id), INDEX IDX_BF5476CA302A8A70 (ride_id), INDEX IDX_BF5476CA71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_sent (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, event_id INT DEFAULT NULL, city_id INT DEFAULT NULL, email TINYINT(1) NOT NULL, pushover TINYINT(1) NOT NULL, shortmessage TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_2F861938BAC62AF (city_id), INDEX IDX_2F86193302A8A70 (ride_id), INDEX IDX_2F8619371F7E88B (event_id), INDEX IDX_2F86193A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ride_id INT DEFAULT NULL, event_id INT DEFAULT NULL, city_id INT DEFAULT NULL, notifyByMail TINYINT(1) NOT NULL, notifyByPushover TINYINT(1) NOT NULL, notifyByShortmessage TINYINT(1) NOT NULL, notifyOnChange TINYINT(1) NOT NULL, notifyOnCreate TINYINT(1) NOT NULL, notifyOnSpecial TINYINT(1) NOT NULL, notifyOnActivity TINYINT(1) NOT NULL, INDEX IDX_A2C88EE68BAC62AF (city_id), INDEX IDX_A2C88EE6302A8A70 (ride_id), INDEX IDX_A2C88EE671F7E88B (event_id), INDEX IDX_A2C88EE6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opening_time (id INT AUTO_INCREMENT NOT NULL, weekday INT NOT NULL, openDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', closeDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plus_voucher_class (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, validSince DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', validUntil DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', codePrefix VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plus_voucher_code (id INT AUTO_INCREMENT NOT NULL, voucher_code_id INT DEFAULT NULL, user_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, activationDateTime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_B3BA073377153098 (code), UNIQUE INDEX UNIQ_B3BA0733A76ED395 (user_id), INDEX IDX_B3BA0733E417D410 (voucher_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, ride_id INT DEFAULT NULL, criticalmaps_user INT DEFAULT NULL, ticket_id INT DEFAULT NULL, user_id INT DEFAULT NULL, apiuser_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, accuracy DOUBLE PRECISION DEFAULT NULL, altitude DOUBLE PRECISION DEFAULT NULL, altitudeAccuracy DOUBLE PRECISION DEFAULT NULL, heading DOUBLE PRECISION DEFAULT NULL, speed DOUBLE PRECISION DEFAULT NULL, timestamp BIGINT DEFAULT NULL, creationDateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_462CE4F5A76ED395 (user_id), INDEX IDX_462CE4F5D8AA7396 (apiuser_id), INDEX IDX_462CE4F5700047D2 (ticket_id), INDEX IDX_462CE4F53CD5FE0A (criticalmaps_user), INDEX IDX_462CE4F5302A8A70 (ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_bikeshop (tag_id INT NOT NULL, bikeshop_id INT NOT NULL, INDEX IDX_613C9E58BAD26311 (tag_id), INDEX IDX_613C9E58D88A1BAA (bikeshop_id), PRIMARY KEY(tag_id, bikeshop_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tilelayer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, attribution VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, plusOnly TINYINT(1) NOT NULL, public TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, standard TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tweet (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(140) DEFAULT NULL COLLATE utf8_unicode_ci, username VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, screenname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, profileImageUrl VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, dateTime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', twitterId VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, rawResponse LONGTEXT NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apicall ADD CONSTRAINT FK_C26770F07987212D FOREIGN KEY (app_id) REFERENCES app (id)');
        $this->addSql('ALTER TABLE apiuser ADD CONSTRAINT FK_837A89878BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE apiuser ADD CONSTRAINT FK_837A89877987212D FOREIGN KEY (app_id) REFERENCES app (id)');
        $this->addSql('ALTER TABLE app ADD CONSTRAINT FK_C96E70CFA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_post_city ADD CONSTRAINT FK_41708D70A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_ride ADD CONSTRAINT FK_F716F394A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post_view ADD CONSTRAINT FK_92D624CAA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B47B43140E7 FOREIGN KEY (lastthread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE caldera_bikeshop_openingtime ADD CONSTRAINT FK_2754ACCAA3AE46D1 FOREIGN KEY (openingtime_id) REFERENCES opening_time (id)');
        $this->addSql('ALTER TABLE caldera_bikeshop_openingtime ADD CONSTRAINT FK_2754ACCAD88A1BAA FOREIGN KEY (bikeshop_id) REFERENCES caldera_bikeshop (id)');
        $this->addSql('ALTER TABLE caldera_bikeshop_tags ADD CONSTRAINT FK_F6893CB8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caldera_bikeshop_tags ADD CONSTRAINT FK_F6893CB8D88A1BAA FOREIGN KEY (bikeshop_id) REFERENCES caldera_bikeshop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES comment_thread (id)');
        $this->addSql('ALTER TABLE content_class_city ADD CONSTRAINT FK_1F8389B8B7E1FF FOREIGN KEY (content_class_id) REFERENCES content_class (id)');
        $this->addSql('ALTER TABLE content_item ADD CONSTRAINT FK_D279C8DB8BAC62AF FOREIGN KEY (city_id) REFERENCES content_class (id)');
        $this->addSql('ALTER TABLE content_view ADD CONSTRAINT FK_339F464B84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE content_view ADD CONSTRAINT FK_339F464BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE criticalmaps_user ADD CONSTRAINT FK_3CD5FE0A302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE criticalmaps_user ADD CONSTRAINT FK_3CD5FE0A8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7CA4E326A FOREIGN KEY (archive_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7365388CC FOREIGN KEY (archive_parent_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE event_view ADD CONSTRAINT FK_E136E72571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_view ADD CONSTRAINT FK_E136E725A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE glympse_ticket ADD CONSTRAINT FK_DAD21ED8302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE glympse_ticket ADD CONSTRAINT FK_DAD21ED88BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE heatmap_city ADD CONSTRAINT FK_3C3361BC8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE heatmap_city ADD CONSTRAINT FK_3C3361BCD12F42C3 FOREIGN KEY (heatmap_id) REFERENCES heatmap (id)');
        $this->addSql('ALTER TABLE heatmap_ride ADD CONSTRAINT FK_8A551F58302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE heatmap_ride ADD CONSTRAINT FK_8A551F58D12F42C3 FOREIGN KEY (heatmap_id) REFERENCES heatmap (id)');
        $this->addSql('ALTER TABLE heatmap_track ADD CONSTRAINT FK_35919BD75ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE heatmap_track ADD CONSTRAINT FK_35919BD7D12F42C3 FOREIGN KEY (heatmap_id) REFERENCES heatmap (id)');
        $this->addSql('ALTER TABLE incident_view ADD CONSTRAINT FK_14F1DC3159E53FB9 FOREIGN KEY (incident_id) REFERENCES incident (id)');
        $this->addSql('ALTER TABLE notification_sent ADD CONSTRAINT FK_2F86193A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE notification_sent ADD CONSTRAINT FK_2F86193302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE notification_sent ADD CONSTRAINT FK_2F8619371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE notification_sent ADD CONSTRAINT FK_2F861938BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE6A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE6302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE671F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE plus_voucher_code ADD CONSTRAINT FK_B3BA0733E417D410 FOREIGN KEY (voucher_code_id) REFERENCES plus_voucher_class (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F53CD5FE0A FOREIGN KEY (criticalmaps_user) REFERENCES criticalmaps_user (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5700047D2 FOREIGN KEY (ticket_id) REFERENCES glympse_ticket (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5D8AA7396 FOREIGN KEY (apiuser_id) REFERENCES apiuser (id)');
        $this->addSql('ALTER TABLE tag_bikeshop ADD CONSTRAINT FK_613C9E58D88A1BAA FOREIGN KEY (bikeshop_id) REFERENCES caldera_bikeshop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_bikeshop ADD CONSTRAINT FK_613C9E58BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841871F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_14B7841871F7E88B ON photo (event_id)');
        $this->addSql('ALTER TABLE post ADD anonymous_name_id INT DEFAULT NULL, ADD event_id INT DEFAULT NULL, ADD content_id INT DEFAULT NULL, ADD chat TINYINT(1) NOT NULL, ADD colorRed INT NOT NULL, ADD colorGreen INT NOT NULL, ADD colorBlue INT NOT NULL, ADD obfuscated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D841C2071 FOREIGN KEY (anonymous_name_id) REFERENCES anonymous_name (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D84A0A3ED ON post (content_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D71F7E88B ON post (event_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D841C2071 ON post (anonymous_name_id)');
        $this->addSql('ALTER TABLE thread ADD board_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C83E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('CREATE INDEX IDX_31204C83E7EC5785 ON thread (board_id)');
        $this->addSql('ALTER TABLE track ADD criticalmapsuser_id INT DEFAULT NULL, ADD ticket_id INT DEFAULT NULL, CHANGE source source VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A6B0BC316C FOREIGN KEY (criticalmapsuser_id) REFERENCES criticalmaps_user (id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A6700047D2 FOREIGN KEY (ticket_id) REFERENCES glympse_ticket (id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A6700047D2 ON track (ticket_id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A6B0BC316C ON track (criticalmapsuser_id)');
    }
}
