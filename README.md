# Blog Symfony

Ce projet est un site web de type blog, développé avec Symfony et Twig Bootstrap. Il offre une interface simple et efficace, centrée sur l'essentiel, avec des fonctionnalités adaptées pour un usage intuitif. 

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Passer en mode développement](#passer-en-mode-developpement)
  - [Passer en mode production](#passer-en-mode-production)
  - [Utilisation du Makefile](#utilisation-du-makefile)
- [Mode Admin](#mode-admin)
- [Auteur](#auteur)

## Fonctionnalités

- Création d'utilisateur avec un username et un mot de passe.
- Système de gestion des utilisateurs (authentification et rôles).
- CRUD articles, avec association d'une catégorie et ajout d'une photo principale.
- CRUD catégories (mode admin).
- Formulaire de contact avec gestion des messages dans le back-office.
- Mode admin : gestion des articles, catégories et messages reçus.

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- PHP 8.2 ou version supérieure
- Composer
- Symfony CLI
- Serveur web (Apache ou Nginx)
- Base de données (MySQL ou autre)
- Mailer configuré pour l'envoi d'e-mails

## Installation

1. Clonez le dépôt :
```bash
   git clone  https://github.com/kevin-rv/blog.git
```
2. Accédez au dossier du projet :
```bash
cd blog
```

3. Installez les dépendances :
```bash 
composer install
```

4. Configurez les variables d'environnement :

## Configuration : 

Symfony fonctionne avec deux environnements : 

- **Dev** : Utilisé pour le mode développement. Affiche les messages d'erreur et active le débogage. 
- **prod** : Utilisé pour la production. Optimisé pour les performances 

### Passer en mode développement : 

1. Assurez vous que vous utilisez le fichier `.env`ou `.env.local` pour configurer l'environnement.

2. Dans le fichier `.env`:

```env
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=your_generated_secret_key
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_base" 
MAILER_DSN="smtp://user:password@smtp.example.com"
MAILER_FROM_ADDRESS="email@email.com"
```

#### Générer une clé secrète aléatoire : 

Si vous devez générer un APP_SECRET, vous pouvez utiliser la commande Symfony CLI pour le faire :
```bash
symfony secret:generate
```

3. Lancez le serveur Symfony : 
```bash
symfony serve
```
### Passer en mode production : 

1. Assurez vous que vous utilisez le fichier `.env`ou `.env.local` pour configurer l'environnement.

2. Dans le fichier `.env`,  configurez les variables suivantes :

```env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=your_generated_secret_key
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_base" 
MAILER_DSN="smtp://user:password@smtp.example.com"
MAILER_FROM_ADDRESS="email@email.com"
```
#### Générer une clé secrète aléatoire : 

Si vous devez générer un APP_SECRET, vous pouvez utiliser la commande Symfony CLI pour le faire :
```bash
symfony secret:generate
```

3. Installez les dépendances nécessaires en production :

```bash
composer install --no-dev --optimize-autoloader  
```

4. Nettoyez le cache :
```bash
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup
```

5. Déployez les assets pour la production :
```bash 
php bin/console assets:install --symlink --relative
```

6. Appliquez les migrations pour créer et mettre à jour la base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

7. Si vous utilisez Apache, installez le pack Symfony pour générer automatiquement un fichier .htaccess :

```bash
composer require symfony/apache-pack
```

## Utilisation du Makefile

Attention : Assurez-vous d'avoir installé make sur votre machine avant d'utiliser ces commandes. Le Makefile permet d'automatiser certaines tâches courantes de configuration et de gestion du projet.

Ce projet utilise un Makefile pour automatiser certaines tâches courantes. Voici les commandes disponibles : 

### Commandes disponibles
- make dev : Installe les dépendances, applique les migrations et lance le serveur en mode développement.
- make prod : Configure le projet en mode production, installe les dépendances, optimise les performances et nettoie les caches.
- make cache : Nettoie et réchauffe le cache du projet.
- make migrate : Applique les migrations de la base de données.
Exemple d'utilisation
Après avoir cloné le projet et installé les dépendances avec composer install, vous pouvez utiliser les commandes suivantes selon l'environnement souhaité.

### Exemple d'utilisation
Après avoir cloné le projet et configuré vos variables dans le fichier `.env`

#### Mode développement :
```bash
make dev
```
Cette commande va : 

- Installer les dépendances.
- Appliquer les migrations de la base de données.
- Lancer le serveur Symfony en mode développement.

#### Mode production : 
```bash
make prod
```
Cette commande va : 

- Installer les dépendances en mode production (--no-dev).
- Optimiser l'autoloader.
- Nettoyer et préchauffer le cache pour la production.
- Déployer les assets pour la production.

#### Gestion du cache :
```bash
make cache
```
Cette commande va nettoyer et réchauffer le cache de l'application.

#### Appliquer les migrations :
```bash
make migrate
```
## Mode Admin

Pour accéder aux fonctionnalités d'administration (gestion des catégories, articles et messages) :

1. Créez un utilisateur via le formulaire d'inscription disponible sur le site.
2. Modifiez le rôle de l'utilisateur pour `ROLE_ADMIN` directement dans la base de données ou via une commande :
```sql
   UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE id = 1;
```

## Auteur

Hoarau Kévin

- [GitHub](https://github.com/kevin-rv)
- [LinkedIn](https://www.linkedin.com/in/k%C3%A9vin-hoarau/)
- [Discord](https://discord.com/users/keyru69)