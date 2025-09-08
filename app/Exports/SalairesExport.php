<?php

namespace App\Exports;

use App\Models\Salaire;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalairesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $salaires;
    protected $periode;
    protected $filters;

    public function __construct($salaires, $periode = null, $filters = [])
    {
        $this->salaires = $salaires;
        $this->periode = $periode ?? 'Toutes périodes';
        $this->filters = $filters;
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
            'Référence',
            'Période',
            'Professeur',
            'Heures travaillées',
            'Taux horaire',
            'Salaire brut',
            'Prime ancienneté',
            'Prime rendement',
            'Indemnité transport',
            'Autres primes',
            'Total gains',
            'CNSS',
            'IR',
            'Autres retenues',
            'Total retenues',
            'Salaire net',
            'Statut',
            'Type de paiement',
            'Date de paiement',
            'Référence paiement',
            'Notes'
        ];
    }

    public function map($salaire): array
    {
        $totalGains = $salaire->salaire_brut + $salaire->prime_anciennete + $salaire->prime_rendement + 
                     $salaire->indemnite_transport + $salaire->autres_primes;
        
        $totalRetenues = $salaire->cnss + $salaire->ir + $salaire->retenues_diverses;

        return [
            $salaire->id,
            $salaire->reference,
            \Carbon\Carbon::parse($salaire->periode)->format('m/Y'),
            $salaire->professeur ? $salaire->professeur->nom . ' ' . $salaire->professeur->prenom : 'N/A',
            $salaire->nb_heures,
            $salaire->taux_horaire,
            $salaire->salaire_brut,
            $salaire->prime_anciennete,
            $salaire->prime_rendement,
            $salaire->indemnite_transport,
            $salaire->autres_primes,
            $totalGains,
            $salaire->cnss,
            $salaire->ir,
            $salaire->retenues_diverses,
            $totalRetenues,
            $salaire->salaire_net,
            $this->getStatutLabel($salaire->statut),
            $salaire->type_paiement ? ucfirst($salaire->type_paiement) : 'N/A',
            $salaire->date_paiement ? \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') : 'N/A',
            $salaire->reference_paiement ?? 'N/A',
            $salaire->notes ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->salaires->count() + 1;
        
        // Style de l'en-tête
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498DB'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Style des cellules de données
        $sheet->getStyle('A2:V' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style pour les montants
        $sheet->getStyle('E2:V' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Style pour les entêtes de colonnes numériques
        $sheet->getStyle('E1:V1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ajout d'une ligne de totaux
        $totalRow = $lastRow + 1;
        $sheet->setCellValue('D' . $totalRow, 'TOTAL');
        
        // Formules pour les totaux
        $columns = [
            'F' => 'taux_horaire',
            'G' => 'salaire_brut',
            'H' => 'prime_anciennete',
            'I' => 'prime_rendement',
            'J' => 'indemnite_transport',
            'K' => 'autres_primes',
            'L' => 'total_gains',
            'M' => 'cnss',
            'N' => 'ir',
            'O' => 'retenues_diverses',
            'P' => 'total_retenues',
            'Q' => 'salaire_net'
        ];
        
        foreach ($columns as $col => $field) {
            if ($field === 'total_gains') {
                $sheet->setCellValue($col . $totalRow, '=SUM(' . $col . '2:' . $col . $lastRow . ')');
            } elseif ($field === 'total_retenues') {
                $sheet->setCellValue($col . $totalRow, '=SUM(' . $col . '2:' . $col . $lastRow . ')');
            } else {
                $sheet->setCellValue($col . $totalRow, '=SUM(' . $col . '2:' . $col . $lastRow . ')');
            }
        }
        
        // Style de la ligne de totaux
        $sheet->getStyle('D' . $totalRow . ':V' . $totalRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
            ],
        ]);

        // Ajout des métadonnées
        $sheet->getHeaderFooter()
            ->setOddHeader('&C&B&16' . config('app.name') . ' - Export des salaires');
            
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            
        $sheet->setAutoFilter('A1:V1');
    }
    
    private function getStatutLabel($statut)
    {
        $statuts = [
            'en_attente' => 'En attente',
            'paye' => 'Payé',
            'retard' => 'En retard',
            'annule' => 'Annulé'
        ];
        
        return $statuts[$statut] ?? $statut;
    }
}
