@extends('layouts.admin')

@section('title', 'Modifier le Paiement Enseignant')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.professeurs.index') }}">Paiements des Enseignants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier le Paiement #{{ $paiement->id }}</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier le Paiement</h1>
        
        <div class="btn-group">
            <a href="{{ route('admin.paiements.professeurs.show', $paiement) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Voir
            </a>
            
            <form action="{{ route('admin.paiements.professeurs.destroy', $paiement) }}" method="POST" class="d-inline" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.paiements.professeurs.update', $paiement) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.paiements.professeurs._form')
    </form>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
@endsection
