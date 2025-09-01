<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificatif d'absence - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
        }
        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8fafc;
        }
        .info-box h2 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 16px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #4a5568;
        }
        .info-value {
            flex: 1;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
            margin-top: 40px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
        @endif
        <h1>JUSTIFICATIF D'ABSENCE</h1>
        <p>Établissement Scolaire {{ config('app.name') }}</p>
        <p>Date d'émission: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <h2>Informations de l'élève</h2>
        <div class="info-row">
            <div class="info-label">Nom complet:</div>
            <div class="info-value">{{ $absence->eleve->prenom }} {{ $absence->eleve->nom }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Classe:</div>
            <div class="info-value">{{ $absence->eleve->classe->nom_complet ?? 'Non défini' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de naissance:</div>
            <div class="info-value">{{ $absence->eleve->date_naissance ? $absence->eleve->date_naissance->format('d/m/Y') : 'Non défini' }}</div>
        </div>
    </div>

    <div class="info-box">
        <h2>Détails de l'absence</h2>
        <div class="info-row">
            <div class="info-label">Date de l'absence:</div>
            <div class="info-value">{{ $absence->date_absence->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Type d'absence:</div>
            <div class="info-value">{{ ucfirst($absence->type_absence) }}</div>
        </div>
        @if($absence->duree_absence)
        <div class="info-row">
            <div class="info-label">Durée:</div>
            <div class="info-value">{{ $absence->duree_absence }} minutes</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Matière:</div>
            <div class="info-value">{{ $absence->cours->matiere->nom ?? 'Non spécifié' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Professeur:</div>
            <div class="info-value">{{ $absence->cours->professeur->user->name ?? 'Non affecté' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut:</div>
            <div class="info-value">
                @if($absence->justifiee)
                    <span style="color: #38a169; font-weight: bold;">Justifiée</span>
                @else
                    <span style="color: #e53e3e; font-weight: bold;">Non justifiée</span>
                @endif
            </div>
        </div>
        @if($absence->motif)
        <div class="info-row">
            <div class="info-label">Motif:</div>
            <div class="info-value">{{ $absence->motif }}</div>
        </div>
        @endif
        @if($absence->commentaire)
        <div class="info-row">
            <div class="info-label">Commentaire:</div>
            <div class="info-value">{{ $absence->commentaire }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Saisie par:</div>
            <div class="info-value">{{ $absence->utilisateurCreation->name ?? 'Système' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de saisie:</div>
            <div class="info-value">{{ $absence->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($absence->chemin_justificatif && Storage::disk('public')->exists($absence->chemin_justificatif))
    <div class="info-box">
        <h2>Justificatif joint</h2>
        <p>Un justificatif a été fourni pour cette absence.</p>
        <p>Type de fichier: {{ strtoupper(pathinfo($absence->chemin_justificatif, PATHINFO_EXTENSION)) }}</p>
    </div>
    @endif

    <div class="signature">
        <p>Le {{ now()->format('d/m/Y') }}</p>
        <div class="signature-line"></div>
        <p>Signature et cachet de l'établissement</p>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }} - {{ config('app.url') }}</p>
        <p>Ce document a été généré électroniquement et ne nécessite pas de signature manuscrite.</p>
    </div>
</body>
</html>
