<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024081241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(125) NOT NULL)');
        $this->addSql('CREATE TABLE categorie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, categorie_nom VARCHAR(128) NOT NULL)');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(125) NOT NULL, editeur VARCHAR(125) DEFAULT NULL, isbn VARCHAR(125) NOT NULL, annÃee INTEGER NOT NULL, resume VARCHAR(512) NOT NULL)');
        $this->addSql('CREATE TABLE ouvrage_categorie (ouvrage_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, categorie_id), CONSTRAINT FK_BF5721A615D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5721A6BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5721A615D884B5 ON ouvrage_categorie (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_BF5721A6BCF5E72D ON ouvrage_categorie (categorie_id)');
        $this->addSql('CREATE TABLE ouvrage_auteur (ouvrage_id INTEGER NOT NULL, auteur_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, auteur_id), CONSTRAINT FK_3E39E6E815D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3E39E6E860BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3E39E6E815D884B5 ON ouvrage_auteur (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_3E39E6E860BB6FE6 ON ouvrage_auteur (auteur_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('DROP TABLE ouvrage_categorie');
        $this->addSql('DROP TABLE ouvrage_auteur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
