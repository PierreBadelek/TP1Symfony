<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024085819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, editeur, isbn, annÃee, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(125) NOT NULL, editeur VARCHAR(125) DEFAULT NULL, isbn INTEGER NOT NULL, annee INTEGER NOT NULL, resume VARCHAR(512) NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, editeur, isbn, annee, resume) SELECT id, titre, editeur, isbn, annÃee, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, editeur, isbn, annee, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(125) NOT NULL, editeur VARCHAR(125) DEFAULT NULL, isbn VARCHAR(125) NOT NULL, annÃee INTEGER NOT NULL, resume VARCHAR(512) NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, editeur, isbn, annÃee, resume) SELECT id, titre, editeur, isbn, annee, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }
}
