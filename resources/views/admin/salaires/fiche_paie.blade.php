<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de paie - {{ $salaire->professeur->nom_complet }} - {{ \Carbon\Carbon::parse($salaire->periode)->format('m/Y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .header-bg {
            background-color: #1e40af;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f3f4f6;
            padding: 10px 0;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            white-space: nowrap;
            z-index: -1;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .footer {
                position: fixed;
                bottom: 0;
            }
            .print-margin {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100 print:bg-white">
    <div class="watermark">{{ config('app.name') }}</div>
    
    <!-- En-tête -->
    <div class="max-w-4xl mx-auto bg-white shadow-sm print:shadow-none">
        <div class="p-8 print:p-6">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Fiche de paie</h1>
                    <p class="text-gray-600">Période : {{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}</p>
                    <p class="text-gray-600">Référence : {{ $salaire->reference ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-700">{{ config('app.name') }}</div>
                    <div class="text-sm text-gray-600">Établissement Scolaire</div>
                    <div class="text-xs text-gray-500 mt-1">123 Rue de l'Éducation</div>
                    <div class="text-xs text-gray-500">10000 Ville, Pays</div>
                    <div class="text-xs text-gray-500">Tél: +212 5XX-XXXXXX</div>
                </div>
            </div>
            
            <!-- Informations du salarié -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations du salarié</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16 rounded-full bg-gray-200 overflow-hidden">
                                <img class="h-full w-full object-cover" 
                                     src="{{ $salaire->professeur->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($salaire->professeur->nom_complet).'&color=7F9CF5&background=EBF4FF' }}" 
                                     alt="{{ $salaire->professeur->nom_complet }}">
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $salaire->professeur->nom_complet }}</h3>
                                <p class="text-sm text-gray-500">Professeur</p>
                                <p class="text-xs text-gray-500">Matricule: {{ $salaire->professeur->matricule ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-1 text-sm">
                            <p><span class="font-medium">Date d'embauche:</span> {{ $salaire->professeur->date_embauche ? \Carbon\Carbon::parse($salaire->professeur->date_embauche)->format('d/m/Y') : 'N/A' }}</p>
                            <p><span class="font-medium">CNSS:</span> {{ $salaire->professeur->num_cnss ?? 'N/A' }}</p>
                            <p><span class="font-medium">CIN:</span> {{ $salaire->professeur->cin ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="border-l border-gray-200 pl-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Coordonnées</h4>
                        <div class="space-y-1 text-sm">
                            <p class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                {{ $salaire->professeur->email ?? 'N/A' }}
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                {{ $salaire->professeur->telephone ?? 'N/A' }}
                            </p>
                            <p class="flex items-start">
                                <i class="fas fa-map-marker-alt mr-2 mt-1 text-gray-400"></i>
                                <span>{{ $salaire->professeur->adresse ?? 'N/A' }}</span>
                            </p>
                        </div>
                        
                        <h4 class="text-sm font-medium text-gray-900 mt-4 mb-2">Informations bancaires</h4>
                        <div class="space-y-1 text-sm">
                            <p><span class="font-medium">Banque:</span> {{ $salaire->professeur->banque_nom ?? 'N/A' }}</p>
                            <p><span class="font-medium">RIB:</span> {{ $salaire->professeur->rib ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Détails de la paie -->
            <div class="mt-10">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Détails de la paie</h2>
                
                <!-- Rémunérations -->
                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Rémunérations</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Taux/Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant (DH)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Salaire de base</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ $salaire->nb_heures }}h × {{ number_format($salaire->taux_horaire, 2, ',', ' ') }} DH/h
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($salaire->salaire_brut, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @if($salaire->prime_anciennete > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Prime d'ancienneté</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        + {{ number_format($salaire->prime_anciennete, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                @if($salaire->prime_rendement > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Prime de rendement</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        + {{ number_format($salaire->prime_rendement, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                @if($salaire->indemnite_transport > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Indemnité de transport</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        + {{ number_format($salaire->indemnite_transport, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                @if($salaire->autres_primes > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Autres primes</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        + {{ number_format($salaire->autres_primes, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Total brut</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right"></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 text-right border-t border-gray-200">
                                        {{ number_format($salaire->salaire_brut + $salaire->prime_anciennete + $salaire->prime_rendement + $salaire->indemnite_transport + $salaire->autres_primes, 2, ',', ' ') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Retenues -->
                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Retenues</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Base</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Taux</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant (DH)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if($salaire->cnss > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">CNSS</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($salaire->salaire_brut, 2, ',', ' ') }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">4,29%</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-red-600 text-right">
                                        - {{ number_format($salaire->cnss, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                @if($salaire->ir > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">IR (Impôt sur le revenu)</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-red-600 text-right">
                                        - {{ number_format($salaire->ir, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                @if($salaire->retenues_diverses > 0)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">Retenues diverses</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">-</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-red-600 text-right">
                                        - {{ number_format($salaire->retenues_diverses, 2, ',', ' ') }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Total des retenues</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right"></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right"></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-red-600 text-right border-t border-gray-200">
                                        - {{ number_format($salaire->cnss + $salaire->ir + $salaire->retenues_diverses, 2, ',', ' ') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Récapitulatif -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-blue-800">Salaire net à payer</h3>
                            <p class="text-sm text-blue-600">Arrondi à la dizaine inférieure</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-900">
                                {{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH
                            </div>
                            <div class="text-sm text-blue-700 mt-1">
                                {{ $salaire->salaire_net_lettres }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations de paiement -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Mode de paiement</h4>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($salaire->type_paiement)
                                    @php
                                        $types = [
                                            'virement' => 'Virement bancaire',
                                            'cheque' => 'Chèque',
                                            'especes' => 'Espèces'
                                        ];
                                    @endphp
                                    {{ $types[$salaire->type_paiement] ?? $salaire->type_paiement }}
                                @else
                                    Non spécifié
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Date de paiement</h4>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $salaire->date_paiement ? \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') : 'Non payé' }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Référence</h4>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $salaire->reference ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                @if($salaire->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
                    <div class="bg-yellow-50 p-3 rounded-md border border-yellow-100">
                        <p class="text-sm text-gray-700">{{ $salaire->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Pied de page -->
    <div class="footer no-print">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    Document généré le {{ now()->format('d/m/Y à H:i') }} par {{ Auth::user()->name }}
                </div>
                <div class="flex space-x-4">
                    <button onclick="window.print()" class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-print mr-1"></i> Imprimer
                    </button>
                    <a href="{{ route('admin.salaires.show', $salaire) }}" class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Script pour gérer l'impression
        document.addEventListener('DOMContentLoaded', function() {
            // Si l'impression est demandée via l'URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                window.print();
            }
            
            // Raccourci clavier Ctrl+P pour l'impression
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });
    </script>
</body>
</html>
