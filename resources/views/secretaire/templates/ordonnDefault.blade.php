<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance Médicale</title>
    <style>
        @page { size: A4; margin: 5cm; }

        html, body {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            padding: 20px;
            width: 700px;
            margin: 0 auto;
            background: white;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .logo {
            max-height: 220px;
            max-width: 220px;
            object-fit: contain;
        }

        .cabinet-info {
            text-align: left;
        }

        .cabinet-info h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .cabinet-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .date-section {
            text-align: right;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .document-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }

        .prescription-box {
            border: 2px solid #000;
            padding: 20px;
            margin: 20px 0;
            min-height: 200px;
            background: #fff;
        }

        .prescription-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .instructions-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
        }

        .signature-section {
            text-align: center;
            margin-top: 50px;
        }

        .signature-line {
            border-bottom: 2px solid #000;
            width: 200px;
            margin: 0 auto 10px;
        }

        .closing-remark {
            text-align: center;
            margin: 30px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($template->logo_file_path)
            <img src="{{ asset('uploads/' . $template->logo_file_path) }}" alt="Logo" class="logo">
        @endif
        <div class="cabinet-info">
            <h1>{{ $template->nom_cabinet ?? 'Cabinet Médical' }}</h1>
            @if($template->addr_cabinet)
                <p>{{ $template->addr_cabinet }}</p>
            @endif
            @if($template->tel_cabinet)
                <p>Tél: {{ $template->tel_cabinet }}</p>
            @endif
            @if($template->desc_cabinet)
                <p>{{ $template->desc_cabinet }}</p>
            @endif
        </div>
    </div>

    <!-- Date -->
    <div class="date-section">
        Fait à bonne foi,
        {{ \Carbon\Carbon::createFromFormat('d/m/Y', $date)->locale('fr')->translatedFormat('d F Y') }}
    </div>

    <!-- Title -->
    <div class="document-title">
        ORDONNANCE MÉDICALE
    </div>

    <!-- Patient Info -->
    <div style="margin-bottom: 20px;">
        <p><strong>Patient:</strong> {{ $patient_cin ?? '' }} - {{ $patient_nom ?? '' }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
    </div>

    <!-- Prescription Box -->
    <div class="prescription-box">
        <div class="prescription-title">Prescription:</div>
        <div style="white-space: pre-line; font-size: 16px; line-height: 1.8;">
            {{ $medicaments }}
        </div>
    </div>

    <!-- Instructions -->
    <div class="instructions-section">
        <strong>Instructions:</strong>
        <div style="margin-top: 10px;">
            {!! nl2br(e($instructions)) !!}
        </div>
        <div style="margin-top: 15px;">
            <strong>Durée du traitement:</strong> {{ $duree }} jours
        </div>
    </div>

    <!-- Closing -->
    <div class="closing-remark">
        <p>Je vous prie d'agréer Madame/Monsieur, l'expression de mes</p>
        <p>sentiments distingués,</p>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <p>Fait le {{ $date }}</p>
        <div style="margin-top: 50px;">
            <strong>Dr. {{ $medecin_nom }}</strong><br>
            <em>Signature et cachet</em>
        </div>
    </div>
</body>
</html>
