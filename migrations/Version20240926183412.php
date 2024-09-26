<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926183412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee ADD fire_base_token VARCHAR(255) NOT NULL AFTER `password`');
        $this->addSql('ALTER TABLE team ADD identifier VARCHAR(255) NOT NULL AFTER `description`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee DROP fire_base_token');
        $this->addSql('ALTER TABLE team DROP identifier');
    }
}
