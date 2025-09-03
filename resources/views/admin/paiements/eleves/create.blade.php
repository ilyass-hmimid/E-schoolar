@extends('layouts.admin')

@section('title', 'Nouveau Paiement d\'Élève')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.eleves.index') }}">Paiements des Élèves</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau Paiement</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouveau Paiement d'Élève</h1>
        <a href="{{ route('admin.paiements.eleves.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour à la liste
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            @include('admin.paiements.eleves._form', ['paiement' => new App\Models\PaiementEleve()])
        </div>
    </div>
</div>
@endsection
