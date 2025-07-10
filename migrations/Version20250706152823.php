<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706152823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_answer (answer LONGTEXT NOT NULL, `order` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, quiz_question_id BINARY(16) DEFAULT NULL, INDEX IDX_3799BA7C3101E51F (quiz_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz_answer_team_weight (weight INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, quiz_answer_id BINARY(16) DEFAULT NULL, team_id BINARY(16) DEFAULT NULL, INDEX IDX_C4E1442CAC5339E1 (quiz_answer_id), INDEX IDX_C4E1442C296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz_question (question LONGTEXT NOT NULL, `order` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id BINARY(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE quiz_answer ADD CONSTRAINT FK_3799BA7C3101E51F FOREIGN KEY (quiz_question_id) REFERENCES quiz_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_answer_team_weight ADD CONSTRAINT FK_C4E1442CAC5339E1 FOREIGN KEY (quiz_answer_id) REFERENCES quiz_answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_answer_team_weight ADD CONSTRAINT FK_C4E1442C296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE achievement CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE api_key CHANGE id id BINARY(16) NOT NULL, CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE attendee CHANGE id id BINARY(16) NOT NULL, CHANGE product_id product_id BINARY(16) DEFAULT NULL, CHANGE team_id team_id BINARY(16) DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE attendee_achievement CHANGE id id BINARY(16) NOT NULL, CHANGE achievement_id achievement_id BINARY(16) DEFAULT NULL, CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE check_in CHANGE id id BINARY(16) NOT NULL, CHANGE attendee_id attendee_id BINARY(16) NOT NULL, CHANGE check_in_list_id check_in_list_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE check_in_list CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE print_job CHANGE id id BINARY(16) NOT NULL, CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE check_in_list_products CHANGE product_id product_id BINARY(16) NOT NULL, CHANGE check_in_list_id check_in_list_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE setting CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE team CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_answer DROP FOREIGN KEY FK_3799BA7C3101E51F');
        $this->addSql('ALTER TABLE quiz_answer_team_weight DROP FOREIGN KEY FK_C4E1442CAC5339E1');
        $this->addSql('ALTER TABLE quiz_answer_team_weight DROP FOREIGN KEY FK_C4E1442C296CD8AE');
        $this->addSql('DROP TABLE quiz_answer');
        $this->addSql('DROP TABLE quiz_answer_team_weight');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('ALTER TABLE achievement CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE api_key CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE attendee CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE product_id product_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE team_id team_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE attendee_achievement CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE achievement_id achievement_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE check_in CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE attendee_id attendee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE check_in_list_id check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE check_in_list CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE check_in_list_products CHANGE product_id product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE check_in_list_id check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE print_job CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE attendee_id attendee_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE setting CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE team CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }
}
