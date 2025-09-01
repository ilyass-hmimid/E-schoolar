@extends('layouts.admin')

@section('title', 'Détails de l\'administrateur')

@section('content')
    <div class="bg-dark-900 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-dark-800 bg-dark-800">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Détails de l'administrateur</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.administrateurs.edit', $administrateur) }}" class="btn-secondary">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('admin.administrateurs.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="bg-dark-800 rounded-lg p-6 text-center">
                        <div class="h-32 w-32 mx-auto rounded-full bg-primary/20 flex items-center justify-center text-4xl font-bold text-primary mb-4">
                            {{ strtoupper(substr($administrateur->name, 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-bold text-white">{{ $administrateur->name }}</h3>
                        <p class="text-gray-400">{{ $administrateur->email }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-dark-700">
                            <p class="text-sm text-gray-400">Dernière connexion :</p>
                            <p class="text-white font-medium">
                                {{ $administrateur->last_login_at ? $administrateur->last_login_at->diffForHumans() : 'Jamais' }}
                            </p>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-dark-700">
                            <p class="text-sm text-gray-400">Compte créé le :</p>
                            <p class="text-white font-medium">
                                {{ $administrateur->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-dark-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4 pb-2 border-b border-dark-700">Informations générales</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-400">Nom complet</p>
                                <p class="text-white font-medium">{{ $administrateur->name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-400">Adresse email</p>
                                <p class="text-white font-medium">{{ $administrateur->email }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-400">Rôles</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($administrateur->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                            $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                            'bg-gray-100 text-gray-800' 
                                        }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-400">Dernière mise à jour</p>
                                <p class="text-white font-medium">
                                    {{ $administrateur->updated_at->format('d/m/Y à H:i') }}
                                    <span class="text-gray-500 text-sm">({{ $administrateur->updated_at->diffForHumans() }})</span>
                                </p>
                            </div>
                        </div>
                        
                        @if($administrateur->id === auth()->id())
                            <div class="mt-6 pt-4 border-t border-dark-700">
                                <a href="{{ route('profile.show') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-user-edit mr-2"></i>
                                    Modifier mon profil
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
