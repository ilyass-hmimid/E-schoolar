@extends('layouts.admin')

@section('title', 'Modifier un administrateur')

@section('content')
    <div class="bg-dark-900 rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Modifier l'administrateur</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.administrateurs.show', $administrateur) }}" class="btn-secondary">
                    <i class="fas fa-eye mr-2"></i>Voir les détails
                </a>
                <a href="{{ route('admin.administrateurs.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
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

        <form action="{{ route('admin.administrateurs.update', $administrateur) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $administrateur->name) }}" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adresse email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $administrateur->email) }}" required
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" name="password" id="password"
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full bg-dark-800 border border-dark-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-dark-800">
                @if($administrateur->id !== auth()->id())
                    <button type="button" onclick="confirmDelete()" class="btn-danger">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                @else
                    <div></div>
                @endif
                
                <div class="flex space-x-3">
                    <a href="{{ route('admin.administrateurs.show', $administrateur) }}" class="btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
        
        @if($administrateur->id !== auth()->id())
            <form id="deleteForm" action="{{ route('admin.administrateurs.destroy', $administrateur) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ? Cette action est irréversible.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endpush
