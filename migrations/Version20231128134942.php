<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128134942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE check_in (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', attendee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, error_reason VARCHAR(255) DEFAULT NULL, reason_explanation VARCHAR(255) DEFAULT NULL, check_in_time DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_90466CF9BCFD782A (attendee_id), INDEX IDX_90466CF9EDD31067 (check_in_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id)');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9EDD31067 FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9BCFD782A');
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9EDD31067');
        $this->addSql('DROP TABLE check_in');
    }
}
