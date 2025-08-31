<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de paiement #{{ $paiement->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 20px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .receipt-info {
            margin-bottom: 20px;
            overflow: hidden;
        }
        .receipt-info div {
            float: left;
            width: 50%;
        }
        .receipt-info .right {
            text-align: right;
        }
        .details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details th, .details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details th {
            background-color: #f2f2f2;
        }
        .total {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists($logo))
            <img src="{{ $logo }}" alt="Logo">
        @endif
        <h1>Reçu de Paiement</h1>
        <p>Référence: {{ $reference }}</p>
        <p>Date d'émission: {{ $date_emission }}</p>
    </div>

    <div class="receipt-info">
        <div>
            <strong>Établissement:</strong><br>
            Allo Tawjih<br>
            Adresse de l'établissement<br>
            Tél: +212 5XX XXX XXX<br>
            Email: contact@allotawjih.ma
        </div>
        <div class="right">
            <strong>Reçu pour:</strong><br>
            {{ $paiement->etudiant->name }}<br>
            {{ $paiement->etudiant->email }}<br>
            @if($paiement->etudiant->telephone)
                {{ $paiement->etudiant->telephone }}<br>
            @endif
        </div>
    </div>

    <table class="details">
        <thead>
            <tr>
                <th>Description</th>
                <th>Montant (MAD)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($paiement->pack)
                        Pack: {{ $paiement->pack->nom }}
                    @elseif($paiement->matiere)
                        Cours: {{ $paiement->matiere->nom }}
                    @else
                        Paiement divers
                    @endif
                    
                    @if($paiement->tarif && $paiement->tarif->nom)
                        <br><small>Tarif: {{ $paiement->tarif->nom }}</small>
                    @endif
                    
                    @if($paiement->mois_periode)
                        <br><small>Période: {{ \Carbon\Carbon::createFromFormat('Y-m', $paiement->mois_periode)->format('F Y') }}</small>
                    @endif
                </td>
                <td>{{ number_format($paiement->montant, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td><strong>Total TTC</strong></td>
                <td><strong>{{ number_format($paiement->montant, 2, ',', ' ') }} MAD</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="payment-info">
        <p><strong>Mode de paiement:</strong> {{ ucfirst($paiement->mode_paiement) }}</p>
        @if($paiement->reference_paiement)
            <p><strong>Référence:</strong> {{ $paiement->reference_paiement }}</p>
        @endif
        <p><strong>Date de paiement:</strong> {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</p>
        <p><strong>Statut:</strong> 
            @if($paiement->statut === 'valide')
                <span style="color: green;">Payé</span>
            @elseif($paiement->statut === 'en_attente')
                <span style="color: orange;">En attente</span>
            @else
                <span style="color: red;">Annulé</span>
            @endif
        </p>
        
        @if($paiement->commentaires)
            <div style="margin-top: 10px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #3498db;">
                <strong>Commentaires:</strong><br>
                {!! nl2br(e($paiement->commentaires)) !!}
            </div>
        @endif
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p>Signature & cachet</p>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
        <p>Ce document fait foi de paiement. Conservez-le précieusement.</p>
        <p>Pour toute réclamation, veuillez présenter ce reçu.</p>
    </div>
</body>
</html>
