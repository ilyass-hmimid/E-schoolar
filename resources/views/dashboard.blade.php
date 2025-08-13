@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Tableau de bord
        </h1>
        <div class="text-sm text-gray-500">
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte Étudiants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-50 text-primary-600 mr-4">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Étudiants</p>
                    <p class="text-2xl font-bold text-gray-900">1,248</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                        </svg>
                        12% ce mois-ci
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte Enseignants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-secondary-50 text-secondary-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Enseignants</p>
                    <p class="text-2xl font-bold text-gray-900">48</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                        </svg>
                        5% ce mois-ci
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte Cours -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Cours</p>
                    <p class="text-2xl font-bold text-gray-900">156</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                        </svg>
                        8% ce mois-ci
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte Taux de présence -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Taux de présence</p>
                    <p class="text-2xl font-bold text-gray-900">94%</p>
                    <p class="text-xs text-red-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 12.586 3.707 8.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L16 9.414V12a1 1 0 102 0V7a1 1 0 00-1-1h-5z" clip-rule="evenodd" />
                        </svg>
                        2% ce mois-ci
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Graphique d'activité -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 h-full">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Activité récente</h3>
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-200 rounded-l-lg hover:bg-primary-100 focus:z-10 focus:ring-1 focus:ring-primary-500 focus:bg-primary-50">
                            Semaine
                        </button>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-200 hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-primary-500 focus:bg-primary-50">
                            Mois
                        </button>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-primary-500 focus:bg-primary-50">
                            Année
                        </button>
                    </div>
                </div>
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <div class="text-center p-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune donnée disponible</h3>
                        <p class="mt-1 text-sm text-gray-500">Les données d'activité seront affichées ici</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prochains événements -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 h-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Prochains événements</h3>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                        Voir tout
                    </a>
                </div>
                <div class="space-y-5">
                    <!-- Événement 1 -->
                    <div class="flex items-start group">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mr-3 mt-0.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Réunion des parents d'élèves</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3.5 w-3.5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Aujourd'hui, 14:00 - 16:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Événement 2 -->
                    <div class="flex items-start group">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 mr-3 mt-0.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Formation des enseignants</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3.5 w-3.5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Demain, 09:00 - 12:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Événement 3 -->
                    <div class="flex items-start group">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 mr-3 mt-0.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Remise des diplômes</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3.5 w-3.5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>20 août 2023, 10:00 - 12:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <a href="#" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors group">
                            <span>Ajouter un événement</span>
                            <svg class="ml-1 h-4 w-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Dernières activités</h3>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                    Voir tout l'historique
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            <!-- Activité 1 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm">
                        JD
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">Jean Dupont</p>
                            <span class="text-xs text-gray-500">Il y a 2 heures</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">A ajouté une nouvelle note</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Mathématiques
                            </span>
                            <span class="ml-2">Terminale A</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activité 2 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-medium text-sm">
                        MS
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">Marie Simon</p>
                            <span class="text-xs text-gray-500">Il y a 5 heures</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">A soumis un devoir</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                En attente
                            </span>
                            <span class="ml-2">Physique - Devoir 3</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activité 3 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium text-sm">
                        AL
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">Admin</p>
                            <span class="text-xs text-gray-500">Hier, 14:30</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">A mis à jour le calendrier</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terminé
                            </span>
                            <span class="ml-2">Ajout des vacances d'hiver</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-2 sm:mb-0">
                Affichage de <span class="font-medium">1</span> à <span class="font-medium">3</span> sur <span class="font-medium">24</span> résultats
            </p>
            <div class="inline-flex rounded-md shadow-sm">
                <button type="button" class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                    <span class="sr-only">Précédent</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="-ml-px relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                    <span class="sr-only">Suivant</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
                </div>
            </div>

    <!-- Aide rapide -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 mb-8 overflow-hidden relative">
        <!-- Décoration graphique -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full opacity-20 -mr-10 -mt-10"></div>
        <div class="absolute bottom-0 right-0 w-20 h-20 bg-blue-300 rounded-full opacity-20 -mr-5 -mb-5"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0 max-w-2xl">
                    <h3 class="text-lg font-semibold text-blue-900 mb-1">Besoin d'aide ?</h3>
                    <p class="text-blue-800">Notre équipe est là pour vous accompagner. Consultez notre centre d'aide ou contactez directement notre support pour une assistance personnalisée.</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="#" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Centre d'aide
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
