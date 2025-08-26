<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .date { font-size: 12px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="date">Généré le {{ $date }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Matière/Pack</th>
                <th class="text-right">Montant</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Mode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $paiement)
            <tr>
                <td>{{ $paiement->etudiant->name }}</td>
                <td>{{ $paiement->matiere?->nom ?? ($paiement->pack?->nom ?? 'N/A') }}</td>
                <td class="text-right">{{ number_format($paiement->montant, 2) }} DH</td>
                <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                <td>{{ ucfirst($paiement->statut) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total :</strong></td>
                <td class="text-right"><strong>{{ number_format($paiements->sum('montant'), 2) }} DH</strong></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Généré par Allo Tawjih - {{ config('app.name') }}
    </div>
</body>
</html>
