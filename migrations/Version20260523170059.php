<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260523170059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE board_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE board_member (id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL, title VARCHAR(100) DEFAULT NULL, expertise VARCHAR(255) DEFAULT NULL, qualifications VARCHAR(255) DEFAULT NULL, sort_order INT DEFAULT 0 NOT NULL, photo_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCFABEDFD17F50A6 ON board_member (uuid)');
        $this->addSql('CREATE INDEX IDX_DCFABEDF7E9E4C8C ON board_member (photo_id)');
        $this->addSql('ALTER TABLE board_member ADD CONSTRAINT FK_DCFABEDF7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE partner ALTER created_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE partner ALTER updated_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('COMMENT ON COLUMN partner.uuid IS \'\'');
        $this->addSql('COMMENT ON COLUMN partner.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN partner.updated_at IS \'\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE board_member_id_seq CASCADE');
        $this->addSql('ALTER TABLE board_member DROP CONSTRAINT FK_DCFABEDF7E9E4C8C');
        $this->addSql('DROP TABLE board_member');
        $this->addSql('ALTER TABLE partner ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE partner ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN partner.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partner.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partner.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
