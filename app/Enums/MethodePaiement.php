<?php

namespace App\Enums;

enum MethodePaiement: string
{
    case ESPECES = 'especes';
    case CHEQUE = 'cheque';
    case VIREMENT = 'virement';
    case CARTE_BANCAIRE = 'carte_bancaire';
    case PRET = 'pret';
    case AUTRE = 'autre';
    
    public function label(): string
    {
        return match($this) {
            self::ESPECES => 'Espèces',
            self::CHEQUE => 'Chèque',
            self::VIREMENT => 'Virement bancaire',
            self::CARTE_BANCAIRE => 'Carte bancaire',
            self::PRET => 'Prêt',
            self::AUTRE => 'Autre',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::ESPECES => 'fa-money-bill-wave',
            self::CHEQUE => 'fa-file-invoice-dollar',
            self::VIREMENT => 'fa-exchange-alt',
            self::CARTE_BANCAIRE => 'fa-credit-card',
            self::PRET => 'fa-hand-holding-usd',
            self::AUTRE => 'fa-ellipsis-h',
        };
    }
}
