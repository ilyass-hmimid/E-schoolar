<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaiementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $paiements;

    public function __construct($paiements)
    {
        $this->paiements = $paiements;
    }

    public function collection()
    {
        return $this->paiements;
    }

    public function headings(): array
    {
        return [
            'Étudiant',
            'Matière',
            'Pack',
            'Montant (DH)',
            'Date de paiement',
            'Statut',
            'Mode de paiement',
            'Référence',
            'Commentaires'
        ];
    }

    public function map($paiement): array
    {
        return [
            $paiement['Étudiant'],
            $paiement['Matière'],
            $paiement['Pack'],
            $paiement['Montant'],
            $paiement['Date'],
            $paiement['Statut'],
            $paiement['Mode de paiement'],
            $paiement['Référence'],
            $paiement['Commentaires'] ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f2f2f2']
                ]
            ],
            // Set border for all cells
            'A1:I' . ($this->paiements->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
