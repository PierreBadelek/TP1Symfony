# LibraShelf - Système de Gestion de Bibliothèque

Application web de gestion de bibliothèque développée avec Symfony 7.x permettant la gestion des ouvrages, exemplaires, emprunts et utilisateurs.

## 🚀 Installation

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/PierreBadelek/TP1Symfony.git
cd TP1Symfony
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**

Le fichier `.env` est déjà configuré pour utiliser SQLite. Aucune modification n'est nécessaire pour le développement local.

4. **Créer la base de données**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Charger les données de test (optionnel mais recommandé)**
```bash
php bin/console doctrine:fixtures:load
```

Cette commande va créer :
- 1 administrateur
- 2 bibliothécaires
- 10 utilisateurs membres
- Des emprunts avec différents statuts (actifs, en retard, terminés)
- Des pénalités pour les retours en retard

6. **Lancer le serveur**
```bash
symfony serve
# OU
php -S localhost:8000 -t public/
```

7. **Accéder à l'application**

Ouvrez votre navigateur à l'adresse : `http://localhost:8000`

## 👥 Comptes de test

Après avoir chargé les fixtures, vous pouvez vous connecter avec les comptes suivants :

### Administrateur (tous les droits)
- **Email** : `admin@admin.com`
- **Mot de passe** : `admin`
- **Droits** : Gestion complète des utilisateurs, ouvrages, exemplaires et emprunts + configuration système

### Bibliothécaire (gestion bibliothèque)
- **Email** : `librairian@librairian.com`
- **Mot de passe** : `password1`
- **Droits** : Gestion des ouvrages, exemplaires, emprunts + consultation des utilisateurs

### Membre (utilisateur simple)
- **Email** : `user1@user1.com`
- **Mot de passe** : `password1`
- **Droits** : Consultation du catalogue, emprunt/retour de livres, gestion de ses propres emprunts

## ⏰ Scheduler (Tâches automatisées)

Le système inclut un scheduler qui exécute des tâches automatiques :

### Tâches planifiées

1. **Envoi de rappels** (tous les jours à 8h)
   - Rappel J-3 : 3 jours avant la date de retour
   - Rappel J-0 : Le jour de la date de retour
   - Rappel J+7 : 7 jours après un retard

2. **Purge des emprunts anciens** (tous les jours à 2h)
   - Nettoyage des données obsolètes

3. **Purge des logs d'audit** (tous les jours à 3h)
   - Suppression automatique des logs de plus de 50 jours

### Lancer l'application avec le scheduler

**Terminal 1 - Serveur web** :
```bash
symfony serve
# OU
php -S localhost:8000 -t public/
```

**Terminal 2 - Scheduler** :
```bash
php bin/console messenger:consume scheduler_default -vv
```
## 📦 Structure du projet

```
TP1Symfony/
├── config/              # Configuration Symfony
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée web
├── src/
│   ├── Controller/      # Contrôleurs (logique routes)
│   ├── Entity/          # Entités Doctrine (modèles)
│   ├── Form/            # Types de formulaires
│   ├── Repository/      # Requêtes base de données
│   ├── Service/         # Services métier
│   ├── Security/        # Voters (contrôle d'accès)
│   ├── Validator/       # Contraintes de validation personnalisées
│   ├── Message/         # Messages pour le scheduler
│   ├── MessageHandler/  # Handlers pour le scheduler
│   └── EventSubscriber/ # Abonnés aux événements Symfony
├── templates/           # Vues Twig
└── var/                 # Cache, logs, base SQLite
```

