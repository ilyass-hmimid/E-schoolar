<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Salaire - Allo Tawjih</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9fafb;
        }
        .info-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        .salary-details {
            border: 2px solid #7c3aed;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .salary-title {
            font-size: 20px;
            font-weight: bold;
            color: #7c3aed;
            text-align: center;
            margin-bottom: 20px;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .salary-table th,
        .salary-table td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }
        .salary-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .currency {
            font-family: monospace;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }
        .signature-box {
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 Allo Tawjih</div>
        <div class="title">Bulletin de Salaire</div>
        <div class="subtitle">Centre de Formation</div>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-title">Professeur</div>
                <div class="info-value">{{ $salaire->professeur->name }}</div>
            </div>
            <div class="info-card">
                <div class="info-title">Matière</div>
                <div class="info-value">{{ $salaire->matiere->nom }}</div>
            </div>
            <div class="info-card">
                <div class="info-title">Période</div>
                <div class="info-value">{{ $salaire->mois }}/{{ $salaire->annee }}</div>
            </div>
            <div class="info-card">
                <div class="info-title">Statut</div>
                <div class="info-value">{{ ucfirst($salaire->statut) }}</div>
            </div>
        </div>
    </div>

    <div class="salary-details">
        <div class="salary-title">Détail du Salaire</div>
        <table class="salary-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="currency">Montant (MAD)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salaire brut</td>
                    <td class="currency">{{ number_format($salaire->montant_brut, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Retenues</td>
                    <td class="currency">{{ number_format($salaire->montant_brut - $salaire->montant_net, 2, ',', ' ') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Salaire net</td>
                    <td class="currency">{{ number_format($salaire->montant_net, 2, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($salaire->commentaire)
    <div class="info-section">
        <div class="info-title">Commentaires</div>
        <div class="info-value">{{ $salaire->commentaire }}</div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Signature du professeur</p>
        </div>
        <div class="signature-box">
            <p>Signature de l'administrateur</p>
        </div>
    </div>

    <div class="footer">
        <p>Bulletin généré le {{ $generated_at }}</p>
        <p>Allo Tawjih - Centre de Formation</p>
    </div>
</body>
</html>
