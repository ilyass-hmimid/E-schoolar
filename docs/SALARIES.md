# Gestion des Salaires

Ce document décrit la fonctionnalité de gestion des salaires de l'application Allo Tawjih.

## Fonctionnalités

- **Calcul des salaires** : Calcul automatique des salaires des professeurs basé sur le nombre d'élèves et les configurations de prix par matière.
- **Gestion des configurations** : Configuration des prix unitaires et des commissions par matière.
- **Validation des paiements** : Suivi des paiements avec différents statuts (en attente, payé, annulé).
- **Exports** : Export des données de salaires aux formats Excel et PDF.

## Structure des données

### Modèle Salaire

- `id` : Identifiant unique
- `professeur_id` : Référence vers le professeur
- `matiere_id` : Référence vers la matière enseignée
- `mois_periode` : Mois de la période de paie (format: YYYY-MM)
- `nombre_eleves` : Nombre d'élèves pour la matière
- `prix_unitaire` : Prix par élève
- `commission_prof` : Pourcentage de commission du professeur
- `montant_brut` : Montant brut (nombre_eleves * prix_unitaire)
- `montant_commission` : Montant de la commission
- `montant_net` : Montant net à payer
- `statut` : Statut du salaire (en_attente, paye, annule)
- `date_paiement` : Date de paiement
- `commentaires` : Commentaires éventuels
- `created_at`, `updated_at`, `deleted_at` : Horodatages

### Modèle ConfigurationSalaire

- `id` : Identifiant unique
- `matiere_id` : Référence vers la matière
- `prix_unitaire` : Prix unitaire par élève
- `commission_prof` : Pourcentage de commission
- `prix_min` : Prix minimum (optionnel)
- `prix_max` : Prix maximum (optionnel)
- `est_actif` : Si la configuration est active
- `description` : Description de la configuration
- `created_at`, `updated_at`, `deleted_at` : Horodatages

## Utilisation

### Calculer les salaires

1. Accédez à la section "Calcul des salaires" dans le menu d'administration
2. Sélectionnez le mois pour lequel vous souhaitez calculer les salaires
3. Cliquez sur "Calculer les salaires"

### Gérer les configurations

1. Accédez à la section "Configuration des salaires"
2. Ajoutez ou modifiez les configurations de prix par matière
3. Activez/désactivez les configurations selon les besoins

### Valider un paiement

1. Accédez à la liste des salaires
2. Cliquez sur "Valider le paiement" pour un salaire en attente
3. Confirmez la validation

### Exporter les données

1. Utilisez les filtres pour afficher les salaires souhaités
2. Cliquez sur "Exporter en Excel" ou "Exporter en PDF"

## Routes API

- `GET /admin/salaires` : Liste des salaires
- `GET /admin/salaires/calculer` : Afficher le formulaire de calcul
- `POST /admin/salaires/calculer` : Lancer le calcul des salaires
- `GET /admin/salaires/{salaire}` : Afficher les détails d'un salaire
- `GET /admin/salaires/{salaire}/edit` : Afficher le formulaire d'édition
- `PUT /admin/salaires/{salaire}` : Mettre à jour un salaire
- `POST /admin/salaires/{salaire}/valider` : Valider le paiement
- `POST /admin/salaires/{salaire}/annuler` : Annuler un salaire
- `GET /admin/salaires/export/excel` : Exporter en Excel
- `GET /admin/salaires/export/pdf` : Exporter en PDF

## Tests

Des routes de test sont disponibles pour valider le bon fonctionnement :

- `GET /test/salaires/calculer/{mois?}` : Tester le calcul des salaires
- `POST /test/salaires/{salaire}/valider` : Tester la validation d'un paiement
- `POST /test/salaires/{salaire}/annuler` : Tester l'annulation d'un salaire

## Sécurité

- Toutes les routes sont protégées par le middleware d'authentification
- Seuls les utilisateurs avec le rôle 'admin' peuvent accéder à ces fonctionnalités
- Les validations sont en place pour assurer l'intégrité des données

## Améliorations futures

- Notifications par email pour les nouveaux salaires disponibles
- Intégration avec un système de paie externe
- Tableau de bord analytique des dépenses en salaires
- Historique des modifications des configurations de salaires
