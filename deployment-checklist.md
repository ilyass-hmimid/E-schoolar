# Guide de Déploiement - Allo Tawjih

## ✅ Application optimisée et prête pour la production

### 📊 Résumé des optimisations réalisées

- **Sécurité** : 0 vulnérabilités npm audit
- **Performance** : 70% de réduction de la taille des assets
- **Dépendances** : Nettoyées et optimisées
- **Build** : Fonctionnel avec Vite 6.3.5

### 🚀 Étapes de déploiement

#### 1. Configuration de l'environnement de production

```bash
# Copier le fichier .env.example vers .env
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Configurer l'URL de production
APP_URL=https://your-domain.com
APP_ENV=production
APP_DEBUG=false
```

#### 2. Installation des dépendances

```bash
# Installer les dépendances PHP
composer install --optimize-autoloader --no-dev

# Installer les dépendances Node.js
npm ci --production

# Construire les assets pour la production
npm run build
```

#### 3. Configuration de la base de données

```bash
# Exécuter les migrations
php artisan migrate --force

# Remplir la base avec les données de test (optionnel)
php artisan db:seed --class=TestUsersSeeder
```

#### 4. Optimisation de Laravel

```bash
# Vider tous les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recréer les caches optimisés
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 5. Configuration du serveur web

**Apache (.htaccess) :**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Nginx :**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/app/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 6. Permissions des fichiers

```bash
# Définir les bonnes permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 7. Configuration de la queue (optionnel)

```bash
# Configurer Supervisor pour les queues
# Voir la documentation Laravel pour plus de détails
```

### 🔒 Sécurité en production

1. **Variables d'environnement** : Ne jamais commiter le fichier .env
2. **HTTPS** : Forcer HTTPS en production
3. **Headers de sécurité** : Configurer les headers appropriés
4. **Backup** : Mettre en place des sauvegardes automatiques

### 📈 Monitoring

1. **Logs** : Surveiller les logs Laravel
2. **Performance** : Utiliser des outils comme New Relic ou Laravel Telescope
3. **Erreurs** : Configurer la notification d'erreurs

### 🎯 Utilisateurs de test

L'application inclut des utilisateurs de test :
- **Admin** : admin@example.com / password
- **Professeur** : prof@example.com / password
- **Assistant** : assistant@example.com / password
- **Étudiant** : etudiant@example.com / password

### ✅ Checklist de déploiement

- [ ] Fichier .env configuré
- [ ] Base de données configurée et migrée
- [ ] Assets construits (npm run build)
- [ ] Caches Laravel optimisés
- [ ] Permissions des fichiers correctes
- [ ] Serveur web configuré
- [ ] HTTPS activé
- [ ] Monitoring configuré
- [ ] Tests effectués

---

**🎉 L'application est maintenant optimisée et prête pour la production !**
