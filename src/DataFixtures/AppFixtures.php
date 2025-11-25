<?php

namespace App\DataFixtures;

use App\Entity\Emprunt;
use App\Entity\Exemplaire;
use App\Entity\Penalite;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Créer des utilisateurs
        $users = [];

        // Admin (déjà créé mais on peut en rajouter un autre)
        $admin = new User();
        $admin->setEmail('superadmin@librashelf.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);
        $users[] = $admin;

        // Bibliothécaires
        for ($i = 1; $i <= 2; $i++) {
            $librarian = new User();
            $librarian->setEmail("librarian{$i}@librashelf.com");
            $librarian->setRoles(['ROLE_LIBRARIAN']);
            $librarian->setPassword($this->passwordHasher->hashPassword($librarian, 'password'));
            $manager->persist($librarian);
            $users[] = $librarian;
        }

        // Utilisateurs normaux
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@librashelf.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();

        // 2. Récupérer tous les exemplaires disponibles
        $exemplaires = $manager->getRepository(Exemplaire::class)->findAll();

        if (empty($exemplaires)) {
            echo "⚠️ Aucun exemplaire trouvé. Créez d'abord des ouvrages et exemplaires.\n";
            return;
        }

        // 3. Créer des emprunts
        $now = new \DateTime();

        // Emprunts en cours (pas de retard)
        for ($i = 0; $i < min(5, count($exemplaires)); $i++) {
            $exemplaire = $exemplaires[$i];
            if (!$exemplaire->isDisponible()) continue;

            $emprunt = new Emprunt();
            $emprunt->setUser($users[array_rand($users)]);
            $emprunt->setExemplaire($exemplaire);
            $emprunt->setDateEmprunt((clone $now)->modify('-5 days'));
            $emprunt->setDateRetourPrevue((clone $now)->modify('+16 days')); // 21 jours - 5 jours
            $emprunt->setStatut('en_cours');

            $exemplaire->setDisponible(false);

            $manager->persist($emprunt);
        }

        // Emprunts avec retard léger (2-5 jours)
        for ($i = 5; $i < min(10, count($exemplaires)); $i++) {
            $exemplaire = $exemplaires[$i];
            if (!$exemplaire->isDisponible()) continue;

            $joursRetard = rand(2, 5);
            $emprunt = new Emprunt();
            $emprunt->setUser($users[array_rand($users)]);
            $emprunt->setExemplaire($exemplaire);
            $emprunt->setDateEmprunt((clone $now)->modify('-30 days'));
            $emprunt->setDateRetourPrevue((clone $now)->modify("-{$joursRetard} days"));
            $emprunt->setStatut('en_retard');

            // Créer la pénalité
            $penalite = new Penalite();
            $penalite->setEmprunt($emprunt);
            $penalite->setJoursRetard($joursRetard);
            $penalite->setMontant((string)($joursRetard * 0.50));
            $penalite->setDateCreation((clone $now)->modify("-{$joursRetard} days"));
            $penalite->setStatut('impayee');

            $exemplaire->setDisponible(false);

            $manager->persist($emprunt);
            $manager->persist($penalite);
        }

        // Emprunts avec gros retard (10-30 jours)
        for ($i = 10; $i < min(15, count($exemplaires)); $i++) {
            $exemplaire = $exemplaires[$i];
            if (!$exemplaire->isDisponible()) continue;

            $joursRetard = rand(10, 30);
            $emprunt = new Emprunt();
            $emprunt->setUser($users[array_rand($users)]);
            $emprunt->setExemplaire($exemplaire);
            $emprunt->setDateEmprunt((clone $now)->modify('-60 days'));
            $emprunt->setDateRetourPrevue((clone $now)->modify("-{$joursRetard} days"));
            $emprunt->setStatut('en_retard');

            // Créer la pénalité
            $penalite = new Penalite();
            $penalite->setEmprunt($emprunt);
            $penalite->setJoursRetard($joursRetard);
            $penalite->setMontant((string)($joursRetard * 0.50));
            $penalite->setDateCreation((clone $now)->modify("-{$joursRetard} days"));
            $penalite->setStatut('impayee');

            $exemplaire->setDisponible(false);

            $manager->persist($emprunt);
            $manager->persist($penalite);
        }

        // Emprunts terminés (historique)
        for ($i = 15; $i < min(25, count($exemplaires)); $i++) {
            $exemplaire = $exemplaires[$i];

            $emprunt = new Emprunt();
            $emprunt->setUser($users[array_rand($users)]);
            $emprunt->setExemplaire($exemplaire);
            $emprunt->setDateEmprunt((clone $now)->modify('-40 days'));
            $emprunt->setDateRetourPrevue((clone $now)->modify('-19 days'));
            $emprunt->setDateRetourEffective((clone $now)->modify('-20 days'));
            $emprunt->setStatut('termine');

            $manager->persist($emprunt);
        }

        // Emprunts terminés avec retard (historique avec pénalités payées)
        for ($i = 25; $i < min(30, count($exemplaires)); $i++) {
            $exemplaire = $exemplaires[$i];

            $joursRetard = rand(2, 10);
            $emprunt = new Emprunt();
            $emprunt->setUser($users[array_rand($users)]);
            $emprunt->setExemplaire($exemplaire);
            $emprunt->setDateEmprunt((clone $now)->modify('-50 days'));
            $emprunt->setDateRetourPrevue((clone $now)->modify('-30 days'));
            $emprunt->setDateRetourEffective((clone $now)->modify('-20 days'));
            $emprunt->setStatut('termine_avec_retard');

            // Créer la pénalité payée
            $penalite = new Penalite();
            $penalite->setEmprunt($emprunt);
            $penalite->setJoursRetard($joursRetard);
            $penalite->setMontant((string)($joursRetard * 0.50));
            $penalite->setDateCreation((clone $now)->modify('-20 days'));
            $penalite->setStatut('payee');
            $penalite->setDatePaiement((clone $now)->modify('-15 days'));

            $manager->persist($emprunt);
            $manager->persist($penalite);
        }

        $manager->flush();

        echo "✅ Fixtures chargées avec succès !\n";
        echo "👥 Utilisateurs créés : " . count($users) . "\n";
        echo "📚 Emprunts créés avec divers statuts (en cours, retards, terminés)\n";
        echo "\n";
        echo "🔑 Comptes de test :\n";
        echo "   - superadmin@librashelf.com / admin (ROLE_ADMIN)\n";
        echo "   - librarian1@librashelf.com / password (ROLE_LIBRARIAN)\n";
        echo "   - user1@librashelf.com / password (ROLE_USER)\n";
        echo "   - ... user2 à user10 avec même mot de passe\n";
    }
}
