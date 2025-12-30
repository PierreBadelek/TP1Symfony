<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230191704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaire AS SELECT id, ouvrage_id, cote, etat, disponible FROM exemplaire');
        $this->addSql('DROP TABLE exemplaire');
        $this->addSql('CREATE TABLE exemplaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote INTEGER NOT NULL, etat VARCHAR(255) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_5EF83C9215D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaire (id, ouvrage_id, cote, etat, disponible) SELECT id, ouvrage_id, cote, etat, disponible FROM __temp__exemplaire');
        $this->addSql('DROP TABLE __temp__exemplaire');
        $this->addSql('CREATE INDEX IDX_5EF83C9215D884B5 ON exemplaire (ouvrage_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, editeur, isbn, annee, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(125) NOT NULL, editeur VARCHAR(125) DEFAULT NULL, isbn VARCHAR(20) NOT NULL, annee INTEGER NOT NULL, resume VARCHAR(512) NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, editeur, isbn, annee, resume) SELECT id, titre, editeur, isbn, annee, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaire AS SELECT id, ouvrage_id, cote, etat, disponible FROM exemplaire');
        $this->addSql('DROP TABLE exemplaire');
        $this->addSql('CREATE TABLE exemplaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ouvrage_id INTEGER NOT NULL, cote INTEGER NOT NULL, etat VARCHAR(16) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_5EF83C9215D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaire (id, ouvrage_id, cote, etat, disponible) SELECT id, ouvrage_id, cote, etat, disponible FROM __temp__exemplaire');
        $this->addSql('DROP TABLE __temp__exemplaire');
        $this->addSql('CREATE INDEX IDX_5EF83C9215D884B5 ON exemplaire (ouvrage_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, editeur, isbn, annee, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(125) NOT NULL, editeur VARCHAR(125) DEFAULT NULL, isbn INTEGER NOT NULL, annee INTEGER NOT NULL, resume VARCHAR(512) NOT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, editeur, isbn, annee, resume) SELECT id, titre, editeur, isbn, annee, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }
}
