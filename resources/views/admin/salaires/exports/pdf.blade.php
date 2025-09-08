<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export des Salaires - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #2c3e50;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 0;
            color: #7f8c8d;
        }
        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .filters span {
            font-weight: bold;
            margin-right: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .totals p {
            margin: 5px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relevé des Salaires - {{ config('app.name') }}</h1>
        <p>Période: {{ $periode }}</p>
        @if(!empty($filters))
            <p>Filtres appliqués: {{ implode(', ', array_values($filters)) }}</p>
        @endif
        <p>Date d'export: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Filtres appliqués :</strong><br>
        @foreach($filters as $key => $value)
            <span>{{ ucfirst($key) }}:</span> {{ $value }}<br>
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Période</th>
                <th>Professeur</th>
                <th class="text-right">Salaire Brut</th>
                <th class="text-right">Primes</th>
                <th class="text-right">Retenues</th>
                <th class="text-right">Net à Payer</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaires as $salaire)
                @php
                    $totalPrimes = $salaire->prime_anciennete + $salaire->prime_rendement + $salaire->indemnite_transport + $salaire->autres_primes;
                    $totalRetenues = $salaire->cnss + $salaire->ir + $salaire->retenues_diverses;
                @endphp
                <tr>
                    <td>{{ $salaire->reference }}</td>
                    <td>{{ \Carbon\Carbon::parse($salaire->periode)->format('m/Y') }}</td>
                    <td>{{ $salaire->professeur ? $salaire->professeur->nom . ' ' . $salaire->professeur->prenom : 'N/A' }}</td>
                    <td class="text-right">{{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($totalPrimes, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($totalRetenues, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</td>
                    <td class="text-center">
                        @php
                            $statuts = [
                                'en_attente' => 'En attente',
                                'paye' => 'Payé',
                                'retard' => 'En retard',
                                'annule' => 'Annulé'
                            ];
                        @endphp
                        {{ $statuts[$salaire->statut] ?? $salaire->statut }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAUX :</th>
                <th class="text-right">{{ number_format($salaires->sum('salaire_brut'), 2, ',', ' ') }} DH</th>
                <th class="text-right">
                    @php
                        $totalPrimes = $salaires->sum('prime_anciennete') + $salaires->sum('prime_rendement') + 
                                     $salaires->sum('indemnite_transport') + $salaires->sum('autres_primes');
                    @endphp
                    {{ number_format($totalPrimes, 2, ',', ' ') }} DH
                </th>
                <th class="text-right">{{ number_format($totalRetenues, 2, ',', ' ') }} DH</th>
                <th class="text-right">{{ number_format($salaires->sum('salaire_net'), 2, ',', ' ') }} DH</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="totals">
        <p><strong>Récapitulatif :</strong></p>
        <p>Nombre de fiches de paie : {{ $salaires->count() }}</p>
        <p>Total des salaires bruts : {{ number_format($salaires->sum('salaire_brut'), 2, ',', ' ') }} DH</p>
        <p>Total des primes : {{ number_format($totalPrimes, 2, ',', ' ') }} DH</p>
        <p>Total des retenues : {{ number_format($totalRetenues, 2, ',', ' ') }} DH</p>
        <p><strong>Total des salaires nets :</strong> {{ number_format($salaires->sum('salaire_net'), 2, ',', ' ') }} DH</p>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name ?? 'Système' }}</p>
        <p>{{ config('app.name') }} - Tous droits réservés</p>
    </div>
</body>
</html>
