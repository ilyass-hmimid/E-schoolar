@extends('layouts.admin')

@section('title', 'Gestion des administrateurs')

@section('content')
    <div class="bg-dark-900 rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Liste des administrateurs</h2>
            <a href="{{ route('admin.administrateurs.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nouvel administrateur
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-900/50 border-l-4 border-green-500 text-green-100 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">Succès</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-900/50 border-l-4 border-red-500 text-red-100 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">Erreur</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-700">
                <thead class="bg-dark-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Nom
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Dernière connexion
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-dark-900 divide-y divide-dark-800">
                    @forelse($administrateurs as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold mr-3">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $admin->name }}</div>
                                        <div class="text-xs text-gray-400">
                                            @foreach($admin->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                    $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                                    'bg-gray-100 text-gray-800' 
                                                }}">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $admin->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Jamais' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.administrateurs.show', $admin) }}" class="text-blue-400 hover:text-blue-300 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.administrateurs.edit', $admin) }}" class="text-yellow-400 hover:text-yellow-300 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($admin->id !== auth()->id())
                                        <form action="{{ route('admin.administrateurs.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-400">
                                Aucun administrateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($administrateurs->hasPages())
            <div class="mt-4">
                {{ $administrateurs->links() }}
            </div>
        @endif
    </div>
@endsection
