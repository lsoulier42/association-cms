<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519213959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment (id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, location VARCHAR(255) NOT NULL, subject TEXT NOT NULL, special_page_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F844D17F50A6 ON appointment (uuid)');
        $this->addSql('CREATE INDEX IDX_FE38F844D44A3F71 ON appointment (special_page_id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844D44A3F71 FOREIGN KEY (special_page_id) REFERENCES special_page (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE appointment_id_seq CASCADE');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844D44A3F71');
        $this->addSql('DROP TABLE appointment');
    }
}
