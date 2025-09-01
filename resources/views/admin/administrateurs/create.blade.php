@extends('layouts.admin')

@section('title', 'Ajouter un administrateur')

@section('content')
    <div class="bg-dark-900 rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Ajouter un administrateur</h2>
            <a href="{{ route('admin.administrateurs.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-900/50 border-l-4 border-red-500 text-red-100 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">Erreur</p>
                        <ul class="list-disc list-inside text-sm mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.administrateurs.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adresse email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-dark-800">
                <a href="{{ route('admin.administrateurs.index') }}" class="btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
