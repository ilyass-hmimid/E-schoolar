<?php

namespace App\Exports;

use App\Models\Centre;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CentresExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Centre::with('responsable')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Adresse',
            'Ville',
            'Pays',
            'Téléphone',
            'Email',
            'Responsable',
            'Statut',
            'Date de création',
            'Dernière mise à jour'
        ];
    }

    /**
     * @param mixed $centre
     *
     * @return array
     */
    public function map($centre): array
    {
        return [
            $centre->id,
            $centre->nom,
            $centre->adresse,
            $centre->ville,
            $centre->pays,
            $centre->telephone,
            $centre->email,
            $centre->responsable ? $centre->responsable->name : 'Non défini',
            $centre->is_active ? 'Actif' : 'Inactif',
            $centre->created_at->format('d/m/Y H:i'),
            $centre->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '3490dc'],
                    ],
                ]);
                
                // Définir la largeur automatique pour toutes les colonnes
                $event->sheet->autoSize();
            },
        ];
    }
}
