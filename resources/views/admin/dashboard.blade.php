@extends('layouts.admin')

@section('title', 'Tableau de bord')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css" rel="stylesheet">
<style>
    /* Styles spécifiques au dashboard */
    .stat-card {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .stat-card.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .stat-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
        transition: all 0.3s ease;
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0.25rem 0;
        font-family: 'Inter', sans-serif;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #94a3b8;
        font-weight: 500;
        letter-spacing: 0.01em;
    }
    
    /* Animation pour les éléments de la liste */
    .activity-item {
        opacity: 0;
        transform: translateX(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .activity-item.visible {
        opacity: 1;
        transform: translateX(0);
    }
    
    /* Style pour les boutons de période */
    .period-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .period-btn.active {
        background-color: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    
    .period-btn:not(.active) {
        background-color: #1e293b;
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .period-btn:not(.active):hover {
        background-color: #1e293b;
        color: #e2e8f0;
    }
    
    /* Style pour les éléments de la liste des activités */
    .activity-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    /* Animation de chargement */
    @keyframes shimmer {
        0% { background-position: -468px 0; }
        100% { background-position: 468px 0; }
    }
    
    .shimmer {
        background: linear-gradient(to right, #1e293b 8%, #2d3748 18%, #1e293b 33%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite linear;
    }
</style>
@endpush

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Tableau de bord</h1>
            <p class="text-sm md:text-base text-gray-400 mt-1">Bienvenue dans votre espace d'administration</p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                </span>
                <input type="text" class="bg-dark-800 border border-dark-700 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5" 
                       placeholder="Filtrer par date" value="{{ now()->format('d M Y') }}">
            </div>
        </div>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Carte Élèves -->
    <div class="stat-card bg-gradient-to-br from-dark-800 to-dark-900 p-6 rounded-xl border border-dark-700">
        <div class="flex items-center">
            <div class="stat-icon bg-blue-500/10 text-blue-400">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <p class="stat-value">{{ $elevesCount ?? 0 }}</p>
                <p class="stat-label">Élèves inscrits</p>
            </div>
        </div>
    </div>

    <!-- Carte Professeurs -->
    <div class="stat-card bg-gradient-to-br from-dark-800 to-dark-900 p-6 rounded-xl border border-dark-700">
        <div class="flex items-center">
            <div class="stat-icon bg-purple-500/10 text-purple-400">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <p class="stat-value">{{ $professeursCount ?? 0 }}</p>
                <p class="stat-label">Professeurs</p>
            </div>
        </div>
    </div>

    <!-- Carte Cours -->
    <div class="stat-card bg-gradient-to-br from-dark-800 to-dark-900 p-6 rounded-xl border border-dark-700">
        <div class="flex items-center">
            <div class="stat-icon bg-green-500/10 text-green-400">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <p class="stat-value">{{ $coursCount ?? 0 }}</p>
                <p class="stat-label">Cours actifs</p>
            </div>
        </div>
    </div>

    <!-- Carte Revenus -->
    <div class="stat-card bg-gradient-to-br from-dark-800 to-dark-900 p-6 rounded-xl border border-dark-700">
        <div class="flex items-center">
            <div class="stat-icon bg-yellow-500/10 text-yellow-400">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <p class="stat-value">{{ number_format($revenus ?? 0, 0, ',', ' ') }} DH</p>
                <p class="stat-label">Revenus annuels</p>
            </div>
        </div>
    </div>
</div>

<!-- Graphique des revenus et activités récentes -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Graphique des revenus -->
    <div class="lg:col-span-2 bg-gradient-to-br from-dark-800 to-dark-900 rounded-xl border border-dark-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-white">Revenus mensuels</h3>
                <p class="text-sm text-gray-400">Évolution des revenus sur {{ now()->year }}</p>
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm rounded-lg bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors">
                    Année
                </button>
                <button class="px-3 py-1 text-sm rounded-lg bg-dark-700 text-gray-400 hover:bg-dark-600 hover:text-white transition-colors">
                    Mois
                </button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="bg-gradient-to-br from-dark-800 to-dark-900 rounded-xl border border-dark-700 overflow-hidden">
        <div class="p-6 border-b border-dark-700">
            <h3 class="text-lg font-semibold text-white">Activités récentes</h3>
            <p class="text-sm text-gray-400">Dernières actions sur la plateforme</p>
        </div>
        <div class="divide-y divide-dark-700">
            @forelse($activitesRecentes as $activite)
            <div class="p-4 hover:bg-dark-700/50 transition-colors">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-{{ $activite['couleur'] }}-500/10 flex items-center justify-center text-{{ $activite['couleur'] }}-400">
                        <i class="{{ $activite['icone'] }}"></i>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="text-sm font-medium text-white">{{ $activite['titre'] }}</p>
                        <p class="text-sm text-gray-400">{{ $activite['description'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="far fa-clock mr-1"></i> {{ $activite['date']->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center">
                <div class="text-gray-500 mb-2">
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
                <p class="text-sm text-gray-400">Aucune activité récente</p>
            </div>
            @endforelse
        </div>
        @if(count($activitesRecentes) > 0)
        <div class="p-4 border-t border-dark-700 text-center">
            <a href="#" class="text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                Voir toutes les activités
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Section Cours et Statistiques -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Prochains Cours -->
    <div class="bg-gradient-to-br from-dark-800 to-dark-900 rounded-xl border border-dark-700 overflow-hidden">
        <div class="p-6 border-b border-dark-700">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">Prochains cours</h3>
                    <p class="text-sm text-gray-400">Vos prochaines séances programmées</p>
                </div>
                <a href="{{ route('admin.cours.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors">
                    Voir tout
                </a>
            </div>
        </div>
        <div class="divide-y divide-dark-700">
            @forelse($prochainsCours as $cours)
            <div class="p-4 hover:bg-dark-750/50 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="mt-1">
                        <div class="w-10 h-10 rounded-lg bg-{{ $cours['couleur'] }}-500/10 flex items-center justify-center text-{{ $cours['couleur'] }}-400">
                            <i class="{{ $cours['icone'] }}"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h4 class="text-sm font-medium text-white">{{ $cours['matiere'] }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $cours['statut_couleur'] }}-100 text-{{ $cours['statut_couleur'] }}-800">
                                {{ $cours['statut'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400">Avec {{ $cours['professeur'] }}</p>
                        <div class="mt-2 flex items-center text-xs text-gray-500">
                            <i class="far fa-calendar-alt mr-1.5"></i>
                            <span>{{ $cours['date']->isoFormat('DD MMM YYYY') }}</span>
                            <span class="mx-2">•</span>
                            <i class="far fa-clock mr-1.5"></i>
                            <span>{{ $cours['heure_debut'] }} - {{ $cours['heure_fin'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center">
                <p class="text-gray-400">Aucun cours à venir</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="bg-gradient-to-br from-dark-800 to-dark-900 rounded-xl border border-dark-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-white">Statistiques rapides</h3>
                <p class="text-sm text-gray-400">Aperçu des performances</p>
            </div>
            <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-dark-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-green-500/10 text-green-400 mr-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Taux de présence</p>
                        <p class="text-lg font-semibold text-white">94%</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-400">+2.5%</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-dark-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 mr-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Tâches terminées</p>
                        <p class="text-lg font-semibold text-white">18/24</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-400">En cours</span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-dark-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-purple-500/10 text-purple-400 mr-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Notifications</p>
                        <p class="text-lg font-semibold text-white">3 nouvelles</p>
                    </div>
                </div>
                <a href="#" class="text-xs px-3 py-1 rounded-lg bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 transition-colors">
                    Voir
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Section Aide rapide -->
<div class="bg-gradient-to-r from-blue-900/40 to-blue-800/40 rounded-xl p-6 mb-8 overflow-hidden relative border border-blue-700/30 shadow-lg">
    <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-800/30 to-transparent"></div>
    <div class="relative z-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-blue-500/20 text-blue-300 mr-4 transform transition-transform hover:scale-105">
                    <i class="fas fa-question-circle text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Besoin d'aide ?</h3>
                    <p class="text-blue-100/80 mt-1">Nous sommes là pour vous accompagner</p>
                </div>
            </div>
            <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-headset mr-2"></i>
                Contacter le support
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="#" class="group p-4 bg-blue-900/20 hover:bg-blue-800/30 rounded-lg border border-blue-800/30 transition-all duration-200 hover:border-blue-600/50 hover:shadow-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-500/20 text-blue-300 mr-3 group-hover:bg-blue-400/30 transition-colors">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-white group-hover:text-blue-200">Centre d'aide</h4>
                        <p class="text-xs text-blue-100/60 group-hover:text-blue-100/80">Guides et tutoriels</p>
                    </div>
                </div>
            </a>
            
            <a href="#" class="group p-4 bg-blue-900/20 hover:bg-blue-800/30 rounded-lg border border-blue-800/30 transition-all duration-200 hover:border-blue-600/50 hover:shadow-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-500/20 text-blue-300 mr-3 group-hover:bg-blue-400/30 transition-colors">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-white group-hover:text-blue-200">Vidéos</h4>
                        <p class="text-xs text-blue-100/60 group-hover:text-blue-100/80">Tutoriels vidéo</p>
                    </div>
                </div>
            </a>
            
            <a href="#" class="group p-4 bg-blue-900/20 hover:bg-blue-800/30 rounded-lg border border-blue-800/30 transition-all duration-200 hover:border-blue-600/50 hover:shadow-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-500/20 text-blue-300 mr-3 group-hover:bg-blue-400/30 transition-colors">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-white group-hover:text-blue-200">Documentation</h4>
                        <p class="text-xs text-blue-100/60 group-hover:text-blue-100/80">Manuels et références</p>
                    </div>
                </div>
            </a>
            
            <a href="#" class="group p-4 bg-blue-900/20 hover:bg-blue-800/30 rounded-lg border border-blue-800/30 transition-all duration-200 hover:border-blue-600/50 hover:shadow-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-500/20 text-blue-300 mr-3 group-hover:bg-blue-400/30 transition-colors">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-white group-hover:text-blue-200">FAQ</h4>
                        <p class="text-xs text-blue-100/60 group-hover:text-blue-100/80">Questions fréquentes</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Section de contact d'urgence -->
        <div class="mt-8 pt-6 border-t border-blue-800/30">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-blue-900/20 rounded-xl p-4">
                <div class="flex items-start sm:items-center">
                    <div class="bg-blue-500/20 p-2 rounded-lg mr-3">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-white">Urgence ?</h4>
                        <p class="text-xs text-blue-100/60">Contactez notre équipe 24/7</p>
                    </div>
                </div>
                <a href="tel:+212600000000" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg text-sm font-medium transition-all duration-200 transform hover:-translate-y-0.5">
                    <i class="fas fa-phone-alt mr-2"></i>
                    Appeler le support
                </a>
            </div>
        </div>
    </div>
    <div class="absolute right-6 bottom-0 h-32 w-32 text-blue-400/10">
        <i class="fas fa-life-ring text-8xl"></i>
    </div>
</div>

<!-- Liens rapides -->
<div class="p-6">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white">Accès rapide</h3>
                <p class="text-sm text-gray-400">Accédez rapidement aux fonctionnalités principales</p>
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white transition-colors">
                    <i class="fas fa-th-large mr-1"></i> Grille
                </button>
                <button class="px-3 py-1.5 text-xs rounded-lg bg-dark-600 hover:bg-dark-500 text-gray-300 transition-colors">
                    <i class="fas fa-list mr-1"></i> Liste
                </button>
            </div>
        </div>

        <!-- Catégorie : Gestion des utilisateurs -->
        <div>
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Gestion des utilisateurs</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-blue-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 group-hover:bg-blue-500/20 text-blue-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-blue-300">Nouvel élève</span>
                    <span class="text-xs text-gray-400 mt-1">Inscription rapide</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-green-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-green-500/10 group-hover:bg-green-500/20 text-green-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-green-300">Nouveau professeur</span>
                    <span class="text-xs text-gray-400 mt-1">Ajout rapide</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-purple-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 group-hover:bg-purple-500/20 text-purple-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-users-cog text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-purple-300">Gestion des rôles</span>
                    <span class="text-xs text-gray-400 mt-1">Permissions</span>
                </a>
            </div>
        </div>

        <!-- Catégorie : Gestion pédagogique -->
        <div class="pt-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Pédagogie</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-indigo-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 group-hover:bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-book text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-indigo-300">Nouveau cours</span>
                    <span class="text-xs text-gray-400 mt-1">Création rapide</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-yellow-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/10 group-hover:bg-yellow-500/20 text-yellow-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-calendar-plus text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-yellow-300">Planifier un cours</span>
                    <span class="text-xs text-gray-400 mt-1">Calendrier</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-pink-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-pink-500/10 group-hover:bg-pink-500/20 text-pink-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-tasks text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-pink-300">Devoirs</span>
                    <span class="text-xs text-gray-400 mt-1">Créer un devoir</span>
                </a>
            </div>
        </div>

        <!-- Catégorie : Administration -->
        <div class="pt-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Administration</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-red-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 group-hover:bg-red-500/20 text-red-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-file-invoice text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-red-300">Facturation</span>
                    <span class="text-xs text-gray-400 mt-1">Créer une facture</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-cyan-500/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 group-hover:bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-chart-bar text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-cyan-300">Rapports</span>
                    <span class="text-xs text-gray-400 mt-1">Statistiques</span>
                </a>
                <a href="#" class="group p-4 bg-dark-700/50 hover:bg-dark-600/80 rounded-xl border border-dark-600 hover:border-gray-400/30 transition-all duration-200 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-gray-500/10 group-hover:bg-gray-500/20 text-gray-400 flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </div>
                    <span class="text-sm font-medium text-white group-hover:text-gray-200">Paramètres</span>
                    <span class="text-xs text-gray-400 mt-1">Configuration</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration du graphique des revenus
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels ?? []),
                datasets: [{
                    label: 'Revenus (DH)',
                    data: @json($revenusParMois ?? []),
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderColor: 'rgba(99, 102, 241, 0.8)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(99, 102, 241, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(99, 102, 241, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(99, 102, 241, 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.y.toLocaleString() + ' DH';
                            },
                            title: function(tooltipItems) {
                                const date = new Date(2023, tooltipItems[0].dataIndex, 1);
                                return date.toLocaleString('fr-FR', { month: 'long' });
                            }
                        },
                        titleFont: {
                            weight: '600',
                            size: 14
                        },
                        bodyFont: {
                            size: 16,
                            weight: '600'
                        },
                        padding: {
                            top: 10,
                            right: 15,
                            bottom: 10,
                            left: 15
                        },
                        cornerRadius: 8
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.03)',
                            drawBorder: false,
                            drawTicks: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 11
                            },
                            padding: 10,
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value / 1000) + 'k DH';
                                }
                                return value + ' DH';
                            }
                        }
                    }
                },
                elements: {
                    line: {
                        borderJoinStyle: 'round',
                        borderCapStyle: 'round'
                    },
                    point: {
                        radius: 0,
                        hoverRadius: 6
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Animation des cartes de statistiques
        const statCards = document.querySelectorAll('.stat-card');
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        statCards.forEach((card, index) => {
            card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Animation des éléments de la liste des activités
        const activityItems = document.querySelectorAll('.activity-item');
        activityItems.forEach((item, index) => {
            item.style.transition = `opacity 0.3s ease ${index * 0.1}s, transform 0.3s ease ${index * 0.1}s`;
            
            const activityObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });
            
            activityObserver.observe(item);
        });

        // Gestion du changement de période pour le graphique
        const periodButtons = document.querySelectorAll('[data-period]');
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                periodButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                // Ici, vous pouvez ajouter la logique pour changer les données du graphique
                // en fonction de la période sélectionnée (this.dataset.period)
                console.log('Période sélectionnée :', this.dataset.period);
            });
        });
    });
</script>
@endpush

@endsection
