@extends('layouts.admin')

@section('title', 'Modifier une absence')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier une absence</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.absences.index') }}" class="ml-2 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                            Absences
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-2 text-sm font-medium text-gray-500 md:ml-2">
                            Modifier l'absence
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Modification de l'absence
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Modifiez les informations de cette absence.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.absences.update', $absence) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.absences._form')
                
                <div class="mt-8 border-t border-gray-200 pt-5">
                    <div class="flex justify-between">
                        <a href="{{ route('admin.absences.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Annuler
                        </a>
                        <div class="space-x-3">
                            <a href="{{ route('admin.absences.show', $absence) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Annuler les modifications
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
