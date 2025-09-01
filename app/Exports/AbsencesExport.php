<?php

namespace App\Exports;

use App\Models\Absence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsencesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $absences;

    public function __construct($absences)
    {
        $this->absences = $absences;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->absences;
    }

    /**
     * Map the data for the export.
     *
     * @param mixed $absence
     * @return array
     */
    public function map($absence): array
    {
        return [
            $absence->eleve->nom_complet,
            $absence->eleve->classe->nom_complet ?? 'N/A',
            $absence->cours->matiere->nom ?? 'N/A',
            $absence->cours->professeur->nom_complet ?? 'N/A',
            $absence->date_absence->format('d/m/Y'),
            $absence->type_absence,
            $absence->duree_absence ? $absence->duree_absence . ' min' : 'N/A',
            $absence->justifiee ? 'Oui' : 'Non',
            $absence->motif ?? 'Non précisé',
            $absence->created_at->format('d/m/Y H:i'),
            $absence->utilisateurCreation->name ?? 'Système',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Élève',
            'Classe',
            'Matière',
            'Professeur',
            'Date d\'absence',
            'Type',
            'Durée',
            'Justifiée',
            'Motif',
            'Date de saisie',
            'Saisi par',
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Élève
            'B' => 20, // Classe
            'C' => 25, // Matière
            'D' => 25, // Professeur
            'E' => 15, // Date absence
            'F' => 15, // Type
            'G' => 12, // Durée
            'H' => 12, // Justifiée
            'I' => 30, // Motif
            'J' => 18, // Date saisie
            'K' => 20, // Saisi par
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style pour l'en-tête
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style pour les lignes paires
        $sheet->getStyle('A2:K' . ($this->absences->count() + 1))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);

        // Alternance des couleurs de ligne
        foreach (range(2, $this->absences->count() + 1) as $row) {
            $fillColor = $row % 2 == 0 ? 'FFFFFF' : 'F2F2F2';
            $sheet->getStyle('A' . $row . ':K' . $row)
                ->getFill()
                ->setFillType('solid')
                ->getStartColor()
                ->setARGB($fillColor);
        }

        // Ajuster la hauteur des lignes
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Verrouiller la première ligne lors du défilement
        $sheet->freezePane('A2');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Liste des absences';
    }
}
