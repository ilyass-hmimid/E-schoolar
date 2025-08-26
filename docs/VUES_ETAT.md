# Documentation des Vues - État Actuel

## Vues Actuellement Utilisées

### Module Absences
- `Absences/Index.vue` - Liste des absences (AbsenceController@index)
- `Absences/Create.vue` - Formulaire de création (AbsenceController@create)
- `Absences/Show.vue` - Détail d'une absence (AbsenceController@show)
- `Absences/Edit.vue` - Édition d'une absence (AbsenceController@edit)
- `Absences/StatistiquesEtudiant.vue` - Statistiques par étudiant (AbsenceController@statistiquesEtudiant)

### Module Administration
#### Matières
- `Admin/Matieres/Index.vue` - Liste des matières
- `Admin/Matieres/Create.vue` - Création d'une matière
- `Admin/Matieres/Show.vue` - Détail d'une matière
- `Admin/Matieres/Edit.vue` - Édition d'une matière

#### Professeurs
- `Admin/Professeurs/Index.vue` - Liste des professeurs
- `Admin/Professeurs/Create.vue` - Création d'un professeur
- `Admin/Professeurs/Show.vue` - Détail d'un professeur
- `Admin/Professeurs/Edit.vue` - Édition d'un professeur

#### Niveaux
- `Admin/Niveaux/Index.vue` - Liste des niveaux
- `Admin/Niveaux/Create.vue` - Création d'un niveau
- `Admin/Niveaux/Show.vue` - Détail d'un niveau
- `Admin/Niveaux/Edit.vue` - Édition d'un niveau

#### Packs
- `Admin/Packs/Index.vue` - Liste des packs
- `Admin/Packs/Create.vue` - Création d'un pack
- `Admin/Packs/Show.vue` - Détail d'un pack
- `Admin/Packs/Edit.vue` - Édition d'un pack

## Vues Non Utilisées (En Attente d'Implémentation)

### Module Salaires
- `Admin/Salaires/Calculer.vue` - Calcul des salaires (prévu)
- `Admin/Salaires/Configuration.vue` - Configuration des paramètres de paie (prévu)
- `Admin/Salaires/Index.vue` - Liste des fiches de paie (prévu)
- `Admin/Salaires/Show.vue` - Détail d'une fiche de paie (prévu)
- `Admin/Salaires/Edit.vue` - Édition d'une fiche de paie (prévu)

### Module Utilisateurs
- `Admin/Users/*` - Toutes les vues sont référencées

## Notes Importantes
- Les vues marquées comme "prévu" sont conservées pour une implémentation future
- Aucune suppression n'a été effectuée pour maintenir l'intégrité des fonctionnalités futures
- Les chemins des contrôleurs sont indiqués entre parenthèses lorsqu'ils sont connus
