<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Absences - Allo Tawjih</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9fafb;
            text-align: center;
        }
        .stat-title {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 20px;
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
            font-size: 12px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .justified {
            color: #059669;
            font-weight: bold;
        }
        .not-justified {
            color: #dc2626;
            font-weight: bold;
        }
        .filters {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .filter-item {
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 Allo Tawjih</div>
        <div class="title">Rapport des Absences</div>
        <div class="subtitle">Centre de Formation</div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <div class="section-title">Filtres appliqués</div>
        @if(!empty($filters['date_debut']))
        <div class="filter-item"><strong>Date de début:</strong> {{ \Carbon\Carbon::parse($filters['date_debut'])->format('d/m/Y') }}</div>
        @endif
        @if(!empty($filters['date_fin']))
        <div class="filter-item"><strong>Date de fin:</strong> {{ \Carbon\Carbon::parse($filters['date_fin'])->format('d/m/Y') }}</div>
        @endif
        @if(!empty($filters['niveau_id']))
        <div class="filter-item"><strong>Niveau:</strong> {{ \App\Models\Niveau::find($filters['niveau_id'])->nom ?? 'N/A' }}</div>
        @endif
        @if(!empty($filters['filiere_id']))
        <div class="filter-item"><strong>Filière:</strong> {{ \App\Models\Filiere::find($filters['filiere_id'])->nom ?? 'N/A' }}</div>
        @endif
        @if(!empty($filters['matiere_id']))
        <div class="filter-item"><strong>Matière:</strong> {{ \App\Models\Matiere::find($filters['matiere_id'])->nom ?? 'N/A' }}</div>
        @endif
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Absences</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Justifiées</div>
            <div class="stat-value justified">{{ $stats['justifiees'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Non Justifiées</div>
            <div class="stat-value not-justified">{{ $stats['non_justifiees'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Taux Justification</div>
            <div class="stat-value">{{ $stats['taux_justification'] }}%</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Détail des Absences</div>
        @if($absences->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Élève</th>
                    <th>Matière</th>
                    <th>Type</th>
                    <th>Justifiée</th>
                    <th>Professeur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absences as $absence)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}</td>
                    <td>{{ $absence->etudiant->name ?? 'N/A' }}</td>
                    <td>{{ $absence->matiere->nom ?? 'N/A' }}</td>
                    <td>
                        @switch($absence->type)
                            @case('absence')
                                Absence
                                @break
                            @case('retard')
                                Retard ({{ $absence->duree_retard ?? 0 }} min)
                                @break
                            @case('sortie_anticipée')
                                Sortie anticipée
                                @break
                            @default
                                {{ $absence->type }}
                        @endswitch
                    </td>
                    <td class="{{ $absence->justifiee ? 'justified' : 'not-justified' }}">
                        {{ $absence->justifiee ? 'Oui' : 'Non' }}
                    </td>
                    <td>{{ $absence->professeur->name ?? $absence->assistant->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Aucune absence trouvée pour les critères sélectionnés.</p>
        @endif
    </div>

    @if($absences->count() > 0)
    <div class="section">
        <div class="section-title">Résumé par Matière</div>
        <table>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Total Absences</th>
                    <th>Justifiées</th>
                    <th>Non Justifiées</th>
                    <th>Taux Justification</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $matieres = $absences->groupBy('matiere_id');
                @endphp
                @foreach($matieres as $matiereId => $absencesMatiere)
                @php
                    $matiere = \App\Models\Matiere::find($matiereId);
                    $total = $absencesMatiere->count();
                    $justifiees = $absencesMatiere->where('justifiee', true)->count();
                    $taux = $total > 0 ? round(($justifiees / $total) * 100, 2) : 0;
                @endphp
                <tr>
                    <td>{{ $matiere->nom ?? 'N/A' }}</td>
                    <td>{{ $total }}</td>
                    <td class="justified">{{ $justifiees }}</td>
                    <td class="not-justified">{{ $total - $justifiees }}</td>
                    <td>{{ $taux }}%</td>
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
