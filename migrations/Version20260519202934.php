<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519202934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE special_page_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, website_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10CD17F50A6 ON media (uuid)');
        $this->addSql('CREATE TABLE special_page (id INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, identifier VARCHAR(50) NOT NULL, content TEXT DEFAULT NULL, show_in_menu BOOLEAN DEFAULT false NOT NULL, menu_order INT DEFAULT 0 NOT NULL, category_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_965DB70BD17F50A6 ON special_page (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_965DB70B989D9B62 ON special_page (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_965DB70B772E836A ON special_page (identifier)');
        $this->addSql('CREATE INDEX IDX_965DB70B12469DE2 ON special_page (category_id)');
        $this->addSql('ALTER TABLE special_page ADD CONSTRAINT FK_965DB70B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE press_mention ADD published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE press_mention ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE press_mention ADD special_page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE press_mention DROP media_name');
        $this->addSql('ALTER TABLE press_mention ADD CONSTRAINT FK_7B4AB945EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE press_mention ADD CONSTRAINT FK_7B4AB945D44A3F71 FOREIGN KEY (special_page_id) REFERENCES special_page (id)');
        $this->addSql('CREATE INDEX IDX_7B4AB945EA9FDD75 ON press_mention (media_id)');
        $this->addSql('CREATE INDEX IDX_7B4AB945D44A3F71 ON press_mention (special_page_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE special_page_id_seq CASCADE');
        $this->addSql('ALTER TABLE special_page DROP CONSTRAINT FK_965DB70B12469DE2');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE special_page');
        $this->addSql('ALTER TABLE press_mention DROP CONSTRAINT FK_7B4AB945EA9FDD75');
        $this->addSql('ALTER TABLE press_mention DROP CONSTRAINT FK_7B4AB945D44A3F71');
        $this->addSql('DROP INDEX IDX_7B4AB945EA9FDD75');
        $this->addSql('DROP INDEX IDX_7B4AB945D44A3F71');
        $this->addSql('ALTER TABLE press_mention ADD media_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE press_mention DROP published_at');
        $this->addSql('ALTER TABLE press_mention DROP media_id');
        $this->addSql('ALTER TABLE press_mention DROP special_page_id');
    }
}
