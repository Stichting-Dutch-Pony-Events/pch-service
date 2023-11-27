<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127123444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendee (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', product_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, family_name VARCHAR(255) DEFAULT NULL, nick_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, order_code VARCHAR(255) NOT NULL, ticket_id INT NOT NULL, ticket_secret VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1150D5674584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(200) NOT NULL, pretix_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D34A04AD124BEC6E (pretix_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE check_in_list_products (product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', check_in_list_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_37CD0A124584665A (product_id), INDEX IDX_37CD0A12EDD31067 (check_in_list_id), PRIMARY KEY(product_id, check_in_list_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D5674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE check_in_list_products ADD CONSTRAINT FK_37CD0A124584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE check_in_list_products ADD CONSTRAINT FK_37CD0A12EDD31067 FOREIGN KEY (check_in_list_id) REFERENCES check_in_list (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D5674584665A');
        $this->addSql('ALTER TABLE check_in_list_products DROP FOREIGN KEY FK_37CD0A124584665A');
        $this->addSql('ALTER TABLE check_in_list_products DROP FOREIGN KEY FK_37CD0A12EDD31067');
        $this->addSql('DROP TABLE attendee');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE check_in_list_products');
    }
}
