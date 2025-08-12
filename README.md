# Allo Tawjih - Plateforme de Gestion Scolaire

[![Tests](https://github.com/votre-org/allo-tawjih/actions/workflows/laravel.yml/badge.svg)](https://github.com/votre-org/allo-tawjih/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com/)

Allo Tawjih est une application web complète de gestion scolaire conçue pour faciliter l'administration des établissements éducatifs. Cette plateforme permet de gérer les étudiants, les professeurs, les classes, les matières, les notes, les absences et les paiements de manière efficace et sécurisée.

## 🚀 Fonctionnalités principales

- **Gestion des utilisateurs** avec rôles multiples (Admin, Professeur, Assistant, Étudiant, Parent)
- **Gestion des classes et matières** avec suivi des effectifs
- **Gestion des notes et évaluations** avec calcul automatique des moyennes
- **Suivi des absences et retards** avec notifications
- **Gestion des paiements** pour les étudiants et des salaires pour les professeurs
- **Packs d'heures** pour une flexibilité de paiement
- **Tableau de bord** avec indicateurs clés et graphiques
- **Système de rapports** avec export CSV, PDF et Excel
- **Recherche avancée** dans toutes les entités
- **Interface responsive** adaptée à tous les appareils

## 🛠️ Prérequis techniques

- PHP 8.1 ou supérieur
- Composer 2.0 ou supérieur
- Node.js 16.x ou supérieur avec npm 8.x ou supérieur
- Base de données MySQL 8.0 ou supérieur / MariaDB 10.3 ou supérieur
- Serveur web (Apache/Nginx) avec support PHP
- Extensions PHP requises : BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Intl, PCNTL, SQLite

## 🚀 Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-org/allo-tawjih.git
   cd allo-tawjih
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Installer les dépendances JavaScript**
   ```bash
   npm install
   npm run build
   ```

4. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Configurer les variables d'environnement dans le fichier `.env` :
   ```
   APP_NAME="Allo Tawjih"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://votre-domaine.com
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=allo_tawjih
   DB_USERNAME=utilisateur_db
   DB_PASSWORD=votre_mot_de_passe
   ```

5. **Exécuter les migrations et les seeders**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=RoleSeeder
   php artisan db:seed --class=AdminUserSeeder
   ```

6. **Configurer le stockage**
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   chown -R $USER:www-data storage bootstrap/cache
   ```

7. **Configurer la tâche planifiée (optionnel)**
   ```
   * * * * * cd /chemin/vers/allo-tawjih && php artisan schedule:run >> /dev/null 2>&1
   ```

## 🔒 Sécurité

L'application intègre plusieurs couches de sécurité :
- Protection CSRF
- Protection XSS (nettoyage des entrées)
- Protection contre les attaques par force brute
- Headers de sécurité HTTP (CSP, HSTS, etc.)
- Chiffrement des données sensibles
- Gestion fine des permissions avec les politiques Laravel

## 🧪 Tests

L'application est couverte par des tests unitaires, d'intégration et E2E :

```bash
# Exécuter les tests PHPUnit
php artisan test

# Exécuter les tests Dusk (E2E)
php artisan dusk
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment contribuer :

1. Forkez le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub ou contacter l'équipe de développement à support@allotawjih.ma

## 📊 Aperçu

![Tableau de bord](public/images/dashboard-preview.png)
*Capture d'écran du tableau de bord administrateur*

---

<div align="center">
  <p>Développé avec ❤️ par l'équipe Allo Tawjih</p>
  <small>© 2023 Allo Tawjih. Tous droits réservés.</small>
</div>
