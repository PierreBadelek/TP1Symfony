<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026084622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exemplaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote INTEGER NOT NULL, etat VARCHAR(16) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_5EF83C9215D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5EF83C9215D884B5 ON exemplaire (ouvrage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE exemplaire');
    }
}
