<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Export des salaires - {{ $periode }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 10px 0;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #777;
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            text-align: right;
            padding: 5px 10px;
        }
        .totals .label {
            font-weight: bold;
            text-align: left;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Allo Tawjih</h1>
        <div class="subtitle">Relevé des salaires - {{ $periode }}</div>
        <div>Date d'édition : {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Professeur</th>
                <th>Matière</th>
                <th>Élèves</th>
                <th>Prix unitaire</th>
                <th>Commission</th>
                <th>Montant Brut</th>
                <th>Montant Net</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaires as $index => $salaire)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $salaire->professeur->name }}</td>
                    <td>{{ $salaire->matiere->nom }}</td>
                    <td class="text-center">{{ $salaire->nombre_eleves }}</td>
                    <td class="text-right">{{ number_format($salaire->prix_unitaire, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($salaire->commission_prof, 2, ',', ' ') }}%</td>
                    <td class="text-right">{{ number_format($salaire->montant_brut, 2, ',', ' ') }} DH</td>
                    <td class="text-right">{{ number_format($salaire->montant_net, 2, ',', ' ') }} DH</td>
                    <td class="text-center">
                        @if($salaire->statut === 'paye')
                            <span style="color: #28a745;">Payé</span>
                        @elseif($salaire->statut === 'annule')
                            <span style="color: #dc3545;">Annulé</span>
                        @else
                            <span>En attente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Total Brut:</td>
                <td>{{ number_format($totalBrut, 2, ',', ' ') }} DH</td>
            </tr>
            <tr>
                <td class="label">Total Commission:</td>
                <td>{{ number_format($totalCommission, 2, ',', ' ') }} DH</td>
            </tr>
            <tr style="font-weight: bold; border-top: 1px solid #000;">
                <td class="label">Total Net:</td>
                <td>{{ number_format($totalNet, 2, ',', ' ') }} DH</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} par {{ auth()->user()->name ?? 'Système' }}
    </div>
</body>
</html>
