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
            $absence->etudiant->nom_complet,
            $absence->etudiant->classe->nom ?? 'N/A',
            $absence->matiere->nom ?? 'N/A',
            $absence->professeur->name ?? 'N/A',
            $absence->date_absence->format('d/m/Y'),
            $absence->heure_debut . ' - ' . $absence->heure_fin,
            ucfirst($absence->type),
            $absence->duree_retard ? $absence->duree_retard . ' min' : 'N/A',
            $absence->justifiee ? 'Oui' : 'Non',
            $absence->statut_justification ? ucfirst($absence->statut_justification) : 'N/A',
            $absence->motif ?? 'Non précisé',
            $absence->piece_jointe ? 'Oui' : 'Non',
            $absence->commentaire_validation ?? 'N/A',
            $absence->assistant->name ?? 'N/A',
            $absence->validateur->name ?? 'N/A',
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
            'Étudiant',
            'Classe',
            'Matière',
            'Professeur',
            'Date',
            'Période',
            'Type',
            'Durée retard',
            'Justifiée',
            'Statut justification',
            'Motif',
            'Pièce jointe',
            'Commentaire validation',
            'Saisie par',
            'Validé par',
            'Date d\'enregistrement',
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Étudiant
            'B' => 15, // Classe
            'C' => 25, // Matière
            'D' => 25, // Professeur
            'E' => 12, // Date
            'F' => 20, // Période
            'G' => 15, // Type
            'H' => 15, // Durée retard
            'I' => 12, // Justifiée
            'J' => 20, // Statut justification
            'K' => 30, // Motif
            'L' => 15, // Pièce jointe
            'M' => 30, // Commentaire validation
            'N' => 25, // Saisie par
            'O' => 25, // Validé par
            'P' => 20, // Date d'enregistrement
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            // Style de l'en-tête
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '4F81BD'],
                ],
            ],
            // Style des lignes paires
            'A2:P' . ($this->absences->count() + 1) => [
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'DCE6F1'],
                ],
            ],
            // Style des bordures
            'A1:P' . ($this->absences->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Alignement du texte
            'A1:P' . ($this->absences->count() + 1) => [
                'alignment' => [
                    'vertical' => 'center',
                ],
            ],
            // Largeur automatique des colonnes
            'A:Z' => [
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];

        foreach ($styleArray as $cell => $style) {
            $sheet->getStyle($cell)->applyFromArray($style);
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
