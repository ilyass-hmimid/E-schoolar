<?php

return [
    'role' => [
        'role' => 'required|string|in:admin,professeur,assistant,eleve',
    ],
    'permission' => [
        'permission' => 'required|string',
    ],
    'login' => [
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ],
    'user' => [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string|in:admin,professeur,assistant,eleve',
    ],
    'niveau' => [
        'nom' => 'required|string|max:255|unique:niveaux,nom',
        'description' => 'nullable|string',
        'ordre' => 'required|integer|min:1',
    ],
    'filiere' => [
        'nom' => 'required|string|max:255|unique:filieres,nom',
        'description' => 'nullable|string',
        'niveau_id' => 'required|exists:niveaux,id',
    ],
    'matiere' => [
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
        'filiere_id' => 'required|exists:filieres,id',
        'coefficient' => 'required|numeric|min:0.1|max:10',
    ],
    'parametre' => [
        'nom_etablissement' => 'required|string|max:255',
        'adresse' => 'required|string|max:255',
        'telephone' => 'required|string|max:20',
        'email_contact' => 'required|email|max:255',
        'annee_scolaire' => 'required|string|max:9',
    ],
];
