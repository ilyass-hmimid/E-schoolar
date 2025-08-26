<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalairesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $salaires;
    protected $periode;

    public function __construct($salaires, $periode)
    {
        $this->salaires = $salaires;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->salaires;
    }

    public function title(): string
    {
        return 'Salaires ' . $this->periode;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Période',
            'Professeur',
            'Matière',
            'Nombre d\'élèves',
            'Prix unitaire',
            'Commission (%)',
            'Montant Brut',
            'Montant Commission',
            'Montant Net',
            'Statut',
            'Date de paiement',
            'Commentaires',
        ];
    }

    public function map($salaire): array
    {
        return [
            $salaire->id,
            $salaire->mois_periode,
            $salaire->professeur->name,
            $salaire->matiere->nom,
            $salaire->nombre_eleves,
            number_format($salaire->prix_unitaire, 2, ',', ' '),
            number_format($salaire->commission_prof, 2, ',', ' '),
            number_format($salaire->montant_brut, 2, ',', ' '),
            number_format($salaire->montant_commission, 2, ',', ' '),
            number_format($salaire->montant_net, 2, ',', ' '),
            $this->getStatutLabel($salaire->statut),
            $salaire->date_paiement ? \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') : '',
            $salaire->commentaires,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style de l'en-tête
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Ajustement automatique de la largeur des colonnes
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format des nombres
        $sheet->getStyle('F2:H' . ($this->salaires->count() + 1))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I2:J' . ($this->salaires->count() + 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Ajout d'une ligne de totaux
        $lastRow = $this->salaires->count() + 2;
        $sheet->setCellValue('E' . $lastRow, 'TOTAL');
        $sheet->setCellValue('H' . $lastRow, '=SUM(H2:H' . ($lastRow - 1) . ')');
        $sheet->setCellValue('I' . $lastRow, '=SUM(I2:I' . ($lastRow - 1) . ')');
        $sheet->setCellValue('J' . $lastRow, '=SUM(J2:J' . ($lastRow - 1) . ')');
        
        // Style de la ligne de totaux
        $sheet->getStyle('E' . $lastRow . ':J' . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Format des nombres pour la ligne de totaux
        $sheet->getStyle('H' . $lastRow . ':J' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    protected function getStatutLabel($statut)
    {
        $statuts = [
            'en_attente' => 'En attente',
            'paye' => 'Payé',
            'annule' => 'Annulé',
        ];

        return $statuts[$statut] ?? $statut;
    }
}
