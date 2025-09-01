@extends('layouts.admin')

@section('title', 'Détails de l\'assistant')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Détails de l'assistant</h1>
            <p class="text-gray-400">Informations détaillées sur l'assistant</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('admin.assistants.edit', $assistant->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
            <a href="{{ route('admin.assistants.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Carte de profil -->
        <div class="lg:col-span-1">
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="h-32 w-32 rounded-full bg-primary-500 flex items-center justify-center text-4xl font-bold text-white">
                            {{ substr($assistant->prenom, 0, 1) }}{{ substr($assistant->nom, 0, 1) }}
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ $assistant->prenom }} {{ $assistant->nom }}</h2>
                    <p class="text-gray-400">Assistant</p>
                    
                    <div class="mt-6 pt-6 border-t border-dark-700">
                        <div class="flex items-center justify-center space-x-4">
                            <a href="mailto:{{ $assistant->email }}" class="text-gray-400 hover:text-white" title="Envoyer un email">
                                <i class="fas fa-envelope text-xl"></i>
                            </a>
                            <a href="tel:{{ $assistant->telephone }}" class="text-gray-400 hover:text-white" title="Appeler">
                                <i class="fas fa-phone-alt text-xl"></i>
                            </a>
                            <button class="text-gray-400 hover:text-white" title="Envoyer un message">
                                <i class="fas fa-comment-alt text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-dark-700 p-6">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">Informations personnelles</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-gray-500 mt-1 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-400">Email</p>
                                <p class="text-sm text-white">{{ $assistant->email }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-gray-500 mt-1 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-400">Téléphone</p>
                                <p class="text-sm text-white">{{ $assistant->telephone }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-birthday-cake text-gray-500 mt-1 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-400">Date de naissance</p>
                                <p class="text-sm text-white">{{ $assistant->date_naissance ? \Carbon\Carbon::parse($assistant->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-500 mt-1 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-400">Adresse</p>
                                <p class="text-sm text-white">{{ $assistant->adresse ?? 'Non renseignée' }}</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-building text-gray-500 mt-1 mr-3 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-400">Centre</p>
                                <p class="text-sm text-white">{{ $assistant->centre->nom ?? 'Non affecté' }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Détails et activités -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statut et informations complémentaires -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Statut et informations complémentaires</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Date d'inscription</p>
                            <p class="text-sm text-white">{{ $assistant->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Dernière connexion</p>
                            <p class="text-sm text-white">{{ $assistant->last_login_at ? \Carbon\Carbon::parse($assistant->last_login_at)->diffForHumans() : 'Jamais connecté' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Statut du compte</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes et commentaires -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-white">Notes et commentaires</h3>
                        <button class="text-primary-500 hover:text-primary-400 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Ajouter une note
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-dark-700 p-4 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold mr-3">
                                        {{ substr(auth()->user()->prenom, 0, 1) }}{{ substr(auth()->user()->nom, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
                                        <p class="text-xs text-gray-400">Aujourd'hui, 14:32</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-gray-400 hover:text-yellow-400">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button class="text-gray-400 hover:text-red-400">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-gray-300">
                                {{ $assistant->prenom }} a été très réactif cette semaine dans la gestion des demandes des élèves. À noter pour une éventuelle promotion.
                            </p>
                        </div>
                        
                        <div class="text-center py-4">
                            <button class="text-primary-500 hover:text-primary-400 text-sm font-medium">
                                <i class="fas fa-history mr-1"></i> Afficher l'historique complet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scripts spécifiques à la page de détails
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des onglets ou autres fonctionnalités JavaScript si nécessaire
    });
</script>
@endpush

@endsection
