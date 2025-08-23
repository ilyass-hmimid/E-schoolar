# 🔍 Guide de Diagnostic - Problème des Dashboards par Rôle

## 🚨 **Problème identifié**

Après authentification, tous les utilisateurs (admin, professeur, assistant, élève) voient le même dashboard administrateur au lieu de leur dashboard spécifique.

## 🔧 **Solutions appliquées**

### **1. Mise à jour du TestUsersSeeder**
- Correction des emails pour correspondre au guide
- Utilisation des bons rôles enum

### **2. Ajout de debug dans DashboardController**
- Logging des informations utilisateur
- Vérification du rôle reçu

### **3. Routes de diagnostic ajoutées**
- `/test-users` : Vérifier tous les utilisateurs et leurs rôles
- `/test-auth` : Vérifier l'utilisateur connecté

## 🧪 **Étapes de diagnostic**

### **Étape 1 : Vérifier les utilisateurs dans la base**

1. Allez sur : `http://127.0.0.1:8000/test-users`
2. Vérifiez que les utilisateurs ont les bons rôles :
   ```json
   [
     {
       "id": 1,
       "name": "Admin Test",
       "email": "admin@example.com",
       "role": 1,
       "is_active": true
     },
     {
       "id": 2,
       "name": "Professeur Test",
       "email": "prof@example.com",
       "role": 2,
       "is_active": true
     }
   ]
   ```

### **Étape 2 : Recréer les utilisateurs de test**

Si les rôles ne sont pas corrects, exécutez :
```bash
php artisan db:seed --class=TestUsersSeeder
```

### **Étape 3 : Tester l'authentification**

1. Connectez-vous avec `admin@example.com` / `password`
2. Vérifiez les logs Laravel : `storage/logs/laravel.log`
3. Cherchez les entrées "Dashboard access" et "Dashboard result"

### **Étape 4 : Vérifier les pages de dashboard**

Assurez-vous que les pages existent :
- `resources/js/Pages/Dashboard/Admin/Index.vue`
- `resources/js/Pages/Dashboard/Professeur/Index.vue`
- `resources/js/Pages/Dashboard/Assistant/Index.vue`
- `resources/js/Pages/Dashboard/Eleve/Index.vue`

## 👥 **Utilisateurs de test corrects**

### **Admin**
- Email : `admin@example.com`
- Mot de passe : `password`
- Rôle : `1` (ADMIN)
- Dashboard attendu : Dashboard administrateur

### **Professeur**
- Email : `prof@example.com`
- Mot de passe : `password`
- Rôle : `2` (PROFESSEUR)
- Dashboard attendu : Dashboard professeur

### **Assistant**
- Email : `assistant@example.com`
- Mot de passe : `password`
- Rôle : `3` (ASSISTANT)
- Dashboard attendu : Dashboard assistant

### **Élève**
- Email : `etudiant@example.com`
- Mot de passe : `password`
- Rôle : `4` (ELEVE)
- Dashboard attendu : Dashboard élève

## 🔍 **Vérifications supplémentaires**

### **1. Vérifier l'enum RoleType**

```php
// Dans app/Enums/RoleType.php
case ADMIN = 1;
case PROFESSEUR = 2;
case ASSISTANT = 3;
case ELEVE = 4;
case PARENT = 5;
```

### **2. Vérifier la migration users**

Assurez-vous que la colonne `role` est bien un entier :
```sql
DESCRIBE users;
```

### **3. Vérifier les logs Laravel**

Après connexion, vérifiez `storage/logs/laravel.log` :
```bash
tail -f storage/logs/laravel.log
```

## 🛠️ **Commandes de réparation**

### **Recréer les utilisateurs de test**
```bash
php artisan db:seed --class=TestUsersSeeder
```

### **Nettoyer les caches**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **Vérifier les migrations**
```bash
php artisan migrate:status
```

## 🎯 **Flux attendu par rôle**

### **Admin (role = 1)**
1. Connexion → `adminDashboard()` → `Dashboard/Admin/Index.vue`
2. Interface : Statistiques générales, gestion complète

### **Professeur (role = 2)**
1. Connexion → `professeurDashboard()` → `Dashboard/Professeur/Index.vue`
2. Interface : Mes cours, mes élèves, mon salaire

### **Assistant (role = 3)**
1. Connexion → `assistantDashboard()` → `Dashboard/Assistant/Index.vue`
2. Interface : Gestion des présences, paiements

### **Élève (role = 4)**
1. Connexion → `eleveDashboard()` → `Dashboard/Eleve/Index.vue`
2. Interface : Mes notes, mes absences, mes paiements

## ⚠️ **Points d'attention**

1. **Vérifiez que les rôles sont des entiers** (1, 2, 3, 4) et non des chaînes
2. **Assurez-vous que les pages de dashboard existent**
3. **Vérifiez les logs pour identifier le problème exact**
4. **Testez en navigation privée pour éviter les problèmes de cache**

## 🚀 **Test final**

1. Ouvrez une fenêtre de navigation privée
2. Connectez-vous avec `admin@example.com` / `password`
3. Vérifiez que vous voyez le dashboard administrateur
4. Déconnectez-vous
5. Connectez-vous avec `prof@example.com` / `password`
6. Vérifiez que vous voyez le dashboard professeur
7. Répétez pour assistant et élève

---

**✅ Si le problème persiste, vérifiez les logs Laravel pour identifier la cause exacte.**
