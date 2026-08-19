<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519181520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD show_in_menu BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE article ADD menu_order INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE category ADD show_in_menu BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE category ADD menu_order INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP show_in_menu');
        $this->addSql('ALTER TABLE article DROP menu_order');
        $this->addSql('ALTER TABLE category DROP show_in_menu');
        $this->addSql('ALTER TABLE category DROP menu_order');
    }
}
