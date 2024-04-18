<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418091343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendee (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, family_name VARCHAR(255) DEFAULT NULL, nick_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, order_code VARCHAR(255) NOT NULL, ticket_id INT NOT NULL, ticket_secret VARCHAR(255) NOT NULL, nfc_tag_id VARCHAR(255) DEFAULT NULL, mini_identifier VARCHAR(4) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1150D567700047D2 (ticket_id), UNIQUE INDEX UNIQ_1150D56726E0822E (ticket_secret), INDEX IDX_1150D5674584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE check_in (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', attendee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, error_reason VARCHAR(255) DEFAULT NULL, reason_explanation VARCHAR(255) DEFAULT NULL, check_in_time DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_90466CF9BCFD782A (attendee_id), INDEX IDX_90466CF9EDD31067 (check_in_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE check_in_list (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, pretix_id INT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, type VARCHAR(255) DEFAULT \'TICKET\' NOT NULL, pretix_product_ids JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_286F89FC124BEC6E (pretix_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, pretix_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D34A04AD124BEC6E (pretix_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE check_in_list_products (product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_37CD0A124584665A (product_id), INDEX IDX_37CD0A12EDD31067 (check_in_list_id), PRIMARY KEY(product_id, check_in_list_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D5674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id)');
        $this->addSql('ALTER TABLE check_in ADD CONSTRAINT FK_90466CF9EDD31067 FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id)');
        $this->addSql('ALTER TABLE check_in_list_products ADD CONSTRAINT FK_37CD0A124584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE check_in_list_products ADD CONSTRAINT FK_37CD0A12EDD31067 FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D5674584665A');
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9BCFD782A');
        $this->addSql('ALTER TABLE check_in DROP FOREIGN KEY FK_90466CF9EDD31067');
        $this->addSql('ALTER TABLE check_in_list_products DROP FOREIGN KEY FK_37CD0A124584665A');
        $this->addSql('ALTER TABLE check_in_list_products DROP FOREIGN KEY FK_37CD0A12EDD31067');
        $this->addSql('DROP TABLE attendee');
        $this->addSql('DROP TABLE check_in');
        $this->addSql('DROP TABLE check_in_list');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE check_in_list_products');
    }
}
