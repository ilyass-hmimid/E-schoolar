<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RoleType;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'eleve_id',
        'type',
        'message',
        'status',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Types de notifications
    const TYPE_PAIEMENT_RETARD = 'paiement_retard';
    const TYPE_PAIEMENT_EFFECTUE = 'paiement_effectue';

    // Statuts des notifications
    const STATUS_NON_LU = 'non_lu';
    const STATUS_LU = 'lu';

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eleve()
    {
        return $this->belongsTo(User::class, 'eleve_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', self::STATUS_NON_LU);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Méthodes utilitaires
    public function markAsRead()
    {
        $this->update([
            'status' => self::STATUS_LU,
            'read_at' => now()
        ]);
    }

    public function isRead()
    {
        return $this->status === self::STATUS_LU;
    }

    // Création de notifications
    public static function createPaiementRetardNotification($eleve, $joursRetard, $montantDu, $matieres)
    {
        $assistants = User::where('role', RoleType::ASSISTANT)->get();
        
        $message = "L'élève {$eleve->name} est en retard de {$joursRetard} jours pour un montant de {$montantDu} DH. " .
                  "Matières concernées : " . implode(', ', $matieres);
        
        $data = [
            'eleve_id' => $eleve->id,
            'eleve_nom' => $eleve->name,
            'niveau' => $eleve->niveau->nom ?? 'Non défini',
            'montant_du' => $montantDu,
            'jours_retard' => $joursRetard,
            'matieres' => $matieres,
            'lien' => route('eleves.show', $eleve->id)
        ];
        
        foreach ($assistants as $assistant) {
            self::create([
                'user_id' => $assistant->id,
                'eleve_id' => $eleve->id,
                'type' => self::TYPE_PAIEMENT_RETARD,
                'message' => $message,
                'status' => self::STATUS_NON_LU,
                'data' => $data
            ]);
        }
    }

    public static function createPaiementEffectueNotification($paiement)
    {
        $admin = User::where('role', RoleType::ADMIN)->first();
        
        if (!$admin) return;
        
        $eleve = $paiement->etudiant;
        $message = "Paiement de {$paiement->montant} DH effectué par {$eleve->name} " .
                  "({$paiement->mode_paiement}) le " . $paiement->date_paiement->format('d/m/Y');
        
        $data = [
            'eleve_id' => $eleve->id,
            'eleve_nom' => $eleve->name,
            'montant' => $paiement->montant,
            'mode_paiement' => $paiement->mode_paiement,
            'date_paiement' => $paiement->date_paiement->format('d/m/Y'),
            'reference' => $paiement->reference_paiement,
            'lien' => route('paiements.show', $paiement->id)
        ];
        
        self::create([
            'user_id' => $admin->id,
            'eleve_id' => $eleve->id,
            'type' => self::TYPE_PAIEMENT_EFFECTUE,
            'message' => $message,
            'status' => self::STATUS_NON_LU,
            'data' => $data
        ]);
    }
