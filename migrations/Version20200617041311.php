<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617041311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make pizza and dependent tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pizza_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE receipt_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ingredient (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, price INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE pizza (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, diameter INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE receipt_part (id INT NOT NULL, ingredient_id INT NOT NULL, pizza_id INT NOT NULL, weight INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DF744B4A933FE08C ON receipt_part (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_DF744B4AD41D1D42 ON receipt_part (pizza_id)');
        $this->addSql('ALTER TABLE receipt_part ADD CONSTRAINT FK_DF744B4A933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_part ADD CONSTRAINT FK_DF744B4AD41D1D42 FOREIGN KEY (pizza_id) REFERENCES pizza (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt_part DROP CONSTRAINT FK_DF744B4A933FE08C');
        $this->addSql('ALTER TABLE receipt_part DROP CONSTRAINT FK_DF744B4AD41D1D42');
        $this->addSql('DROP SEQUENCE ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pizza_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE receipt_part_id_seq CASCADE');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE receipt_part');
    }
}
