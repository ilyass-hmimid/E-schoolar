@extends('layouts.app')

@section('title', 'Détails de la classe : ' . $classe->nom)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $classe->nom }}
                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-200">
                        {{ $classe->niveau }}
                    </span>
                </h1>
                @if($classe->professeurPrincipal)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Professeur principal: {{ $classe->professeurPrincipal->name }}
                </p>
                @endif
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </a>
                <a href="{{ route('admin.classes.edit', $classe) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Informations de la classe
                </h3>
                @if($classe->description)
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        {{ $classe->description }}
                    </p>
                @endif
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <dl>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">
                            Nom de la classe
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                            {{ $classe->nom }}
                        </dd>
                    </div>
                    <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">
                            Niveau
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                            {{ $classe->niveau }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">
                            Effectif
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                            {{ $classe->eleves_count ?? 0 }} élève(s)
                        </dd>
                    </div>
                    <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">
                            Professeur principal
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                            @if($classe->professeurPrincipal)
                                {{ $classe->professeurPrincipal->name }}
                            @else
                                <span class="text-gray-400">Non défini</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Liste des élèves de la classe -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Liste des élèves
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tous les élèves inscrits dans cette classe
                </p>
            </div>
            
            @if($classe->eleves->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nom
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Prénom
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Téléphone
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($classe->eleves as $eleve)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $eleve->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $eleve->prenom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $eleve->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $eleve->telephone ?? 'Non défini' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.eleves.show', $eleve) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucun élève n'est inscrit dans cette classe pour le moment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
