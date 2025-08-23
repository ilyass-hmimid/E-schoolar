<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Global - Allo Tawjih</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9fafb;
        }
        .stat-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .currency {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 Allo Tawjih</div>
        <div class="title">Rapport Global</div>
        <div class="subtitle">Centre de Formation</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Élèves</div>
            <div class="stat-value">{{ $stats['total_eleves'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Professeurs</div>
            <div class="stat-value">{{ $stats['total_professeurs'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Paiements ce mois</div>
            <div class="stat-value currency">{{ number_format($stats['paiements_mois'], 0, ',', ' ') }} MAD</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Taux de présence</div>
            <div class="stat-value">{{ $stats['taux_presence_mois'] }}%</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statistiques Détaillées</div>
        <table>
            <thead>
                <tr>
                    <th>Métrique</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Assistants</td>
                    <td>{{ $stats['total_assistants'] }}</td>
                </tr>
                <tr>
                    <td>Paiements cette année</td>
                    <td class="currency">{{ number_format($stats['paiements_annee'], 0, ',', ' ') }} MAD</td>
                </tr>
                <tr>
                    <td>Absences ce mois</td>
                    <td>{{ $stats['absences_mois'] }}</td>
                </tr>
                <tr>
                    <td>Absences justifiées</td>
                    <td>{{ $stats['absences_justifiees_mois'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(!empty($data))
    <div class="section">
        <div class="section-title">Données Supplémentaires</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Rapport généré le {{ $generated_at }}</p>
        <p>Allo Tawjih - Centre de Formation</p>
    </div>
</body>
</html>
