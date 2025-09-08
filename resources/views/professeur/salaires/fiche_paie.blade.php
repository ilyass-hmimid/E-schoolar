<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Paie - {{ $salaire->reference }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1a365d;
            font-size: 20px;
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0 0;
            color: #4a5568;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
            color: #4a5568;
        }
        .info-value {
            flex: 1;
        }
        .section-title {
            background-color: #2c5282;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            margin: 15px 0 10px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #4a5568;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px solid #cbd5e0;
            padding-top: 10px;
            text-align: center;
            width: 300px;
            margin-left: auto;
            margin-right: 0;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding: 5px 0;
        }
        .page-break {
            page-break-after: always;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            opacity: 0.1;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            color: #1a365d;
        }
    </style>
</head>
<body>
    <!-- Filigrane -->
    <div class="watermark">
        {{ config('app.name', 'E-Schoolar') }}
    </div>

    <!-- En-tête -->
    <div class="header">
        <h1>FICHE DE PAIE</h1>
        <p>Période : {{ $salaire->periode_formatted }} | Référence : {{ $salaire->reference }}</p>
        <p>Établie le {{ $date_emission }}</p>
    </div>

    <!-- Informations du salarié et de l'entreprise -->
    <div style="display: flex; margin-bottom: 20px;">
        <div style="flex: 1; margin-right: 20px;">
            <div class="info-box">
                <div style="font-weight: bold; margin-bottom: 10px; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                    INFORMATIONS DU SALARIÉ
                </div>
                <div class="info-row">
                    <div class="info-label">Nom & Prénom :</div>
                    <div class="info-value">{{ $salaire->professeur->user->nom }} {{ $salaire->professeur->user->prenom }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Matricule :</div>
                    <div class="info-value">{{ $salaire->professeur->matricule ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Poste :</div>
                    <div class="info-value">Professeur {{ $salaire->professeur->matiere->nom ?? 'Matière non spécifiée' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date d'embauche :</div>
                    <div class="info-value">{{ $salaire->professeur->date_embauche ? \Carbon\Carbon::parse($salaire->professeur->date_embauche)->format('d/m/Y') : 'Non spécifiée' }}</div>
                </div>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="info-box">
                <div style="font-weight: bold; margin-bottom: 10px; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                    INFORMATIONS DE L'ÉTABLISSEMENT
                </div>
                <div class="info-row">
                    <div class="info-label">Établissement :</div>
                    <div class="info-value">{{ config('app.name', 'E-Schoolar') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Adresse :</div>
                    <div class="info-value">123 Rue de l'Éducation, Ville, Pays</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Téléphone :</div>
                    <div class="info-value">+212 5 00 00 00 00</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email :</div>
                    <div class="info-value">contact@eschoolar.com</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails de la paie -->
    <div class="section-title">DÉTAIL DE LA RÉMUNÉRATION</div>
    
    <table>
        <thead>
            <tr>
                <th>DÉSIGNATION</th>
                <th style="width: 15%; text-align: right;">NOMBRE</th>
                <th style="width: 15%; text-align: right;">TAUX UNITAIRE</th>
                <th style="width: 20%; text-align: right;">GAINS</th>
                <th style="width: 20%; text-align: right;">RETENUES</th>
            </tr>
        </thead>
        <tbody>
            <!-- Salaire de base -->
            <tr>
                <td>Salaire de base</td>
                <td class="text-right">1</td>
                <td class="text-right">{{ number_format($salaire->salaire_base, 2, ',', ' ') }} DH</td>
                <td class="text-right">{{ number_format($salaire->salaire_base, 2, ',', ' ') }} DH</td>
                <td class="text-right">-</td>
            </tr>
            
            <!-- Heures supplémentaires -->
            @if($salaire->heures_supplementaires > 0)
            <tr>
                <td>Heures supplémentaires ({{ $salaire->heures_supplementaires }}h)</td>
                <td class="text-right">{{ $salaire->heures_supplementaires }}</td>
                <td class="text-right">{{ number_format($salaire->taux_horaire ?? 0, 2, ',', ' ') }} DH</td>
                <td class="text-right">{{ number_format($salaire->montant_heures_supp, 2, ',', ' ') }} DH</td>
                <td class="text-right">-</td>
            </tr>
            @endif
            
            <!-- Primes -->
            @if($salaire->prime_rendement > 0)
            <tr>
                <td>Prime de rendement</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($salaire->prime_rendement, 2, ',', ' ') }} DH</td>
                <td class="text-right">-</td>
            </tr>
            @endif
            
            @if($salaire->prime_anciennete > 0)
            <tr>
                <td>Prime d'ancienneté</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($salaire->prime_anciennete, 2, ',', ' ') }} DH</td>
                <td class="text-right">-</td>
            </tr>
            @endif
            
            @if($salaire->autres_primes > 0)
            <tr>
                <td>Autres primes</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($salaire->autres_primes, 2, ',', ' ') }} DH</td>
                <td class="text-right">-</td>
            </tr>
            @endif
            
            <!-- Cotisations et retenues -->
            @if($salaire->cnss > 0)
            <tr>
                <td>Cotisation CNSS</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-{{ number_format($salaire->cnss, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            
            @if($salaire->amo > 0)
            <tr>
                <td>Assurance Maladie Obligatoire (AMO)</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-{{ number_format($salaire->amo, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            
            @if($salaire->ir > 0)
            <tr>
                <td>Impôt sur le revenu (IR)</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-{{ number_format($salaire->ir, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            
            @if($salaire->autres_retenues > 0)
            <tr>
                <td>Autres retenues</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-{{ number_format($salaire->autres_retenues, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            
            @if($salaire->avance_salaire > 0)
            <tr>
                <td>Avance sur salaire</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-{{ number_format($salaire->avance_salaire, 2, ',', ' ') }} DH</td>
            </tr>
            @endif
            
            <!-- Totaux -->
            <tr style="font-weight: bold; background-color: #f7fafc;">
                <td colspan="3" class="text-right">TOTAL BRUT</td>
                <td class="text-right">{{ number_format($salaire->salaire_brut, 2, ',', ' ') }} DH</td>
                <td class="text-right">-{{ number_format(($salaire->salaire_brut - $salaire->salaire_net), 2, ',', ' ') }} DH</td>
            </tr>
            <tr style="font-weight: bold; background-color: #ebf8ff;">
                <td colspan="4" class="text-right">NET À PAYER</td>
                <td class="text-right">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DH</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Informations de paiement -->
    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
        <div style="width: 48%;" class="info-box">
            <div style="font-weight: bold; margin-bottom: 10px; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                MODALITÉS DE PAIEMENT
            </div>
            <div class="info-row">
                <div class="info-label">Mode de paiement :</div>
                <div class="info-value">{{ ucfirst($salaire->type_paiement ?? 'Virement bancaire') }}</div>
            </div>
            @if($salaire->date_paiement)
            <div class="info-row">
                <div class="info-label">Date de paiement :</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($salaire->date_paiement)->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($salaire->reference_paiement)
            <div class="info-row">
                <div class="info-label">Référence :</div>
                <div class="info-value">{{ $salaire->reference_paiement }}</div>
            </div>
            @endif
        </div>
        <div style="width: 48%;" class="info-box">
            <div style="font-weight: bold; margin-bottom: 10px; color: #2c5282; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                INFORMATIONS BANCAIRES
            </div>
            <div class="info-row">
                <div class="info-label">Titulaire du compte :</div>
                <div class="info-value">{{ $salaire->professeur->user->nom }} {{ $salaire->professeur->user->prenom }}</div>
            </div>
            @if($salaire->professeur->banque)
            <div class="info-row">
                <div class="info-label">Banque :</div>
                <div class="info-value">{{ $salaire->professeur->banque }}</div>
            </div>
            @endif
            @if($salaire->professeur->rib)
            <div class="info-row">
                <div class="info-label">RIB :</div>
                <div class="info-value">{{ $salaire->professeur->rib }}</div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Notes et mentions -->
    @if($salaire->notes)
    <div style="margin-top: 20px;" class="info-box">
        <div style="font-weight: bold; margin-bottom: 5px; color: #2c5282;">
            NOTES
        </div>
        <div style="font-size: 11px; line-height: 1.5;">
            {!! nl2br(e($salaire->notes)) !!}
        </div>
    </div>
    @endif
    
    <!-- Mentions légales -->
    <div style="margin-top: 30px; font-size: 9px; color: #718096; border-top: 1px solid #e2e8f0; padding-top: 10px;">
        <p>Document établi électroniquement, valable sans signature et à conserver pendant 5 ans.</p>
        <p>En cas de différence entre ce document et les données enregistrées dans notre système, ces dernières feront foi.</p>
    </div>
    
    <!-- Signature -->
    <div style="margin-top: 40px; text-align: right;">
        <div style="display: inline-block; text-align: center;">
            <div style="height: 1px; width: 200px; background-color: #cbd5e0; margin: 0 auto 5px;"></div>
            <div style="font-size: 10px; color: #4a5568;">
                Fait à {{ config('app.ville', 'Ville') }}, le {{ $date_emission }}
            </div>
            <div style="margin-top: 20px; font-weight: bold;">
                La Direction
            </div>
        </div>
    </div>
    
    <!-- Pied de page -->
    <div class="footer">
        {{ config('app.name', 'E-Schoolar') }} - {{ config('app.adresse', '123 Rue de l\'Éducation, Ville, Pays') }} - 
        Tél: {{ config('app.telephone', '+212 5 00 00 00 00') }} - Email: {{ config('app.email', 'contact@eschoolar.com') }}
    </div>
</body>
</html>
