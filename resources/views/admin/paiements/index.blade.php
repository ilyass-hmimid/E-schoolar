@extends('layouts.admin')

@section('title', 'Gestion des Paiements')

@section('content')
    <div class="bg-dark-900 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-white">Gestion des Paiements</h2>
            <a href="{{ route('admin.paiements.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>Nouveau Paiement
            </a>
        </div>

        <div class="bg-dark-800 rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-dark-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" class="bg-dark-700 text-white w-full pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Rechercher des paiements...">
                        </div>
                    </div>
                    <div class="ml-4">
                        <select class="bg-dark-700 text-white rounded-lg border-0 focus:ring-2 focus:ring-primary-500">
                            <option>Tous les statuts</option>
                            <option>Payés</option>
                            <option>En attente</option>
                            <option>Annulés</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-dark-700">
                    <thead class="bg-dark-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Élève</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-dark-800 divide-y divide-dark-700">
                        <!-- Exemple de ligne de données -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold">JD</div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">John Doe</div>
                                        <div class="text-sm text-gray-400">Classe A1</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Mensualité
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">1 500,00 MAD</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">15/03/2023</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Payé
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-primary-500 hover:text-primary-400 mr-4">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="text-blue-500 hover:text-blue-400 mr-4">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-red-500 hover:text-red-400">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- Plus de lignes ici... -->
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-dark-700 flex items-center justify-between">
                <div class="text-sm text-gray-400">
                    Affichage de <span class="font-medium">1</span> à <span class="font-medium">10</span> sur <span class="font-medium">24</span> résultats
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 rounded-lg bg-dark-700 text-gray-300 hover:bg-dark-600 disabled:opacity-50" disabled>
                        Précédent
                    </button>
                    <button class="px-3 py-1 rounded-lg bg-primary-600 text-white">
                        1
                    </button>
                    <button class="px-3 py-1 rounded-lg bg-dark-700 text-gray-300 hover:bg-dark-600">
                        2
                    </button>
                    <button class="px-3 py-1 rounded-lg bg-dark-700 text-gray-300 hover:bg-dark-600">
                        3
                    </button>
                    <button class="px-3 py-1 rounded-lg bg-dark-700 text-gray-300 hover:bg-dark-600">
                        Suivant
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
