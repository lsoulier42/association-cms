<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260523211520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board_member ADD dons JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE board_member ADD comites JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE board_member ALTER dons DROP DEFAULT');
        $this->addSql('ALTER TABLE board_member ALTER comites DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board_member DROP dons');
        $this->addSql('ALTER TABLE board_member DROP comites');
    }
}
