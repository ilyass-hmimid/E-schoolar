<?php

namespace App\Exports;

use App\Models\Paiement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

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
            'ID',
            'Étudiant',
            'Email étudiant',
            'Type',
            'Matière',
            'Pack',
            'Tarif appliqué',
            'Montant (DH)',
            'Date de paiement',
            'Mois de période',
            'Statut',
            'Mode de paiement',
            'Référence',
            'Assistant',
            'Date de création',
            'Dernière mise à jour',
            'Commentaires'
        ];
    }

    public function map($paiement): array
    {
        $type = $paiement->pack_id ? 'Pack' : ($paiement->matiere_id ? 'Cours' : 'Divers');
        $statut = ucfirst(str_replace('_', ' ', $paiement->statut));
        $modePaiement = ucfirst($paiement->mode_paiement);
        
        return [
            $paiement->id,
            $paiement->etudiant->name ?? 'N/A',
            $paiement->etudiant->email ?? 'N/A',
            $type,
            $paiement->matiere->nom ?? 'N/A',
            $paiement->pack->nom ?? 'N/A',
            $paiement->tarif->nom ?? 'N/A',
            number_format($paiement->montant, 2, ',', ' '),
            Carbon::parse($paiement->date_paiement)->format('d/m/Y'),
            $paiement->mois_periode ? Carbon::createFromFormat('Y-m', $paiement->mois_periode)->format('m/Y') : 'N/A',
            $statut,
            $modePaiement,
            $paiement->reference_paiement ?? 'N/A',
            $paiement->assistant->name ?? 'Système',
            $paiement->created_at->format('d/m/Y H:i'),
            $paiement->updated_at->format('d/m/Y H:i'),
            $paiement->commentaires ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style de l'en-tête
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498db'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Ajuster la hauteur de la première ligne
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Centrer le texte dans les cellules
        $sheet->getStyle('A1:Q' . ($this->paiements->count() + 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
        // Aligner à gauche les colonnes de texte long
        $sheet->getStyle('Q1:Q' . ($this->paiements->count() + 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            
        // Formater les colonnes numériques
        $sheet->getStyle('H2:H' . ($this->paiements->count() + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00\ "DH"');
            
        // Ajuster la largeur des colonnes automatiquement
        foreach(range('A','Q') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Ajouter un filtre sur l'en-tête
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
        
        // Geler la première ligne
        $sheet->freezePane('A2');
        
        return [];
}
