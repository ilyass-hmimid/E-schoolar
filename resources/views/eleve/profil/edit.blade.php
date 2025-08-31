@extends('layouts.eleve')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Mon Profil
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Mettez à jour vos informations personnelles et vos préférences.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Il y a {{ $errors->count() }} erreur(s) dans votre formulaire
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Informations personnelles</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Ces informations seront visibles par vos professeurs et l'administration.
                        </p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('eleve.profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Photo de profil -->
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Photo de profil
                                        </label>
                                        <div class="mt-2 flex items-center">
                                            <span class="inline-block h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                                                @if($user->photo)
                                                    <img src="{{ Storage::url($user->photo) }}" alt="Photo de profil" class="h-full w-full object-cover">
                                                @else
                                                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                @endif
                                            </span>
                                            <label class="ml-5">
                                                <span class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer">
                                                    <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Changer
                                                <input type="file" name="photo" class="sr-only">
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Informations de base -->
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $user->prenom) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                                        <input type="text" name="nom" id="nom" value="{{ old('nom', $user->nom) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               autocomplete="email">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $user->telephone) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="date_naissance" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                        <input type="date" name="date_naissance" id="date_naissance" 
                                               value="{{ old('date_naissance', $user->date_naissance ? $user->date_naissance->format('Y-m-d') : '') }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700">Lieu de naissance</label>
                                        <input type="text" name="lieu_naissance" id="lieu_naissance" value="{{ old('lieu_naissance', $user->lieu_naissance) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                                        <select id="genre" name="genre" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="" {{ old('genre', $user->genre) === null ? 'selected' : '' }}>Non spécifié</option>
                                            <option value="M" {{ old('genre', $user->genre) === 'M' ? 'selected' : '' }}>Masculin</option>
                                            <option value="F" {{ old('genre', $user->genre) === 'F' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Adresse -->
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium leading-6 text-gray-900">Adresse</h4>
                                    <p class="mt-1 text-sm text-gray-500">Votre adresse de résidence actuelle.</p>
                                </div>

                                <div class="mt-4 grid grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $user->adresse) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                        <label for="code_postal" class="block text-sm font-medium text-gray-700">Code postal</label>
                                        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $user->code_postal) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                        <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                                        <input type="text" name="ville" id="ville" value="{{ old('ville', $user->ville) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                        <label for="pays" class="block text-sm font-medium text-gray-700">Pays</label>
                                        <input type="text" name="pays" id="pays" value="{{ old('pays', $user->pays ?? 'Maroc') }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Informations complémentaires -->
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium leading-6 text-gray-900">Informations complémentaires</h4>
                                    <p class="mt-1 text-sm text-gray-500">Ces informations nous aident à mieux vous connaître.</p>
                                </div>

                                <div class="mt-4 grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="groupe_sanguin" class="block text-sm font-medium text-gray-700">Groupe sanguin</label>
                                        <select id="groupe_sanguin" name="groupe_sanguin" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="" {{ old('groupe_sanguin', $user->groupe_sanguin) === null ? 'selected' : '' }}>Non spécifié</option>
                                            <option value="A+" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('groupe_sanguin', $user->groupe_sanguin) === 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="numero_cin" class="block text-sm font-medium text-gray-700">Numéro CIN/Passeport</label>
                                        <input type="text" name="numero_cin" id="numero_cin" value="{{ old('numero_cin', $user->numero_cin) }}" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="col-span-6">
                                        <label for="allergies" class="block text-sm font-medium text-gray-700">Allergies connues</label>
                                        <textarea id="allergies" name="allergies" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('allergies', $user->allergies) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500">Listez toutes les allergies connues, séparées par des virgules.</p>
                                    </div>

                                    <div class="col-span-6">
                                        <label for="maladies_chroniques" class="block text-sm font-medium text-gray-700">Maladies chroniques</label>
                                        <textarea id="maladies_chroniques" name="maladies_chroniques" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('maladies_chroniques', $user->maladies_chroniques) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500">Indiquez toutes les maladies chroniques ou conditions médicales à prendre en compte.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section mot de passe -->
        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="mt-10 sm:mt-0">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Changer le mot de passe</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Mettez à jour le mot de passe associé à votre compte.
                        </p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('eleve.profil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                                        <input type="password" name="current_password" id="current_password" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               required>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                        <input type="password" name="password" id="password" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               required>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Mettre à jour le mot de passe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Afficher un aperçu de l'image sélectionnée
    document.querySelector('input[name="photo"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'h-full w-full object-cover';
                
                const container = document.querySelector('.inline-block.h-16.w-16.rounded-full.overflow-hidden.bg-gray-100');
                container.innerHTML = '';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
