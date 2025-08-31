@extends('layouts.app')

@section('title', 'Tableau de bord - Assistant')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Tableau de bord - Assistant
        </h1>
        <div class="text-sm text-gray-500">
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Carte Élèves -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Élèves</p>
                    <p class="text-2xl font-bold text-gray-900">248</p>
                </div>
            </div>
        </div>

        <!-- Carte Présences -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Présences</p>
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

        <!-- Carte Absences -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Absences</p>
                    <p class="text-2xl font-bold text-gray-900">24</p>
                    <p class="text-xs text-red-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                        </svg>
                        5% ce mois-ci
                    </p>
                </div>
            </div>
        </div>

        <!-- Carte Retards -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Retards</p>
                    <p class="text-2xl font-bold text-gray-900">18</p>
                    <p class="text-xs text-yellow-600 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                        </svg>
                        Stable
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières absences -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Dernières absences</h3>
            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 5; $i++)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600">JD</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Jean Dupont</div>
                                    <div class="text-sm text-gray-500">jean.dupont@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Terminale A</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">15/05/2023</div>
                            <div class="text-sm text-gray-500">08:00 - 10:00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Mathématiques</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Non justifiée
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                            <a href="#" class="text-red-600 hover:text-red-900">Supprimer</a>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Planning du jour -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Planning d'aujourd'hui</h3>
            <div class="space-y-4">
                @for($i = 1; $i <= 3; $i++)
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">Mathématiques - Terminale A</h4>
                            <p class="text-sm text-gray-500">08:00 - 10:00</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Salle 201
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Thème : Géométrie dans l'espace</p>
                        <p class="mt-1">Professeur : M. Martin</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Tâches à faire -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tâches à faire</h3>
                <button type="button" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouvelle tâche
                </button>
            </div>
            <div class="space-y-4">
                @for($i = 1; $i <= 4; $i++)
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="task-{{ $i }}" name="task-{{ $i }}" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="task-{{ $i }}" class="font-medium text-gray-700">Tâche importante à faire {{ $i }}</label>
                        <p class="text-gray-500">Description de la tâche à accomplir</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
@endsection
