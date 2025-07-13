<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250713104608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable_day (title VARCHAR(255) DEFAULT \'Untitled Timetable Day\' NOT NULL, starts_at DATETIME NOT NULL, ends_at DATETIME NOT NULL, `order` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, INDEX timetable_day_order_idx (`order`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE timetable_item (title VARCHAR(255) DEFAULT \'Untitled Item\' NOT NULL, description LONGTEXT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, timetable_day_id BINARY(16) NOT NULL, timetable_location_id BINARY(16) NOT NULL, volunteer_id BINARY(16) DEFAULT NULL, INDEX IDX_8A27CF10D79EF2F2 (timetable_day_id), INDEX IDX_8A27CF10FE22626 (timetable_location_id), INDEX IDX_8A27CF108EFAB6B1 (volunteer_id), INDEX timetable_item_start_time_idx (start_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE timetable_location (title VARCHAR(255) DEFAULT \'Untitled Timetable Location\' NOT NULL, timetable_location_type ENUM(\'ROOM\', \'VOLUNTEER_POST\') NOT NULL, `order` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, INDEX timetable_location_type_idx (timetable_location_type), INDEX timetable_location_order_idx (`order`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE timetable_location_timetable_day (timetable_location_id BINARY(16) NOT NULL, timetable_day_id BINARY(16) NOT NULL, INDEX IDX_414B5835FE22626 (timetable_location_id), INDEX IDX_414B5835D79EF2F2 (timetable_day_id), PRIMARY KEY(timetable_location_id, timetable_day_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE timetable_item ADD CONSTRAINT FK_8A27CF10D79EF2F2 FOREIGN KEY (timetable_day_id) REFERENCES timetable_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_item ADD CONSTRAINT FK_8A27CF10FE22626 FOREIGN KEY (timetable_location_id) REFERENCES timetable_location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_item ADD CONSTRAINT FK_8A27CF108EFAB6B1 FOREIGN KEY (volunteer_id) REFERENCES attendee (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE timetable_location_timetable_day ADD CONSTRAINT FK_414B5835FE22626 FOREIGN KEY (timetable_location_id) REFERENCES timetable_location (id)');
        $this->addSql('ALTER TABLE timetable_location_timetable_day ADD CONSTRAINT FK_414B5835D79EF2F2 FOREIGN KEY (timetable_day_id) REFERENCES timetable_day (id)');
        $this->addSql('ALTER TABLE quiz_answer CHANGE title title VARCHAR(255) DEFAULT \'Untitled Answer\' NOT NULL');
        $this->addSql('ALTER TABLE quiz_question CHANGE title title VARCHAR(255) DEFAULT \'Untitled Question\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timetable_item DROP FOREIGN KEY FK_8A27CF10D79EF2F2');
        $this->addSql('ALTER TABLE timetable_item DROP FOREIGN KEY FK_8A27CF10FE22626');
        $this->addSql('ALTER TABLE timetable_item DROP FOREIGN KEY FK_8A27CF108EFAB6B1');
        $this->addSql('ALTER TABLE timetable_location_timetable_day DROP FOREIGN KEY FK_414B5835FE22626');
        $this->addSql('ALTER TABLE timetable_location_timetable_day DROP FOREIGN KEY FK_414B5835D79EF2F2');
        $this->addSql('DROP TABLE timetable_day');
        $this->addSql('DROP TABLE timetable_item');
        $this->addSql('DROP TABLE timetable_location');
        $this->addSql('DROP TABLE timetable_location_timetable_day');
        $this->addSql('ALTER TABLE quiz_answer CHANGE title title LONGTEXT DEFAULT \'Untitled Answer\' NOT NULL');
        $this->addSql('ALTER TABLE quiz_question CHANGE title title LONGTEXT DEFAULT \'Untitled Question\' NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
