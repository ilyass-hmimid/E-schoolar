@extends('admin.layout')

@section('title', 'Confirmer la suppression - ' . $professeur->name)

@section('header')
    <div class="flex items-center">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('admin.professeurs.index') }}" class="text-indigo-600 hover:text-indigo-900">Professeurs</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="{{ route('admin.professeurs.show', $professeur) }}" class="text-indigo-600 hover:text-indigo-900">{{ $professeur->name }}</a>
            <span class="text-gray-400 mx-2">/</span>
            Supprimer
        </h1>
    </div>
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Confirmer la suppression
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Cette action est irréversible. Toutes les données associées à ce professeur seront définitivement supprimées.
            </p>
        </div>
        
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Attention : Suppression irréversible
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>
                                Vous êtes sur le point de supprimer définitivement le professeur <strong>{{ $professeur->name }}</strong>.
                                Cette action supprimera également toutes les données associées, y compris :
                            </p>
                            <ul class="list-disc pl-5 mt-2 space-y-1">
                                <li>Ses affectations aux matières</li>
                                <li>Ses cours planifiés</li>
                                <li>Les évaluations liées</li>
                                <li>Toutes les autres données liées à son compte</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Avant de continuer...
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>
                                Avez-vous envisagé de simplement désactiver le compte professeur au lieu de le supprimer ?
                                Cela permettra de conserver les données historiques tout en empêchant l'accès au compte.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('admin.professeurs.edit', $professeur) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Désactiver le compte au lieu de supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between">
                <a href="{{ route('admin.professeurs.show', $professeur) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler et revenir aux détails
                </a>
                
                <form action="{{ route('admin.professeurs.destroy', $professeur) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center">
                        <div class="mr-4">
                            <label for="confirm_delete" class="flex items-center">
                                <input type="checkbox" id="confirm_delete" name="confirm_delete" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                                <span class="ml-2 text-sm text-gray-700">Je confirme vouloir supprimer définitivement ce professeur</span>
                            </label>
                        </div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer définitivement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
