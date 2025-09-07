@extends('admin.layout')

@section('title', 'Matières de ' . $etudiant->name)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.etudiants.index') }}" class="text-indigo-600 hover:text-indigo-900">Étudiants</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="text-indigo-600 hover:text-indigo-900">{{ $etudiant->name }}</a>
            <span class="text-gray-400 mx-2">/</span>
            Matières
        </h1>
    </div>
@endsection

@section('header-actions')
    <a href="{{ route('admin.etudiants.matieres.ajouter', $etudiant) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Ajouter une matière
    </a>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Matières suivies par {{ $etudiant->name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Gestion des inscriptions aux matières pour cet étudiant
            </p>
        </div>
        
        @if($etudiant->matieres->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Matière
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Professeur principal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix mensuel
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date d'inscription
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($etudiant->matieres as $matiere)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-1 rounded-full" style="background-color: {{ $matiere->couleur }}"></div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $matiere->nom }}
                                            </div>
                                            @if($matiere->pivot->est_actif)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($matiere->professeurs->count() > 0)
                                        <div class="text-sm text-gray-900">
                                            {{ $matiere->professeurs->first()->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $matiere->professeurs->first()->email }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Aucun professeur</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($matiere->prix_mensuel, 2, ',', ' ') }} DH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $matiere->pivot->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('admin.etudiants.matieres.toggle-status', [$etudiant, $matiere]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            @if($matiere->pivot->est_actif)
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Désactiver">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Activer">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>
                                        
                                        <form action="{{ route('admin.etudiants.matieres.retirer', [$etudiant, $matiere]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cette matière ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Retirer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune matière</h3>
                <p class="mt-1 text-sm text-gray-500">Cet étudiant n'est inscrit à aucune matière pour le moment.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.etudiants.matieres.ajouter', $etudiant) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter une matière
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($etudiant->matieres->count() > 0)
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Récapitulatif des paiements
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Vue d'ensemble des paiements pour les matières sélectionnées
                </p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">Total mensuel</h4>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($etudiant->matieres->sum('prix_mensuel'), 2, ',', ' ') }} DH
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">Payé ce mois</h4>
                                <p class="text-2xl font-semibold text-gray-900">
                                    0,00 DH
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-500">Reste à payer</h4>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($etudiant->matieres->sum('prix_mensuel'), 2, ',', ' ') }} DH
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enregistrer un paiement
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
