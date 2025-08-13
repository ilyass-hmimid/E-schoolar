<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-bold text-2xl text-gray-900">
                {{ __('Tableau de bord') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ now()->translatedFormat('l d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Carte Étudiants -->
                <div class="card">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary-50 text-primary-600 mr-4">
                            <i class="fas fa-user-graduate text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Étudiants</p>
                            <p class="text-2xl font-bold text-gray-900">1,248</p>
                            <p class="text-xs text-green-600">
                                <i class="fas fa-arrow-up mr-1"></i> 12% ce mois-ci
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Carte Enseignants -->
                <div class="card">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-secondary-50 text-secondary-600 mr-4">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Enseignants</p>
                            <p class="text-2xl font-bold text-gray-900">48</p>
                            <p class="text-xs text-green-600">
                                <i class="fas fa-arrow-up mr-1"></i> 5% ce mois-ci
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Carte Cours -->
                <div class="card">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Cours</p>
                            <p class="text-2xl font-bold text-gray-900">156</p>
                            <p class="text-xs text-green-600">
                                <i class="fas fa-arrow-up mr-1"></i> 8% ce mois-ci
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Carte Taux de présence -->
                <div class="card">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                            <i class="fas fa-clipboard-check text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Taux de présence</p>
                            <p class="text-2xl font-bold text-gray-900">94%</p>
                            <p class="text-xs text-red-600">
                                <i class="fas fa-arrow-down mr-1"></i> 2% ce mois-ci
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et tableaux -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Graphique d'activité -->
                <div class="lg:col-span-2">
                    <div class="card h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Activité récente</h3>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 text-sm rounded-lg bg-primary-100 text-primary-700">Semaine</button>
                                <button class="px-3 py-1 text-sm rounded-lg text-gray-500 hover:bg-gray-100">Mois</button>
                                <button class="px-3 py-1 text-sm rounded-lg text-gray-500 hover:bg-gray-100">Année</button>
                            </div>
                        </div>
                        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                            <p class="text-gray-400">Graphique d'activité sera affiché ici</p>
                        </div>
                    </div>
                </div>

                <!-- Prochains événements -->
                <div>
                    <div class="card h-full">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Prochains événements</h3>
                        <div class="space-y-4">
                            <!-- Événement 1 -->
                            <div class="flex items-start">
                                <div class="bg-primary-100 text-primary-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-3">15:00</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Réunion des enseignants</h4>
                                    <p class="text-sm text-gray-500">Salle de conférence</p>
                                </div>
                            </div>
                            <!-- Événement 2 -->
                            <div class="flex items-start">
                                <div class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-3">Demain</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Délai de remise des notes</h4>
                                    <p class="text-sm text-gray-500">Tous les enseignants</p>
                                </div>
                            </div>
                            <!-- Événement 3 -->
                            <div class="flex items-start">
                                <div class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-3">Ven</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Sortie pédagogique</h4>
                                    <p class="text-sm text-gray-500">Classes de Terminale</p>
                                </div>
                            </div>
                            <!-- Bouton Voir tout -->
                            <div class="pt-2">
                                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800 flex items-center">
                                    Voir tout le calendrier
                                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières activités -->
            <div class="card mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Dernières activités</h3>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-800">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Ligne 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-medium text-sm mr-3">
                                            JD
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Jean Dupont</div>
                                            <div class="text-sm text-gray-500">Enseignant</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">A ajouté une nouvelle note</div>
                                    <div class="text-sm text-gray-500">Mathématiques - Terminale A</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Il y a 2 heures
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Terminé
                                    </span>
                                </td>
                            </tr>
                            <!-- Ligne 2 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-medium text-sm mr-3">
                                            MS
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Marie Simon</div>
                                            <div class="text-sm text-gray-500">Étudiante</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">A soumis un devoir</div>
                                    <div class="text-sm text-gray-500">Physique - Devoir 3</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Il y a 5 heures
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        En attente
                                    </span>
                                </td>
                            </tr>
                            <!-- Ligne 3 -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium text-sm mr-3">
                                            AL
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Admin</div>
                                            <div class="text-sm text-gray-500">Administrateur</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">A mis à jour le calendrier</div>
                                    <div class="text-sm text-gray-500">Ajout des vacances d'hiver</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Hier, 14:30
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Terminé
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aide rapide -->
            <div class="bg-blue-50 rounded-xl p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-lg font-semibold text-blue-900">Besoin d'aide ?</h3>
                        <p class="text-blue-700">Consultez notre centre d'aide ou contactez notre équipe de support.</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="#" class="px-4 py-2 bg-white text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors text-sm font-medium">
                            Centre d'aide
                        </a>
                        <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Contacter le support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
