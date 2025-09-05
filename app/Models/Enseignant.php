<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'ville',
        'pays',
        'cin',
        'carte_sejour',
        'numero_securite_sociale',
        'situation_familiale',
        'nombre_enfants',
        'niveau_etude',
        'diplome',
        'specialite_diplome',
        'etablissement_diplome',
        'annee_obtention_diplome',
        'salaire_base',
        'type_contrat',
        'date_embauche',
        'date_fin_contrat',
        'banque',
        'numero_compte_bancaire',
        'nom_urgence',
        'telephone_urgence',
        'lien_parente_urgence',
        'notes',
        'est_actif'
    ];
    
    protected $casts = [
        'date_naissance' => 'date',
        'annee_obtention_diplome' => 'string',
        'date_embauche' => 'date',
        'date_fin_contrat' => 'date',
        'salaire_base' => 'decimal:2',
        'nombre_enfants' => 'integer',
        'est_actif' => 'boolean',
    ];
    
    protected $dates = [
        'date_naissance',
        'date_embauche',
        'date_fin_contrat',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
