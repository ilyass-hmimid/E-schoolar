<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementProfesseur extends Model
{
    use SoftDeletes;

    protected $table = 'paiements_professeurs';

    protected $fillable = [
        'professeur_id',
        'mois',
        'montant',
        'statut',
        'mode_paiement',
        'reference_paiement',
        'date_paiement',
        'notes',
        'enregistre_par',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * Get the professeur that owns the paiement.
     */
    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    /**
     * Get the user who recorded the payment.
     */
    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /**
     * Format the mois attribute for display.
     */
    public function getMoisFormateAttribute(): string
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->mois)->format('F Y');
    }
}
