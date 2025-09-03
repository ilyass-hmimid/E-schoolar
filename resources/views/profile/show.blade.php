<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <img class="h-20 w-20 rounded-full object-cover" 
                                 src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/default-avatar.png') }}" 
                                 alt="{{ $user->name }}">
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $user->roles->first() ? ucfirst($user->roles->first()->name) : 'Aucun rôle' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Nom complet</h4>
                            <p class="text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        
                        @if($user->telephone)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Téléphone</h4>
                            <p class="text-gray-900">{{ $user->telephone }}</p>
                        </div>
                        @endif
                        
                        @if($user->adresse)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Adresse</h4>
                            <p class="text-gray-900">{{ $user->adresse }}</p>
                        </div>
                        @endif
                        
                        @if($user->date_naissance)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Date de naissance</h4>
                            <p class="text-gray-900">{{ $user->date_naissance->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Compte créé le</h4>
                            <p class="text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Modifier le profil') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
