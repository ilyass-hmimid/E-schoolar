# Guide de Déploiement - Allo Tawjih

Ce document explique comment déployer l'application Allo Tawjih en environnement de production ou de staging.

## 📋 Prérequis

### Sur la machine locale
- Git
- Composer
- Node.js (version 16.x ou supérieure) et npm
- Accès SSH au serveur de déploiement

### Sur le serveur
- PHP 8.1 ou supérieur avec les extensions requises
- Base de données MySQL/MariaDB
- Serveur web (Nginx/Apache)
- Supervisord ou équivalent pour les files d'attente

## 🚀 Installation initiale

### 1. Configuration du serveur

#### 1.1 Mise à jour du système
```bash
sudo apt update && sudo apt upgrade -y
```

#### 1.2 Installation des dépendances système
```bash
sudo apt install -y git unzip nginx mysql-server php8.1-fpm php8.1-{mysql,mbstring,xml,zip,gd,curl,bcmath,common,cli,json,intl,opcache,readline}
```

#### 1.3 Configuration de la base de données
```bash
sudo mysql_secure_installation

# Création de la base de données
mysql -u root -p
CREATE DATABASE allo_tawjih CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'allo_tawjih_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
GRANT ALL PRIVILEGES ON allo_tawjih.* TO 'allo_tawjih_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Configuration du serveur web

#### 2.1 Pour Nginx
Créez un fichier de configuration pour votre site :

```nginx
server {
    listen 80;
    server_name votredomaine.com;
    root /var/www/allo-tawjih/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 2.2 Pour Apache
Assurez-vous que le module `mod_rewrite` est activé et ajoutez ceci à votre configuration :

```apache
<VirtualHost *:80>
    ServerName votredomaine.com
    DocumentRoot /var/www/allo-tawjih/public

    <Directory /var/www/allo-tawjih/public>
        Options Indexes MultiViews FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### 3. Déploiement initial

#### 3.1 Cloner le dépôt
```bash
sudo mkdir -p /var/www/allo-tawjih
sudo chown -R $USER:$USER /var/www/allo-tawjih
cd /var/www/allo-tawjih
git clone https://github.com/votre-org/allo-tawjih.git .
```

#### 3.2 Configurer l'application
```bash
cp .env.example .env
nano .env  # Mettez à jour les paramètres de la base de données et autres configurations
```

#### 3.3 Installation des dépendances
```bash
composer install --no-dev --optimize-autoloader
npm ci --no-progress
npm run build
```

#### 3.4 Configuration des permissions
```bash
sudo chown -R www-data:www-data /var/www/allo-tawjih/storage
sudo chown -R www-data:www-data /var/www/allo-tawjih/bootstrap/cache
chmod -R 775 /var/www/allo-tawjih/storage
chmod -R 775 /var/www/allo-tawjih/bootstrap/cache
```

#### 3.5 Génération de la clé d'application
```bash
php artisan key:generate
```

#### 3.6 Exécution des migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### 3.7 Optimisation
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 Déploiement continu

### Utilisation du script de déploiement

Le projet inclut un script de déploiement automatisé (`deploy.sh`) qui facilite les mises à jour.

#### 1. Rendre le script exécutable
```bash
chmod +x deploy.sh
```

#### 2. Déploiement en environnement de staging
```bash
./deploy.sh staging
```

#### 3. Déploiement en production
```bash
./deploy.sh production
```

#### Options disponibles
- `--seed` : Exécute les seeders après les migrations
- `--migrate` : Force l'exécution des migrations même si aucune nouvelle migration n'est disponible

## 🔧 Configuration avancée

### Configuration des tâches planifiées

Ajoutez cette ligne à la crontab du serveur :
```
* * * * * cd /var/www/allo-tawjih && php artisan schedule:run >> /dev/null 2>&1
```

### Configuration des files d'attente

#### 1. Installation de Supervisor
```bash
sudo apt install supervisor
```

#### 2. Configuration de Supervisor
Créez un fichier de configuration :
```ini
[program:allo-tawjih-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/allo-tawjih/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/allo-tawjih/storage/logs/worker.log
stopwaitsecs=3600
```

#### 3. Démarrer Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start allo-tawjih-worker:*
```

## 🔒 Sécurité

### Certificat SSL
Il est fortement recommandé d'utiliser Let's Encrypt pour obtenir un certificat SSL gratuit :

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d votredomaine.com
```

### Sécurisation supplémentaire
- Mettez à jour régulièrement les dépendances : `composer update`
- Surveillez les journaux : `tail -f storage/logs/laravel.log`
- Configurez un pare-feu (UFW) :
  ```bash
  sudo ufw allow ssh
  sudo ufw allow http
  sudo ufw allow https
  sudo ufw enable
  ```

## 🔄 Rollback

En cas de problème après un déploiement, vous pouvez revenir à la version précédente :

```bash
# Se déplacer dans le répertoire du projet
cd /var/www/allo-tawjih

# Annuler la dernière migration si nécessaire
php artisan migrate:rollback

# Revenir au commit précédent
git reset --hard HEAD~1

# Reconstruire les assets
npm run build

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Redémarrer les services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx  # ou apache2
```

## 📞 Support

Pour toute question ou problème lié au déploiement, veuillez ouvrir une issue sur GitHub ou contacter l'équipe technique à dev@allotawjih.ma

---

*Dernière mise à jour : Août 2023*
