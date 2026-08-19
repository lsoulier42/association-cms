<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519235841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename name to title and add subtitle to partner table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partner RENAME COLUMN name TO title');
        $this->addSql('ALTER TABLE partner ADD subtitle VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partner RENAME COLUMN title TO name');
        $this->addSql('ALTER TABLE partner DROP subtitle');
    }
}
