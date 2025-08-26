<?php

namespace App\Services;

use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PaiementExportService
{
    public function exportToPdf($paiements)
    {
        $data = [
            'title' => 'Rapport des Paiements',
            'date' => now()->format('d/m/Y'),
            'paiements' => $paiements,
        ];

        $pdf = Pdf::loadView('exports.paiements.pdf', $data);
        return $pdf->download('paiements_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportToExcel($paiements)
    {
        $data = $paiements->map(function($paiement) {
            return [
                'Étudiant' => $paiement->etudiant->name,
                'Matière' => $paiement->matiere?->nom ?? 'N/A',
                'Pack' => $paiement->pack?->nom ?? 'N/A',
                'Montant' => number_format($paiement->montant, 2) . ' DH',
                'Date' => $paiement->date_paiement->format('d/m/Y'),
                'Statut' => ucfirst($paiement->statut),
                'Mode de paiement' => ucfirst(str_replace('_', ' ', $paiement->mode_paiement)),
                'Référence' => $paiement->reference_paiement ?? 'N/A',
            ];
        });

        return Excel::download(new \App\Exports\PaiementsExport($data), 'paiements_' . now()->format('Y-m-d') . '.xlsx');
    }
}
