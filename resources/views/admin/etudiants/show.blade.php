@extends('admin.layout')

@section('title', 'Détails de l\'étudiant : ' . $etudiant->name)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.etudiants.index') }}" class="text-indigo-600 hover:text-indigo-900">Étudiants</a>
            <span class="text-gray-400 mx-2">/</span>
            {{ $etudiant->name }}
        </h1>
    </div>
@endsection

@section('header-actions')
    <div class="flex space-x-2">
        <a href="{{ route('admin.etudiants.edit', $etudiant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Modifier
        </a>
        <a href="{{ route('admin.etudiants.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Informations personnelles
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Détails de l'étudiant
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Nom complet
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->name }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->email }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Téléphone
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->telephone ?? 'Non renseigné' }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Date d'inscription
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->created_at->format('d/m/Y') }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Statut
                    </dt>
                    <dd class="mt-1">
                        @if($etudiant->est_actif)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Actif
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </dd>
                </div>
                @if($etudiant->adresse)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Adresse
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->adresse }}
                    </dd>
                </div>
                @endif
                @if($etudiant->date_naissance)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Date de naissance
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->date_naissance->format('d/m/Y') }}
                        ({{ now()->diffInYears($etudiant->date_naissance) }} ans)
                    </dd>
                </div>
                @endif
                @if($etudiant->lieu_naissance)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Lieu de naissance
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $etudiant->lieu_naissance }}
                    </dd>
                </div>
                @endif
                @if($etudiant->nom_pere || $etudiant->nom_mere)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Parents
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($etudiant->nom_pere)
                            <div>Père : {{ $etudiant->nom_pere }}</div>
                        @endif
                        @if($etudiant->nom_mere)
                            <div>Mère : {{ $etudiant->nom_mere }}</div>
                        @endif
                    </dd>
                </div>
                @endif
                @if($etudiant->remarques)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Remarques
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                        {{ $etudiant->remarques }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Matières suivies
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Liste des matières auxquelles l'étudiant est inscrit
                </p>
            </div>
            <a href="{{ route('admin.etudiants.matieres.ajouter', $etudiant) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter une matière
            </a>
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
                                Professeur
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
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($matiere->professeurs->count() > 0)
                                        <div class="text-sm text-gray-900">
                                            {{ $matiere->professeurs->first()->name }}
                                            @if($matiere->professeurs->count() > 1)
                                                <span class="text-xs text-gray-500">+{{ $matiere->professeurs->count() - 1 }} autres</span>
                                            @endif
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
                                    <form action="{{ route('admin.etudiants.matieres.retirer', [$etudiant, $matiere]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet étudiant de cette matière ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Retirer de la matière">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
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

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Paiements
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Historique des paiements de l'étudiant
            </p>
        </div>
        <div class="px-4 py-5 sm:px-6">
            <p class="text-sm text-gray-500">
                Aucun paiement enregistré pour le moment.
            </p>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Absences
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Historique des absences de l'étudiant
            </p>
        </div>
        <div class="px-4 py-5 sm:px-6">
            <p class="text-sm text-gray-500">
                Aucune absence enregistrée pour le moment.
            </p>
        </div>
    </div>
@endsection
