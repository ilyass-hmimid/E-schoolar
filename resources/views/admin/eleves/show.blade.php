@extends('layouts.admin')

@section('title', 'Détails de l\'élève')

@section('content')
<div class="bg-dark-800 rounded-lg shadow-lg overflow-hidden">
    <!-- En-tête avec photo et actions -->
    <div class="bg-dark-700 px-6 py-4 border-b border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="h-20 w-20 rounded-full bg-dark-600 flex items-center justify-center text-3xl text-gray-400">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 bg-{{ $eleve->status === 'actif' ? 'green' : 'red' }}-500 rounded-full h-4 w-4 border-2 border-dark-700"></span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $eleve->nom }} {{ $eleve->prenom }}</h1>
                    <div class="flex items-center mt-1">
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $eleve->status === 'actif' ? 'green' : ($eleve->status === 'inactif' ? 'yellow' : 'red') }}-500 bg-opacity-20 text-{{ $eleve->status === 'actif' ? 'green' : ($eleve->status === 'inactif' ? 'yellow' : 'red') }}-300">
                            {{ ucfirst($eleve->status) }}
                        </span>
                        <span class="ml-2 text-sm text-gray-400">
                            {{ $eleve->classe->niveau->nom }} - {{ $eleve->classe->nom }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.eleves.edit', $eleve) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
                <a href="{{ route('admin.eleves.index') }}" class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-dark-600 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
        <!-- Colonne de gauche - Informations personnelles -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Carte d'information personnelle -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-user-circle mr-2"></i> Informations personnelles
                    </h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">CNE</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->cne }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">CNI</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->cni ?? 'Non renseigné' }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">Date de naissance</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->date_naissance->format('d/m/Y') }} ({{ $eleve->age }} ans)</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">Lieu de naissance</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->lieu_naissance }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">Sexe</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->sexe }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">Date d'inscription</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->date_inscription->format('d/m/Y') }} ({{ $eleve->date_inscription->diffForHumans() }})</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Carte de contact -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-address-card mr-2"></i> Coordonnées
                    </h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Adresse</dt>
                            <dd class="mt-1 text-sm text-white">{{ $eleve->adresse }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-400">Téléphone</dt>
                                <dd class="mt-1 text-sm text-white">
                                    <a href="tel:{{ $eleve->telephone }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $eleve->telephone }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-white">
                                    @if($eleve->email)
                                        <a href="mailto:{{ $eleve->email }}" class="text-blue-400 hover:text-blue-300">
                                            {{ $eleve->email }}
                                        </a>
                                    @else
                                        Non renseigné
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Carte des parents -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-users mr-2"></i> Parents
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Père -->
                        <div class="bg-dark-600 rounded-lg p-4">
                            <h3 class="font-medium text-white mb-3 flex items-center">
                                <i class="fas fa-male mr-2 text-blue-400"></i> Père
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Nom complet</p>
                                    <p class="text-white">{{ $eleve->nom_pere }}</p>
                                </div>
                                @if($eleve->profession_pere)
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Profession</p>
                                    <p class="text-white">{{ $eleve->profession_pere }}</p>
                                </div>
                                @endif
                                @if($eleve->telephone_pere)
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Téléphone</p>
                                    <a href="tel:{{ $eleve->telephone_pere }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $eleve->telephone_pere }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mère -->
                        <div class="bg-dark-600 rounded-lg p-4">
                            <h3 class="font-medium text-white mb-3 flex items-center">
                                <i class="fas fa-female mr-2 text-pink-400"></i> Mère
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Nom complet</p>
                                    <p class="text-white">{{ $eleve->nom_mere }}</p>
                                </div>
                                @if($eleve->profession_mere)
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Profession</p>
                                    <p class="text-white">{{ $eleve->profession_mere }}</p>
                                </div>
                                @endif
                                @if($eleve->telephone_mere)
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Téléphone</p>
                                    <a href="tel:{{ $eleve->telephone_mere }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $eleve->telephone_mere }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($eleve->adresse_parents)
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-sm font-medium text-gray-400">Adresse des parents</p>
                        <p class="text-white">{{ $eleve->adresse_parents }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Carte des notes et évaluations -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-chart-line mr-2"></i> Notes et évaluations
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-4xl text-gray-600 mb-2"></i>
                        <p class="text-gray-400">Aucune donnée disponible pour le moment</p>
                        <button class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                            Ajouter une évaluation
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne de droite - Activités récentes et statut -->
        <div class="space-y-6">
            <!-- Carte de statut -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-info-circle mr-2"></i> Statut
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Classe actuelle</p>
                            <div class="mt-1 flex items-center">
                                <span class="h-8 w-8 rounded-full bg-dark-600 flex items-center justify-center mr-3">
                                    <i class="fas fa-chalkboard-teacher text-blue-400"></i>
                                </span>
                                <div>
                                    <p class="text-white">{{ $eleve->classe->niveau->nom }} - {{ $eleve->classe->nom }}</p>
                                    <p class="text-xs text-gray-400">Année scolaire: {{ $eleve->classe->annee_scolaire }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Statut de l'élève</p>
                            <div class="mt-2">
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ 
                                    $eleve->status === 'actif' ? 'bg-green-900 text-green-300' : 
                                    ($eleve->status === 'inactif' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300') 
                                }}">
                                    {{ ucfirst($eleve->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Date d'inscription</p>
                            <p class="text-white">{{ $eleve->date_inscription->format('d/m/Y') }} <span class="text-gray-400 text-sm">({{ $eleve->date_inscription->diffForHumans() }})</span></p>
                        </div>
                        
                        @if($eleve->remarques)
                        <div>
                            <p class="text-sm font-medium text-gray-400">Remarques</p>
                            <p class="text-white mt-1 bg-dark-600 p-3 rounded-md">{{ $eleve->remarques }}</p>
                        </div>
                        @endif
                        
                        <div class="pt-4 border-t border-gray-700">
                            <a href="{{ route('admin.eleves.edit', $eleve) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-edit mr-2"></i> Modifier le profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte des paiements -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-credit-card mr-2"></i> Paiements
                    </h2>
                    <a href="{{ route('admin.paiements.eleves.create', ['eleve_id' => $eleve->id]) }}" class="text-xs bg-primary-600 hover:bg-primary-700 text-white px-2 py-1 rounded">
                        <i class="fas fa-plus mr-1"></i> Ajouter
                    </a>
                </div>
                <div class="p-4">
                    @if($eleve->paiements->count() > 0)
                        <div class="space-y-3">
                            @foreach($eleve->paiements->take(3) as $paiement)
                                <div class="bg-dark-600 p-3 rounded-md">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-white">{{ number_format($paiement->montant, 2, ',', ' ') }} DH</p>
                                            <p class="text-xs text-gray-400">{{ $paiement->date_paiement->format('d/m/Y') }} - {{ ucfirst($paiement->methode_paiement) }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $paiement->statut === 'paye' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }}">
                                            {{ ucfirst($paiement->statut) }}
                                        </span>
                                    </div>
                                    @if($paiement->mois)
                                        <p class="text-xs text-gray-400 mt-1">Mois: {{ $paiement->mois }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($eleve->paiements->count() > 3)
                            <div class="mt-3 text-center">
                                <a href="{{ route('admin.paiements.eleves.index', ['eleve_id' => $eleve->id]) }}" class="text-sm text-blue-400 hover:text-blue-300">
                                    Voir tous les paiements ({{ $eleve->paiements->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card text-3xl text-gray-600 mb-2"></i>
                            <p class="text-gray-400 text-sm">Aucun paiement enregistré</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Carte des absences -->
            <div class="bg-dark-700 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-medium text-white">
                        <i class="fas fa-calendar-times mr-2"></i> Absences récentes
                    </h2>
                </div>
                <div class="p-4">
                    @if($eleve->absences->count() > 0)
                        <div class="space-y-3">
                            @foreach($eleve->absences->sortByDesc('date_absence')->take(3) as $absence)
                                <div class="bg-dark-600 p-3 rounded-md">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-white">{{ $absence->date_absence->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-400">{{ $absence->cours->matiere->nom ?? 'Matière non spécifiée' }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">
                                            {{ $absence->justifie ? 'Justifiée' : 'Non justifiée' }}
                                        </span>
                                    </div>
                                    @if($absence->justificatif)
                                        <p class="text-xs text-gray-400 mt-1">Justificatif: {{ $absence->justificatif }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($eleve->absences->count() > 3)
                            <div class="mt-3 text-center">
                                <a href="#" class="text-sm text-blue-400 hover:text-blue-300">
                                    Voir toutes les absences ({{ $eleve->absences->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-3xl text-green-500 mb-2"></i>
                            <p class="text-gray-400 text-sm">Aucune absence enregistrée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des onglets si nécessaire
        const tabs = document.querySelectorAll('[data-tab]');
        if (tabs.length > 0) {
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    // Masquer tous les contenus d'onglet
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    // Désactiver tous les onglets
                    tabs.forEach(t => t.classList.remove('border-blue-500', 'text-blue-400'));
                    // Activer l'onglet cliqué
                    this.classList.add('border-blue-500', 'text-blue-400');
                    // Afficher le contenu correspondant
                    document.getElementById(`tab-${tabId}`).classList.remove('hidden');
                });
            });
            // Activer le premier onglet par défaut
            tabs[0].click();
        }
    });
</script>
@endpush
@endsection
