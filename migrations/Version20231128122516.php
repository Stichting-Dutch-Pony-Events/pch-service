<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128122516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1150D567700047D2 ON attendee (ticket_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1150D56726E0822E ON attendee (ticket_secret)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1150D567700047D2 ON attendee');
        $this->addSql('DROP INDEX UNIQ_1150D56726E0822E ON attendee');
    }
}
