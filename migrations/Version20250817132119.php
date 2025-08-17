<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817132119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee ADD override_badge_product_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D567620B054 FOREIGN KEY (override_badge_product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1150D567620B054 ON attendee (override_badge_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D567620B054');
        $this->addSql('DROP INDEX IDX_1150D567620B054 ON attendee');
        $this->addSql('ALTER TABLE attendee DROP override_badge_product_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
