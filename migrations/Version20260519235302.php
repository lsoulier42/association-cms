<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519235302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create partner table and seed partner special page';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE partner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE partner (id INT NOT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, website_url VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, position INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_312B3E16D17F50A6 ON partner (uuid)');
        $this->addSql('COMMENT ON COLUMN partner.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partner.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partner.updated_at IS \'(DC2Type:datetime_immutable)\'');

        // Seed partner special page
        $this->addSql('INSERT INTO special_page (id, uuid, title, slug, identifier, content, show_in_menu, menu_order, created_at, updated_at) 
            VALUES (nextval(\'special_page_id_seq\'), gen_random_uuid(), \'Partenaires\', \'partenaires\', \'partner\', \'<p>Nos associations partenaires.</p>\', true, 50, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE partner_id_seq CASCADE');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DELETE FROM special_page WHERE identifier = \'partner\'');
    }
}
