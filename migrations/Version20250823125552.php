<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823125552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY `FK_90466CF9BCFD782A`');
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY `FK_90466CF9EDD31067`');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9EDD31067 FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9BCFD782A');
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9EDD31067');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT `FK_90466CF9BCFD782A` FOREIGN KEY (attendee_id) REFERENCES attendee (id)');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT `FK_90466CF9EDD31067` FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
