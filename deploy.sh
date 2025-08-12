#!/bin/bash

# Script de déploiement Allo Tawjih
# Utilisation : ./deploy.sh [environnement]
# Exemple : ./deploy.sh production

set -e  # Arrête le script en cas d'erreur

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérification des arguments
if [ $# -ne 1 ]; then
    echo -e "${RED}Erreur: Argument manquant.${NC}"
    echo -e "Usage: $0 [environnement]"
    echo -e "Environnements disponibles: production, staging"
    exit 1
fi

ENV=$1

# Vérification de l'environnement
if [[ ! "$ENV" =~ ^(production|staging)$ ]]; then
    echo -e "${RED}Erreur: Environnement non valide.${NC}"
    echo -e "Environnements disponibles: production, staging"
    exit 1
fi

# Configuration selon l'environnement
case $ENV in
    production)
        REMOTE_USER="user_prod"
        REMOTE_HOST="votre-serveur.com"
        REMOTE_PATH="/var/www/allo-tawjih"
        BRANCH="main"
        ;;
    staging)
        REMOTE_USER="user_staging"
        REMOTE_HOST="staging.votre-serveur.com"
        REMOTE_PATH="/var/www/allo-tawjih-staging"
        BRANCH="develop"
        ;;
esac

# Fonction pour afficher les messages d'information
info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

# Fonction pour afficher les avertissements
warn() {
    echo -e "${YELLOW}[ATTENTION]${NC} $1"
}

# Fonction pour afficher les erreurs et quitter
error() {
    echo -e "${RED}[ERREUR]${NC} $1"
    exit 1
}

# Vérification des prérequis
check_requirements() {
    info "Vérification des prérequis..."
    
    # Vérification de SSH
    if ! command -v ssh &> /dev/null; then
        error "SSH n'est pas installé"
    fi
    
    # Vérification de Git
    if ! command -v git &> /dev/null; then
        error "Git n'est pas installé"
    fi
    
    # Vérification de Composer
    if ! command -v composer &> /dev/null; then
        error "Composer n'est pas installé"
    fi
    
    # Vérification de Node.js et npm
    if ! command -v node &> /dev/null || ! command -v npm &> /dev/null; then
        error "Node.js et/ou npm ne sont pas installés"
    fi
    
    info "Tous les prérequis sont satisfaits."
}

# Mise à jour du code source
update_code() {
    info "Mise à jour du code source..."
    
    # Récupérer les dernières modifications
    git fetch origin $BRANCH
    
    # Vérifier si des modifications locales non commitées
    if [[ -n $(git status -s) ]]; then
        warn "Il y a des modifications locales non commitées. Elles seront écrasées."
        git stash
        STASHED=true
    fi
    
    # Basculer sur la branche appropriée
    git checkout $BRANCH
    
    # Mettre à jour la branche
    git pull origin $BRANCH
    
    info "Code source mis à jour avec succès."
}

# Installation des dépendances
install_dependencies() {
    info "Installation des dépendances..."
    
    # Installer les dépendances PHP
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Installer les dépendances JavaScript
    npm ci --no-progress
    
    # Compiler les assets
    npm run build
    
    info "Dépendances installées avec succès."
}

# Configuration de l'application
setup_application() {
    info "Configuration de l'application..."
    
    # Copier le fichier .env.example si .env n'existe pas
    if [ ! -f .env ]; then
        cp .env.example .env
        php artisan key:generate
    fi
    
    # Mettre à jour le fichier .env avec les configurations spécifiques à l'environnement
    if [ "$ENV" = "production" ]; then
        sed -i 's/APP_ENV=local/APP_ENV=production/g' .env
        sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' .env
    else
        sed -i 's/APP_ENV=local/APP_ENV=staging/g' .env
        sed -i 's/APP_DEBUG=true/APP_DEBUG=true/g' .env
    fi
    
    # Mettre à jour l'URL de l'application
    sed -i "s|APP_URL=.*|APP_URL=https://${REMOTE_HOST}|" .env
    
    info "Application configurée avec succès."
}

# Exécution des migrations
database_migrations() {
    info "Exécution des migrations de base de données..."
    
    # Exécuter les migrations
    php artisan migrate --force
    
    # Si c'est un nouvel environnement, exécuter les seeders
    if [ "$1" = "--seed" ]; then
        php artisan db:seed --force
    fi
    
    info "Migrations exécutées avec succès."
}

# Optimisation de l'application
optimize_application() {
    info "Optimisation de l'application..."
    
    # Optimiser le chargement des classes
    composer dump-autoload --optimize
    
    # Mettre en cache la configuration et les routes
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Mettre en place le lien de stockage
    php artisan storage:link
    
    # Nettoyer le cache
    php artisan optimize:clear
    
    # Définir les bonnes permissions
    chmod -R 755 storage bootstrap/cache
    chmod -R 775 storage/
    chmod -R 775 bootstrap/cache/
    
    info "Application optimisée avec succès."
}

# Redémarrage des services
restart_services() {
    info "Redémarrage des services..."
    
    # Redémarrer le service PHP-FPM
    if command -v systemctl &> /dev/null; then
        sudo systemctl restart php8.1-fpm
    fi
    
    # Redémarrer le serveur web
    if command -v nginx &> /dev/null; then
        sudo systemctl restart nginx
    elif command -v apache2 &> /dev/null; then
        sudo systemctl restart apache2
    fi
    
    # Redémarrer la file d'attente (si utilisée)
    php artisan queue:restart
    
    info "Services redémarrés avec succès."
}

# Vérification de l'application
check_application() {
    info "Vérification de l'application..."
    
    # Vérifier la configuration
    php artisan about
    
    # Vérifier les routes
    php artisan route:list
    
    # Vérifier le stockage
    php artisan storage:link --relative
    
    info "Vérification terminée avec succès."
}

# Déploiement sur le serveur
deploy_to_server() {
    info "Démarrage du déploiement sur ${ENV}..."
    
    # Vérifier les prérequis
    check_requirements
    
    # Mettre à jour le code source
    update_code
    
    # Installer les dépendances
    install_dependencies
    
    # Configurer l'application
    setup_application
    
    # Exécuter les migrations
    if [ "$1" = "--seed" ]; then
        database_migrations --seed
    else
        database_migrations
    fi
    
    # Optimiser l'application
    optimize_application
    
    # Redémarrer les services
    restart_services
    
    # Vérifier l'application
    check_application
    
    # Restaurer les modifications locales si nécessaire
    if [ "$STASHED" = true ]; then
        git stash pop
    fi
    
    echo -e "${GREEN}✅ Déploiement terminé avec succès sur ${ENV}!${NC}"
}

# Exécution du déploiement
deploy_to_server "$@"
