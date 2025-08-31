<?php

namespace App\Exports;

use App\Models\Classe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClasseExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $classe;
    protected $eleves;

    public function __construct(Classe $classe)
    {
        $this->classe = $classe->load([
            'eleves' => function ($query) {
                $query->orderBy('nom')->orderBy('prenom');
            },
            'matiere',
            'niveau',
            'notes'
        ]);

        $this->eleves = $this->classe->eleves->map(function ($eleve) use ($classe) {
            $moyenne = $eleve->notes()
                ->where('matiere_id', $classe->matiere_id)
                ->where('classe_id', $classe->id)
                ->avg('valeur');

            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'email' => $eleve->email,
                'moyenne' => $moyenne ? (float) round($moyenne, 2) : null,
            ];
        });
    }

    public function collection()
    {
        return $this->eleves;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Moyenne',
        ];
    }

    public function map($eleve): array
    {
        return [
            $eleve['id'],
            $eleve['nom'],
            $eleve['prenom'],
            $eleve['email'],
            $eleve['moyenne'] ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Élèves de ' . $this->classe->nom;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->eleves->count() + 1;
        
        // Style de l'en-tête
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style des lignes alternées
        for ($i = 2; $i <= $lastRow; $i++) {
            $fillColor = $i % 2 == 0 ? 'FFFFFF' : 'F2F2F2';
            $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        // Ajustement automatique de la largeur des colonnes
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
