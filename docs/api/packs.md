# API de Gestion des Packs

Ce document décrit les endpoints de l'API pour la gestion des packs dans l'application.

## Base URL

Toutes les requêtes doivent être préfixées par `/api/v1`.

## Authentification

Toutes les requêtes nécessitent une authentification via un token Bearer.

```
Authorization: Bearer {token}
```

## Types de Packs

Les types de packs disponibles sont définis dans le fichier de configuration `config/packs.php`.

## Endpoints

### Liste des Packs

Récupère une liste paginée des packs avec possibilité de filtrage et de tri.

```
GET /packs
```

#### Paramètres de requête

| Paramètre  | Type    | Requis | Description                                      |
|------------|---------|--------|--------------------------------------------------|
| search     | string  | Non    | Terme de recherche dans le nom ou la description |
| type       | string  | Non    | Filtre par type de pack                         |
| status     | string  | Non    | Filtre par statut (active, inactive)            |
| sort_by    | string  | Non    | Champ de tri (nom, type, prix, created_at)       |
| sort_order | string  | Non    | Ordre de tri (asc, desc)                        |
| per_page   | integer | Non    | Nombre d'éléments par page (1-100)              |

#### Réponse

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Pack Découverte",
      "slug": "pack-decouverte",
      "description": "Pack d'initiation aux cours",
      "type": {
        "code": "cours",
        "name": "Cours",
        "icon": "academic-cap",
        "color": "indigo"
      },
      "prix": 999.99,
      "prix_formate": "999,99 DH",
      "prix_promo": 799.99,
      "prix_promo_formate": "799,99 DH",
      "prix_affichage": 799.99,
      "prix_affichage_formate": "799,99 DH",
      "remise_pourcentage": 20,
      "duree_jours": 30,
      "est_actif": true,
      "est_populaire": true,
      "created_at": "2023-08-24T12:00:00Z",
      "updated_at": "2023-08-24T12:00:00Z",
      "matieres": [
        {
          "id": 1,
          "nom": "Mathématiques",
          "pivot": {
            "nombre_heures_par_matiere": 10
          }
        }
      ]
    }
  ],
  "meta": {
    "total": 15,
    "per_page": 10,
    "current_page": 1,
    "last_page": 2,
    "from": 1,
    "to": 10,
    "filters": {
      "search": "découverte",
      "type": "cours",
      "status": "active"
    },
    "sort": {
      "by": "prix",
      "order": "asc"
    }
  },
  "links": {
    "first": "https://api.example.com/packs?page=1",
    "last": "https://api.example.com/packs?page=2",
    "prev": null,
    "next": "https://api.example.com/packs?page=2"
  }
}
```

### Créer un Pack

Crée un nouveau pack.

```
POST /packs
```

#### Corps de la requête

```json
{
  "nom": "Pack Premium",
  "description": "Pack premium avec accès illimité",
  "type": "abonnement",
  "prix": 1999.99,
  "prix_promo": 1499.99,
  "duree_jours": 365,
  "est_actif": true,
  "est_populaire": true,
  "matieres": [
    {
      "id": 1,
      "nombre_heures": 20
    },
    {
      "id": 2,
      "nombre_heures": 15
    }
  ]
}
```

#### Réponse

```json
{
  "message": "Pack créé avec succès",
  "data": {
    "id": 2,
    "nom": "Pack Premium",
    "type": "abonnement",
    "prix": 1999.99,
    "prix_promo": 1499.99,
    "duree_jours": 365,
    "est_actif": true,
    "est_populaire": true,
    "created_at": "2023-08-24T13:00:00Z",
    "updated_at": "2023-08-24T13:00:00Z"
  }
}
```

### Récupérer un Pack

Récupère les détails d'un pack spécifique.

```
GET /packs/{id}
```

#### Réponse

```json
{
  "data": {
    "id": 1,
    "nom": "Pack Découverte",
    "type": "cours",
    "prix": 999.99,
    "prix_promo": 799.99,
    "duree_jours": 30,
    "est_actif": true,
    "est_populaire": true,
    "created_at": "2023-08-24T12:00:00Z",
    "updated_at": "2023-08-24T12:00:00Z",
    "matieres": [
      {
        "id": 1,
        "nom": "Mathématiques",
        "pivot": {
          "nombre_heures_par_matiere": 10
        }
      }
    ]
  }
}
```

### Mettre à jour un Pack

Met à jour un pack existant.

```
PUT /packs/{id}
```

#### Corps de la requête

```json
{
  "nom": "Pack Découverte Pro",
  "prix": 1199.99,
  "prix_promo": 999.99,
  "est_actif": true
}
```

#### Réponse

```json
{
  "message": "Pack mis à jour avec succès",
  "data": {
    "id": 1,
    "nom": "Pack Découverte Pro",
    "type": "cours",
    "prix": 1199.99,
    "prix_promo": 999.99,
    "duree_jours": 30,
    "est_actif": true,
    "est_populaire": true,
    "updated_at": "2023-08-24T14:00:00Z"
  }
}
```

### Supprimer un Pack

Supprime un pack. Ne peut pas être supprimé s'il est associé à des ventes ou inscriptions.

```
DELETE /packs/{id}
```

#### Réponse

```json
{
  "message": "Pack supprimé avec succès"
}
```

### Activer/Désactiver un Pack

Active ou désactive un pack.

```
POST /packs/{id}/toggle-status
```

#### Réponse

```json
{
  "message": "Pack activé avec succès",
  "data": {
    "id": 1,
    "est_actif": true
  }
}
```

### Mettre en avant un Pack

Met en avant ou retire de la mise en avant un pack.

```
POST /packs/{id}/toggle-popularity
```

#### Réponse

```json
{
  "message": "Pack mis en avant avec succès",
  "data": {
    "id": 1,
    "est_populaire": true
  }
}
```

### Dupliquer un Pack

Crée une copie d'un pack existant avec le statut inactif et non mis en avant.

```
POST /packs/{id}/duplicate
```

#### Réponse

```json
{
  "message": "Pack dupliqué avec succès",
  "data": {
    "id": 2,
    "nom": "Pack Découverte (Copie)",
    "est_actif": false,
    "est_populaire": false
  }
}
```

### Statistiques des Packs

Récupère des statistiques sur les packs.

```
GET /packs/statistics
```

#### Réponse

```json
{
  "data": {
    "total": 15,
    "active": 12,
    "average_price": 1499.99,
    "total_sales": 25499.99
  }
}
```

## Codes d'erreur

| Code | Description                                  |
|------|----------------------------------------------|
| 401  | Non authentifié                              |
| 403  | Non autorisé                                 |
| 404  | Pack non trouvé                              |
| 422  | Erreur de validation                         |
| 500  | Erreur serveur                               |

## Exemples d'erreurs

### Erreur de validation

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "prix": [
      "Le champ prix est obligatoire."
    ]
  }
}
```

### Erreur de suppression

```json
{
  "message": "Impossible de supprimer ce pack car il est associé à des ventes ou inscriptions"
}
```
