@extends('layouts.admin')

@section('title', 'Détails de la classe')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Détails de la classe</h1>
            <p class="text-gray-400">Informations complètes sur la classe {{ $classe->nom }}</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('admin.classes.edit', $classe->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
            <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-dark-700 border border-dark-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dark-600 focus:bg-dark-700 active:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $classe->nom }}</h2>
                            <p class="text-gray-400">Code: {{ $classe->code }} | {{ $classe->annee_scolaire }}</p>
                        </div>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $classe->niveau->nom }}
                        </span>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Effectif</h3>
                            <div class="flex items-center">
                                <div class="h-12 w-12 rounded-full bg-primary-500 bg-opacity-20 flex items-center justify-center text-primary-400">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-2xl font-bold text-white">{{ $classe->eleves_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-400">élèves inscrits</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Professeur principal</h3>
                            @if($classe->professeurPrincipal)
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                                        {{ substr($classe->professeurPrincipal->user->prenom, 0, 1) }}{{ substr($classe->professeurPrincipal->user->nom, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white">{{ $classe->professeurPrincipal->user->prenom }} {{ $classe->professeurPrincipal->user->nom }}</p>
                                        <p class="text-xs text-gray-400">{{ $classe->professeurPrincipal->specialite ?? 'Aucune spécialité' }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-400 italic">Aucun professeur principal défini</div>
                            @endif
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Salle</h3>
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-lg bg-dark-700 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $classe->salle ?? 'Non spécifiée' }}</p>
                                    <p class="text-xs text-gray-400">Salle de classe</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Année scolaire</h3>
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-lg bg-dark-700 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $classe->annee_scolaire }}</p>
                                    <p class="text-xs text-gray-400">Session</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($classe->description)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                            <div class="prose prose-invert max-w-none bg-dark-700 rounded-lg p-4">
                                {{ $classe->description }}
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-dark-700 px-6 py-4 bg-dark-750">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-4">
                            <span class="inline-flex items-center text-sm text-gray-400">
                                <i class="fas fa-calendar-plus mr-1.5"></i>
                                Créée le {{ $classe->created_at->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center text-sm text-gray-400">
                                <i class="fas fa-edit mr-1.5"></i>
                                Modifiée le {{ $classe->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classe->est_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $classe->est_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des élèves -->
            <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-white">Liste des élèves</h3>
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-user-plus mr-1.5"></i> Ajouter des élèves
                    </button>
                </div>
                <div class="p-6">
                    @if($classe->eleves->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-dark-700">
                                <thead class="bg-dark-750">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            Nom & Prénom
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            Téléphone
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-dark-800 divide-y divide-dark-700">
                                    @foreach($classe->eleves as $eleve)
                                        <tr class="hover:bg-dark-750 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-bold">
                                                        {{ substr($eleve->user->prenom, 0, 1) }}{{ substr($eleve->user->nom, 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-white">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</div>
                                                        <div class="text-xs text-gray-400">INE: {{ $eleve->ine ?? 'Non défini' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $eleve->user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $eleve->telephone ?? 'Non défini' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('admin.eleves.show', $eleve->id) }}" class="text-blue-400 hover:text-blue-300" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="text-red-400 hover:text-red-300" title="Retirer de la classe" onclick="confirm('Êtes-vous sûr de vouloir retirer cet élève de la classe ?') && document.getElementById('remove-student-{{ $eleve->id }}').submit()">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                    <form id="remove-student-{{ $eleve->id }}" action="{{ route('admin.classes.remove-student', ['classe' => $classe->id, 'eleve' => $eleve->id]) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users-slash text-4xl text-gray-500 mb-4"></i>
                            <p class="text-gray-400">Aucun élève n'est inscrit dans cette classe pour le moment.</p>
                            <button class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-user-plus mr-2"></i> Ajouter des élèves
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Emploi du temps -->
            <div class="mt-6 bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-white">Emploi du temps</h3>
                    <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-plus mr-1.5"></i> Ajouter un cours
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-dark-700 rounded-lg p-6 text-center">
                        <i class="fas fa-calendar-alt text-4xl text-gray-500 mb-4"></i>
                        <h4 class="text-gray-300 font-medium">Aucun emploi du temps défini</h4>
                        <p class="text-sm text-gray-500 mt-1">Créez un emploi du temps pour cette classe</p>
                        <button class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-2"></i> Créer l'emploi du temps
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statut et actions -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">Statut de la classe</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classe->est_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $classe->est_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Année scolaire</span>
                            <span class="text-sm text-white">{{ $classe->annee_scolaire }}</span>
                        </div>
                        <div class="pt-4 mt-4 border-t border-dark-700">
                            <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-calendar-plus mr-2"></i> Planifier un cours
                            </button>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-300 bg-dark-700 hover:bg-dark-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-file-export mr-2"></i> Exporter la liste
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enseignants -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Enseignants</h3>
                </div>
                <div class="p-4">
                    @if($classe->enseignants->count() > 0)
                        <ul class="space-y-3">
                            @foreach($classe->enseignants->take(5) as $enseignant)
                                <li class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($enseignant->user->prenom, 0, 1) }}{{ substr($enseignant->user->nom, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</p>
                                        <p class="text-xs text-gray-400">{{ $enseignant->matiere->nom ?? 'Aucune matière' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if($classe->enseignants->count() > 5)
                            <div class="mt-3 text-center">
                                <a href="#" class="text-sm text-primary-500 hover:text-primary-400">
                                    Voir les {{ $classe->enseignants->count() - 5 }} autres
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chalkboard-teacher text-2xl text-gray-500 mb-2"></i>
                            <p class="text-sm text-gray-400">Aucun enseignant affecté</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Documents partagés -->
            <div class="bg-dark-800 rounded-xl shadow-lg overflow-hidden border border-dark-700">
                <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Documents partagés</h3>
                    <button class="text-primary-500 hover:text-primary-400">
                        <i class="fas fa-upload"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div class="text-center py-4">
                        <i class="fas fa-file-upload text-3xl text-gray-500 mb-2"></i>
                        <p class="text-sm text-gray-400">Aucun document partagé</p>
                        <button class="mt-2 text-sm text-primary-500 hover:text-primary-400">
                            <i class="fas fa-plus mr-1"></i> Ajouter un document
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scripts spécifiques à la page de détails de la classe
    });
</script>
@endpush

@endsection
