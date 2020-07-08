<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702051437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates User table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE site_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE site_user (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6096BB0E7927C74 ON site_user (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE site_user_id_seq CASCADE');
        $this->addSql('DROP TABLE site_user');
    }
}
