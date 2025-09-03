<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case PAYE = 'paye';
    case EN_RETARD = 'en_retard';
    case IMPAYE = 'impaye';
    case ANNULE = 'annule';
    case REMBOURSE = 'rembourse';
    
    public function label(): string
    {
        return match($this) {
            self::PAYE => 'Payé',
            self::EN_RETARD => 'En retard',
            self::IMPAYE => 'Impayé',
            self::ANNULE => 'Annulé',
            self::REMBOURSE => 'Remboursé',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PAYE => 'success',
            self::EN_RETARD => 'warning',
            self::IMPAYE => 'danger',
            self::ANNULE => 'secondary',
            self::REMBOURSE => 'info',
        };
    }
}
