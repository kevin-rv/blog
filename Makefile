# Makefile pour automatiser les tâches courantes dans un projet Symfony

.PHONY: help dev prod cache migrate

help:
	@echo "Commandes disponibles :"
	@echo "  make dev        - Installe les dépendances, applique les migrations et lance le serveur en mode développement"
	@echo "  make prod       - Configure le projet en mode production"
	@echo "  make cache      - Nettoie et chauffe le cache"
	@echo "  make migrate    - Applique les migrations de base de données"

dev:
	# Installation des dépendances, migrations et démarrage du serveur Symfony
	composer install
	php bin/console doctrine:migrations:migrate --no-interaction
	symfony serve
	@echo "Le projet est en mode développement, le serveur est lancé."

prod:
	composer install --no-dev --optimize-autoloader
	php bin/console cache:clear --env=prod --no-debug
	php bin/console cache:warmup
	php bin/console assets:install --symlink --relative
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Le projet est configuré pour la production."

cache:
	php bin/console cache:clear
	php bin/console cache:warmup
	@echo "Cache nettoyé et réchauffé."

migrate:
	php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Migrations appliquées avec succès."
