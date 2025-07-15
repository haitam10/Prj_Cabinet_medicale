<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat Médicale</title>
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
            margin: 0 auto; /* Centers the content horizontally */
            background: white;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        .logo {
            max-height: 220px;
            max-width: 220px;
            object-fit: contain;
        }

        .medical-symbol {
            margin-right: 30px;
        }

        .doctor-info {
            text-align: left;
        }

        .doctor-info h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .doctor-info p {
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

        .doctor-signature-line {
            margin-bottom: 20px;
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
</style>

</head>
<body>
    <div class="header">
        @if($template->logo_file_path)
            <img src="{{ asset('uploads/' . $template->logo_file_path) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $template->nom_cabinet ?? 'Cabinet Médical' }}</h1>
        <div class="cabinet-info">
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

    <div class="document-title">CERTIFICAT MÉDICAL</div>

    <div class="patient-info">
        <strong>Patient:</strong> {{ $patient_cin }} - {{ $patient_nom }}<br>
        <strong>Date:</strong> {{ $date }}
    </div>

    <div class="content">
        <p><strong>Type:</strong> {{ $type }}</p>
        <div style="margin-top: 20px;">
            <strong>Description:</strong>
            <div style="margin-top: 10px; padding: 15px; border: 1px solid #ddd; min-height: 100px;">
                {!! nl2br(e($description)) !!}
            </div>
        </div>
    </div>

    <div class="signature-section">
        <p>Fait le {{ $date }}</p>
        <div style="margin-top: 50px;">
            <strong>Dr. {{ $medecin_nom }}</strong><br>
            <em>Signature et cachet</em>
        </div>
    </div>
</body>
</html>