@extends('layouts.admin')

@section('title', 'Modifier le Paiement d\'Élève')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.paiements.eleves.index') }}">Paiements des Élèves</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier le Paiement</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier le Paiement</h1>
        <div>
            <a href="{{ route('admin.paiements.eleves.show', $paiement) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i> Voir les détails
            </a>
            <a href="{{ route('admin.paiements.eleves.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('admin.paiements.eleves._form', ['paiement' => $paiement])
        </div>
    </div>
</div>
@endsection
