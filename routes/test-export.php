<?php

use App\Exports\SalairesExport;
use App\Models\Salaire;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/test-export', function () {
    // Create a test salary record if none exists
    if (Salaire::count() === 0) {
        Salaire::create([
            'reference' => 'SAL-' . strtoupper(Str::random(8)),
            'professeur_id' => 1, // Make sure this professor exists
            'periode' => now(),
            'nb_heures' => 160,
            'taux_horaire' => 100,
            'salaire_brut' => 16000,
            'prime_anciennete' => 500,
            'prime_rendement' => 300,
            'indemnite_transport' => 200,
            'autres_primes' => 100,
            'cnss' => 500,
            'ir' => 1000,
            'retenues_diverses' => 200,
            'salaire_net' => 14800,
            'statut' => 'paye',
            'type_paiement' => 'virement',
            'date_paiement' => now(),
            'reference_paiement' => 'PAY-' . strtoupper(Str::random(8)),
            'notes' => 'Test export',
        ]);
    }

    // Get all salaries
    $salaires = Salaire::all();
    
    // Test Excel export
    return Excel::download(new SalairesExport($salaires, now()->format('Y-m'), []), 'test-export.xlsx');
});
