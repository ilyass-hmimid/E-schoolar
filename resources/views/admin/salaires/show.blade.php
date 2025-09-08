@extends('layouts.admin')

@section('title', 'Détails du salaire')

@push('styles')
<style>
    .detail-label {
        @apply text-sm font-medium text-gray-500;
    }
    .detail-value {
        @apply mt-1 text-sm text-gray-900;
    }
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .badge-success {
        @apply bg-green-100 text-green-800;
    }
    .badge-warning {
        @apply bg-yellow-100 text-yellow-800;
    }
    .badge-danger {
        @apply bg-red-100 text-red-800;
    }
    .badge-info {
        @apply bg-blue-100 text-blue-800;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Détails du salaire</h1>
                <p class="text-sm text-gray-500">
                    {{ $salaire->professeur->nom_complet }} - {{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.salaires.edit', $salaire) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
                @if($salaire->statut != 'paye')
                <form action="{{ route('admin.salaires.paiement', $salaire) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Enregistrer le paiement
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.salaires.fiche', $salaire) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Fiche de paie
                </a>
                <a href="{{ route('admin.salaires.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </a>
            </div>
        </div>
        
        <nav class="flex mt-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.salaires.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Salaires
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Détails
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informations générales
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Détails du salaire pour la période sélectionnée.
                    </p>
                </div>
                <div>
                    @php
                        $statusClasses = [
                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                            'paye' => 'bg-green-100 text-green-800',
                            'retard' => 'bg-red-100 text-red-800',
                        ][$salaire->statut];
                        
                        $statusLabels = [
                            'en_attente' => 'En attente',
                            'paye' => 'Payé',
                            'retard' => 'En retard',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                        {{ $statusLabels[$salaire->statut] }}
                    </span>
                    @if($salaire->est_avance)
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Avance sur salaire
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Professeur
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $salaire->professeur->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($salaire->professeur->nom_complet).'&color=7F9CF5&background=EBF4FF' }}" 
                                     alt="{{ $salaire->professeur->nom_complet }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $salaire->professeur->nom_complet }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $salaire->professeur->email }} | {{ $salaire->professeur->telephone }}
                                </div>
                            </div>
                        </div>
                    </dd>
                </div>
                
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Période
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ \Carbon\Carbon::parse($salaire->periode)->format('F Y') }}
                    </dd>
                </div>
                
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Nombre d'heures
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $salaire->nb_heures }} heures
                    </dd>
                </div>
                
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Taux horaire
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ number_format($salaire->taux_horaire, 2, ',', ' ') }} DH / heure
                    </dd>
                </div>
                
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Salaire brut
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH
                    </dd>
                </div>
                
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Primes
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        + {{ number_format($salaire->primes, 2, ',', ' ') }} DH
                    </dd>
                </div>
                
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Retenues
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        - {{ number_format($salaire->retenues, 2, ',', ' ') }} DH
                    </dd>
                </div>
                
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                    <dt class="text-lg font-bold text-gray-900">
                        Salaire net
                    </dt>
                    <dd class="mt-1 text-2xl font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH
                    </dd>
                </div>
                
                @if($salaire->statut == 'paye')
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500">
                        Date de paiement
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') }}
                    </dd>
                </div>
                
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Mode de paiement
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @php
                            $paiementTypes = [
                                'virement' => 'Virement bancaire',
                                'cheque' => 'Chèque',
                                'especes' => 'Espèces',
                            ];
                        @endphp
                        {{ $paiementTypes[$salaire->type_paiement] ?? $salaire->type_paiement }}
                        @if($salaire->reference)
                            <span class="text-gray-500 ml-2">({{ $salaire->reference }})</span>
                        @endif
                    </dd>
                </div>
                @endif
                
                @if($salaire->notes)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500">
                        Notes
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                        {{ $salaire->notes }}
                    </dd>
                </div>
                @endif
                
                <div class="bg-white px-4 py-4 sm:px-6 border-t border-gray-200">
                    <div class="flex justify-between text-sm text-gray-500">
                        <div>
                            Créé le {{ $salaire->created_at->format('d/m/Y à H:i') }}
                        </div>
                        <div>
                            Dernière mise à jour : {{ $salaire->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
            </dl>
        </div>
    </div>
    
    <!-- Historique des modifications -->
    @if($salaire->activities->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Historique des modifications
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Journal des modifications apportées à ce salaire.
            </p>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-b-lg">
            <ul class="divide-y divide-gray-200">
                @foreach($salaire->activities as $activity)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center">
                        <div class="min-w-0 flex-1 flex items-center">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $activity->causer->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($activity->causer->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                     alt="{{ $activity->causer->name }}">
                            </div>
                            <div class="min-w-0 flex-1 px-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $activity->causer->name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $activity->description }}
                                    </p>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-5 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $activity->event == 'created' ? 'bg-green-100 text-green-800' : 
                                ($activity->event == 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                            }}">
                                {{ ucfirst($activity->event) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($activity->properties->has('old') || $activity->properties->has('attributes'))
                    <div class="mt-2 ml-14 text-xs bg-gray-50 p-2 rounded">
                        @foreach($activity->properties->get('attributes', []) as $key => $value)
                            @if(!in_array($key, ['updated_at']))
                            <div class="mb-1">
                                @php
                                    $oldValue = $activity->properties->get('old')[$key] ?? null;
                                    $fieldNames = [
                                        'statut' => 'Statut',
                                        'date_paiement' => 'Date de paiement',
                                        'type_paiement' => 'Mode de paiement',
                                        'reference' => 'Référence',
                                        'notes' => 'Notes',
                                        'nb_heures' => 'Nombre d\'heures',
                                        'taux_horaire' => 'Taux horaire',
                                        'salaire_brut' => 'Salaire brut',
                                        'primes' => 'Primes',
                                        'retenues' => 'Retenues',
                                        'salaire_net' => 'Salaire net',
                                        'est_avance' => 'Avance sur salaire',
                                    ];
                                    
                                    $fieldName = $fieldNames[$key] ?? str_replace('_', ' ', ucfirst($key));
                                @endphp
                                
                                <div class="font-medium">{{ $fieldName }}:</div>
                                <div class="ml-2">
                                    @if($oldValue !== null)
                                        <span class="line-through text-red-500">
                                            @if(in_array($key, ['date_paiement']))
                                                {{ \Carbon\Carbon::parse($oldValue)->format('d/m/Y') }}
                                            @elseif(in_array($key, ['taux_horaire', 'salaire_brut', 'primes', 'retenues', 'salaire_net']))
                                                {{ number_format($oldValue, 2, ',', ' ') }} DH
                                            @elseif($key === 'est_avance')
                                                {{ $oldValue ? 'Oui' : 'Non' }}
                                            @else
                                                {{ $oldValue }}
                                            @endif
                                        </span>
                                        <span class="mx-1">→</span>
                                    @endif
                                    
                                    @if(in_array($key, ['date_paiement']))
                                        {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                                    @elseif(in_array($key, ['taux_horaire', 'salaire_brut', 'primes', 'retenues', 'salaire_net']))
                                        {{ number_format($value, 2, ',', ' ') }} DH
                                    @elseif($key === 'est_avance')
                                        {{ $value ? 'Oui' : 'Non' }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    
    <!-- Actions -->
    <div class="flex justify-between items-center mt-6">
        <div>
            @if($salaire->statut == 'paye')
                <form action="{{ route('admin.salaires.annuler-paiement', $salaire) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce paiement ?')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <i class="fas fa-undo mr-2"></i>
                        Annuler le paiement
                    </button>
                </form>
            @endif
        </div>
        
        <div class="space-x-2">
            <a href="{{ route('admin.salaires.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            
            <a href="{{ route('admin.salaires.edit', $salaire) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            
            @if($salaire->statut != 'paye')
            <form action="{{ route('admin.salaires.paiement', $salaire) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Enregistrer le paiement
                </button>
            </form>
            @endif
            
            <a href="{{ route('admin.salaires.fiche', $salaire) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-file-invoice mr-2"></i>
                Fiche de paie
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scripts spécifiques à la page de détail si nécessaire
</script>
@endpush

@endsection
