@extends('layouts.admin')

@section('title', 'Centre d\'aide')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Centre d'aide</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Trouvez des réponses à vos questions et apprenez à utiliser la plateforme Allo Tawjih.
            </p>
        </div>

        <!-- Barre de recherche -->
        <div class="mb-12">
            <div class="relative max-w-2xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" 
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                       placeholder="Rechercher dans l'aide...">
            </div>
        </div>

        <!-- Catégories d'aide -->
        <div class="grid md:grid-cols-2 gap-6 mb-12">
            <!-- Carte 1 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Gestion des élèves</h3>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Apprenez à gérer les dossiers des élèves, les inscriptions et les suivis académiques.</p>
                        <ul class="mt-3 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Ajouter un nouvel élève</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Gérer les inscriptions</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Consulter le dossier scolaire</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Carte 2 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Gestion des cours</h3>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Créez et gérez les cours, les emplois du temps et les ressources pédagogiques.</p>
                        <ul class="mt-3 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Planifier un nouveau cours</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Gérer les emplois du temps</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Partager des ressources</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Carte 3 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <i class="fas fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Paiements et facturation</h3>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Gérez les paiements, les factures et les reçus des élèves.</p>
                        <ul class="mt-3 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Enregistrer un paiement</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Générer une facture</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Consulter l'historique</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Carte 4 -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                            <i class="fas fa-cog text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Paramètres et configuration</h3>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Personnalisez les paramètres de votre compte et de l'application.</p>
                        <ul class="mt-3 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Modifier le profil</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Paramètres de notification</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-xs mt-1 text-primary-500 mr-2"></i>
                                <span>Sécurité du compte</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section contact -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border border-gray-200">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Besoin d'aide supplémentaire ?</h2>
                <p class="text-gray-600 mb-6">Notre équipe est là pour vous aider</p>
                <a href="{{ route('support.contact') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-envelope mr-2"></i> Contacter le support
                </a>
            </div>
        </div>

        <!-- FAQ -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Questions fréquentes</h2>
            <div class="space-y-4">
                <!-- Question 1 -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = !open" class="w-full px-6 py-4 text-left focus:outline-none">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-medium text-gray-900">Comment réinitialiser mon mot de passe ?</span>
                            <svg :class="{ 'transform rotate-180': open }" class="h-5 w-5 text-gray-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-6 pb-4 text-gray-600">
                        <p>Pour réinitialiser votre mot de passe, cliquez sur le lien "Mot de passe oublié ?" sur la page de connexion. Entrez votre adresse email et suivez les instructions que vous recevrez par email pour créer un nouveau mot de passe.</p>
                    </div>
                </div>

                <!-- Question 2 -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = !open" class="w-full px-6 py-4 text-left focus:outline-none">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-medium text-gray-900">Comment ajouter un nouvel élève au système ?</span>
                            <svg :class="{ 'transform rotate-180': open }" class="h-5 w-5 text-gray-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div x-show="open" x-transition class="px-6 pb-4 text-gray-600">
                        <p>Pour ajouter un nouvel élève, accédez à la section "Élèves" dans le menu de navigation, puis cliquez sur le bouton "Ajouter un élève". Remplissez le formulaire avec les informations requises et enregistrez. L'élève recevra un email avec ses identifiants de connexion.</p>
                    </div>
                </div>

                <!-- Question 3 -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = !open" class="w-full px-6 py-4 text-left focus:outline-none">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-medium text-gray-900">Comment générer un rapport d'absences ?</span>
                            <svg :class="{ 'transform rotate-180': open }" class="h-5 w-5 text-gray-500 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div x-show="open" x-transition class="px-6 pb-4 text-gray-600">
                        <p>Pour générer un rapport d'absences, accédez à la section "Rapports" dans le menu principal. Sélectionnez "Absences" comme type de rapport, puis définissez la période et les filtres souhaités. Cliquez sur "Générer le rapport" pour le télécharger au format PDF ou Excel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
