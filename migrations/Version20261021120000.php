<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261021120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Live Quiz tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE live_quiz_session (
            id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            created_by_id binary(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(200) NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_active TINYINT(1) DEFAULT 1 NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_LQS_CREATED_BY (created_by_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE live_quiz_question (
            id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            session_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            label VARCHAR(500) DEFAULT NULL,
            duration_seconds INT DEFAULT 15 NOT NULL,
            opened_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_LQQ_SESSION (session_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE live_quiz_option (
            id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            question_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            label VARCHAR(200) NOT NULL,
            `order` INT DEFAULT 0 NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_LQO_QUESTION (question_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE live_quiz_participant (
            id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            session_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            attendee_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            joined_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_LQP_SESSION (session_id),
            INDEX IDX_LQP_ATTENDEE (attendee_id),
            UNIQUE INDEX UNIQ_LQP (session_id, attendee_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE live_quiz_response (
            id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            question_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            option_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            participant_id binary(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_LQR_QUESTION (question_id),
            INDEX IDX_LQR_OPTION (option_id),
            INDEX IDX_LQR_PARTICIPANT (participant_id),
            UNIQUE INDEX UNIQ_LQR (question_id, participant_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE live_quiz_session ADD CONSTRAINT FK_LQS_CREATED_BY FOREIGN KEY (created_by_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_question ADD CONSTRAINT FK_LQQ_SESSION FOREIGN KEY (session_id) REFERENCES live_quiz_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_option ADD CONSTRAINT FK_LQO_QUESTION FOREIGN KEY (question_id) REFERENCES live_quiz_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_participant ADD CONSTRAINT FK_LQP_SESSION FOREIGN KEY (session_id) REFERENCES live_quiz_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_participant ADD CONSTRAINT FK_LQP_ATTENDEE FOREIGN KEY (attendee_id) REFERENCES attendee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_response ADD CONSTRAINT FK_LQR_QUESTION FOREIGN KEY (question_id) REFERENCES live_quiz_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_response ADD CONSTRAINT FK_LQR_OPTION FOREIGN KEY (option_id) REFERENCES live_quiz_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE live_quiz_response ADD CONSTRAINT FK_LQR_PARTICIPANT FOREIGN KEY (participant_id) REFERENCES live_quiz_participant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE live_quiz_response DROP FOREIGN KEY FK_LQR_QUESTION');
        $this->addSql('ALTER TABLE live_quiz_response DROP FOREIGN KEY FK_LQR_OPTION');
        $this->addSql('ALTER TABLE live_quiz_response DROP FOREIGN KEY FK_LQR_PARTICIPANT');
        $this->addSql('ALTER TABLE live_quiz_participant DROP FOREIGN KEY FK_LQP_SESSION');
        $this->addSql('ALTER TABLE live_quiz_participant DROP FOREIGN KEY FK_LQP_ATTENDEE');
        $this->addSql('ALTER TABLE live_quiz_option DROP FOREIGN KEY FK_LQO_QUESTION');
        $this->addSql('ALTER TABLE live_quiz_question DROP FOREIGN KEY FK_LQQ_SESSION');
        $this->addSql('ALTER TABLE live_quiz_session DROP FOREIGN KEY FK_LQS_CREATED_BY');
        $this->addSql('DROP TABLE live_quiz_response');
        $this->addSql('DROP TABLE live_quiz_participant');
        $this->addSql('DROP TABLE live_quiz_option');
        $this->addSql('DROP TABLE live_quiz_question');
        $this->addSql('DROP TABLE live_quiz_session');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
