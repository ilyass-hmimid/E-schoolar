<?php

namespace App\Enums;

enum TypeNotification: string
{
    case ABSENCE = 'absence';
    case PAIEMENT = 'paiement';
    case COURS = 'cours';
    case MESSAGE = 'message';
    case AUTRE = 'autre';
    
    public function label(): string
    {
        return match($this) {
            self::ABSENCE => 'Absence',
            self::PAIEMENT => 'Paiement',
            self::COURS => 'Cours',
            self::MESSAGE => 'Message',
            self::AUTRE => 'Autre',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::ABSENCE => 'fa-user-times',
            self::PAIEMENT => 'fa-money-bill-wave',
            self::COURS => 'fa-chalkboard-teacher',
            self::MESSAGE => 'fa-envelope',
            self::AUTRE => 'fa-bell',
        };
    }
}
