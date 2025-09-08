@extends('admin.layout')

@section('title', 'Détails de la matière : ' . $matiere->nom)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.matieres.index') }}" class="text-indigo-600 hover:text-indigo-900">Matières</a>
            <span class="text-gray-400 mx-2">/</span>
            {{ $matiere->nom }}
        </h1>
        <span class="ml-3 px-3 py-1 rounded-full text-xs font-medium {{ $matiere->est_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
            {{ $matiere->est_active ? 'Active' : 'Inactive' }}
        </span>
    </div>
@endsection

@section('header-actions')
    <div class="flex space-x-2">
        <a href="{{ route('admin.matieres.edit', $matiere) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Modifier
        </a>
        @if($matiere->est_active)
            <form action="{{ route('admin.matieres.desactiver', $matiere) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                    Désactiver
                </button>
            </form>
        @else
            <form action="{{ route('admin.matieres.activer', $matiere) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Activer
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Détails de la matière -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-md flex items-center justify-center" style="background-color: {{ $matiere->couleur }}20;">
                            @if($matiere->icone)
                                <i class="{{ $matiere->icone }} text-xl" style="color: {{ $matiere->couleur }};"></i>
                            @else
                                <span class="text-xl font-medium" style="color: {{ $matiere->couleur }};">
                                    {{ strtoupper(substr($matiere->nom, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $matiere->nom }}
                                @if($matiere->est_fixe)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Matière fixe
                                    </span>
                                @endif
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ $matiere->niveau_libelle }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Description
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $matiere->description ?? 'Aucune description fournie' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Niveau d'enseignement
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $matiere->niveau_libelle }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Prix mensuel
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ number_format($matiere->prix_mensuel, 2, ',', ' ') }} DH
                            </dd>
                        </div>
                        @if($matiere->prix_trimestriel)
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Prix trimestriel
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ number_format($matiere->prix_trimestriel, 2, ',', ' ') }} DH
                                @php
                                    $rabais = (1 - ($matiere->prix_trimestriel / ($matiere->prix_mensuel * 3))) * 100;
                                @endphp
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    {{ round($rabais) }}% d'économie
                                </span>
                            </dd>
                        </div>
                        @endif
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Apparence
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 space-y-2">
                                <div class="flex items-center">
                                    <span class="w-24 text-gray-500">Couleur :</span>
                                    <div class="h-6 w-6 rounded-full mr-2 border border-gray-300" style="background-color: {{ $matiere->couleur }};"></div>
                                    <span class="font-mono">{{ $matiere->couleur }}</span>
                                </div>
                                @if($matiere->icone)
                                <div class="flex items-center">
                                    <span class="w-24 text-gray-500">Icône :</span>
                                    <div class="flex items-center justify-center h-6 w-6 mr-2">
                                        <i class="{{ $matiere->icone }} text-lg" style="color: {{ $matiere->couleur }};"></i>
                                    </div>
                                    <span class="font-mono">{{ $matiere->icone }}</span>
                                </div>
                                @endif
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Date de création
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $matiere->created_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Dernière mise à jour
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $matiere->updated_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="space-y-6">
            <!-- Carte Élèves -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Élèves inscrits
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stats['nombre_eleves'] }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <a href="{{ route('admin.matieres.eleves', $matiere) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Voir la liste
                                        </a>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.matieres.ajouter-eleves', $matiere) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Ajouter des élèves <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carte Professeurs -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Professeurs
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stats['nombre_professeurs'] }}
                                    </div>
                                    @if($stats['nombre_professeurs'] > 0)
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                            <a href="{{ route('admin.matieres.professeurs', $matiere) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                Voir la liste
                                            </a>
                                        </div>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.matieres.ajouter-professeurs', $matiere) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Gérer les professeurs <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carte Revenu -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Revenu mensuel
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($stats['revenu_mensuel'], 2, ',', ' ') }} DH
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Voir les statistiques <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="mt-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Activités récentes
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Les dernières activités liées à cette matière
                </p>
            </div>
            <div class="bg-white overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-gray-500 text-center py-4">
                        Aucune activité récente à afficher pour le moment.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
