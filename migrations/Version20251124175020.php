<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124175020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, date_action DATETIME NOT NULL, "action" VARCHAR(100) NOT NULL, entite VARCHAR(100) NOT NULL, entite_id INTEGER DEFAULT NULL, details CLOB DEFAULT NULL, ip_address VARCHAR(45) DEFAULT NULL, CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F6E1C0F5A76ED395 ON audit_log (user_id)');
        $this->addSql('CREATE TABLE bibliotheque_config (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cle VARCHAR(100) NOT NULL, valeur CLOB NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3B6923641401D17 ON bibliotheque_config (cle)');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, exemplaire_id INTEGER NOT NULL, date_emprunt DATETIME NOT NULL, date_retour_prevue DATETIME NOT NULL, date_retour_effective DATETIME DEFAULT NULL, statut VARCHAR(50) NOT NULL, date_rappel_j3 DATETIME DEFAULT NULL, date_rappel_j0 DATETIME DEFAULT NULL, date_rappel_j7 DATETIME DEFAULT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE INDEX IDX_364071D75843AA21 ON emprunt (exemplaire_id)');
        $this->addSql('CREATE TABLE penalite (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, emprunt_id INTEGER NOT NULL, montant NUMERIC(10, 2) NOT NULL, date_creation DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, date_paiement DATETIME DEFAULT NULL, jours_retard INTEGER NOT NULL, CONSTRAINT FK_C62D8C5DAE7FEF94 FOREIGN KEY (emprunt_id) REFERENCES emprunt (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C62D8C5DAE7FEF94 ON penalite (emprunt_id)');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, ouvrage_id INTEGER NOT NULL, date_reservation DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, date_notification DATETIME DEFAULT NULL, date_expiration DATETIME DEFAULT NULL, position INTEGER NOT NULL, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495515D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C8495515D884B5 ON reservation (ouvrage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE bibliotheque_config');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('DROP TABLE penalite');
        $this->addSql('DROP TABLE reservation');
    }
}
