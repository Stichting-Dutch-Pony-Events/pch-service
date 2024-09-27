<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927220134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, description VARCHAR(255) DEFAULT NULL, identifier VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendee_achievement (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', achievement_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', attendee_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E8A3DC0BB3EC99FE (achievement_id), INDEX IDX_E8A3DC0BBCFD782A (attendee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee_achievement ADD CONSTRAINT FK_E8A3DC0BB3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendee_achievement ADD CONSTRAINT FK_E8A3DC0BBCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee_achievement DROP FOREIGN KEY FK_E8A3DC0BB3EC99FE');
        $this->addSql('ALTER TABLE attendee_achievement DROP FOREIGN KEY FK_E8A3DC0BBCFD782A');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE attendee_achievement');
        $this->addSql('DROP TABLE setting');
    }
}
