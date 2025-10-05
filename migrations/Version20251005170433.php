<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005170433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character_quiz_submission (last_submission TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, attendee_id BINARY(16) NOT NULL, INDEX IDX_D94744F0BCFD782A (attendee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE character_quiz_submission_answer (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, submission_id BINARY(16) NOT NULL, question_id BINARY(16) NOT NULL, answer_id BINARY(16) NOT NULL, INDEX IDX_62D6AD4FE1FD4933 (submission_id), INDEX IDX_62D6AD4F1E27F6BF (question_id), INDEX IDX_62D6AD4FAA334807 (answer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE character_quiz_submission_team_result (percentage INT DEFAULT 0 NOT NULL, points INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, submission_id BINARY(16) NOT NULL, team_id BINARY(16) NOT NULL, INDEX IDX_DD125BF6E1FD4933 (submission_id), INDEX IDX_DD125BF6296CD8AE (team_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE character_quiz_submission ADD CONSTRAINT FK_D94744F0BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_quiz_submission_answer ADD CONSTRAINT FK_62D6AD4FE1FD4933 FOREIGN KEY (submission_id) REFERENCES character_quiz_submission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_quiz_submission_answer ADD CONSTRAINT FK_62D6AD4F1E27F6BF FOREIGN KEY (question_id) REFERENCES quiz_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_quiz_submission_answer ADD CONSTRAINT FK_62D6AD4FAA334807 FOREIGN KEY (answer_id) REFERENCES quiz_answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_quiz_submission_team_result ADD CONSTRAINT FK_DD125BF6E1FD4933 FOREIGN KEY (submission_id) REFERENCES character_quiz_submission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_quiz_submission_team_result ADD CONSTRAINT FK_DD125BF6296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE character_quiz_submission DROP FOREIGN KEY FK_D94744F0BCFD782A');
        $this->addSql('ALTER TABLE character_quiz_submission_answer DROP FOREIGN KEY FK_62D6AD4FE1FD4933');
        $this->addSql('ALTER TABLE character_quiz_submission_answer DROP FOREIGN KEY FK_62D6AD4F1E27F6BF');
        $this->addSql('ALTER TABLE character_quiz_submission_answer DROP FOREIGN KEY FK_62D6AD4FAA334807');
        $this->addSql('ALTER TABLE character_quiz_submission_team_result DROP FOREIGN KEY FK_DD125BF6E1FD4933');
        $this->addSql('ALTER TABLE character_quiz_submission_team_result DROP FOREIGN KEY FK_DD125BF6296CD8AE');
        $this->addSql('DROP TABLE character_quiz_submission');
        $this->addSql('DROP TABLE character_quiz_submission_answer');
        $this->addSql('DROP TABLE character_quiz_submission_team_result');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
