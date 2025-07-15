{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat Médical</title>
   
</head>

@php
    // Safely extract data from session or passed variables
    $printData = session('print_certificat', []);
    
    // Create template object with safe property access
    $templateData = $printData['template'] ?? [];
    $template = (object) array_merge([
        'nom_cabinet' => 'Cabinet Médical',
        'addr_cabinet' => '123 Rue Médicale, Casablanca',
        'tel_cabinet' => '0522-123456',
        'desc_cabinet' => '',
        'logo_file_path' => null,
    ], $templateData);
    
    // Extract other variables with defaults
    $medecin_nom = $printData['medecin_nom'] ?? $medecin_nom ?? '';
    $patient_cin = $printData['patient_cin'] ?? $patient_cin ?? '';
    $patient_nom = $printData['patient_nom'] ?? $patient_nom ?? '';
    $type = $printData['type'] ?? $type ?? '';
    $contenu = $printData['contenu'] ?? $contenu ?? $printData['description'] ?? '';
    $date = $printData['date'] ?? $date ?? now()->format('Y-m-d');
@endphp

<body>
    <div class="header">
        @if(!empty($template->logo_file_path))
            <img src="{{ asset('uploads/' . $template->logo_file_path) }}" alt="Logo" class="logo">
        @else
            <!-- Default medical symbol if no logo -->
            <div class="medical-symbol">
                <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 10C30 10 15 25 15 45C15 65 30 80 50 80C70 80 85 65 85 45C85 25 70 10 50 10Z" stroke="black" stroke-width="2" fill="none"/>
                    <path d="M35 35L65 65M65 35L35 65" stroke="black" stroke-width="2"/>
                    <path d="M50 20L50 70M30 50L70 50" stroke="black" stroke-width="3"/>
                </svg>
            </div>
        @endif
        
        <div class="doctor-info">
            <h1>{{ $template->nom_cabinet }}</h1>
            <p><strong>DR. {{ strtoupper($medecin_nom) }}</strong></p>
            <p><strong>MÉDECIN - CHIRURGIEN</strong></p>
            @if(!empty($template->desc_cabinet))
                <p>{{ $template->desc_cabinet }}</p>
            @else
                <p>MÉDECINE GÉNÉRALE, PÉDIATRIE, MALADIES RESPIRATOIRES ET CUTANÉES</p>
                <p>CHIRURGIE VÉNÉRIENNE, MAJEURE ET MINEURE</p>
            @endif
            <p>Adresse: {{ $template->addr_cabinet }} | Tél: {{ $template->tel_cabinet }}</p>
        </div>
    </div>

    <!-- Date -->
    <div class="date-section">
        BONNE FOI, {{ strtoupper(\Carbon\Carbon::parse($date)->translatedFormat('d F Y')) }}
    </div>

    <!-- Title -->
    <div class="document-title">CERTIFICAT MÉDICAL</div>

    <!-- Doctor Info -->
    <div class="doctor-signature-line">
        <p><strong>YO, Dr. {{ $medecin_nom }}</strong></p>
        <p><strong>MÉDECIN CHIRURGIEN</strong></p>
    </div>

    <!-- Patient Info -->
    <div class="patient-info">
        <strong>Patient:</strong> {{ $patient_cin }} - {{ $patient_nom }}<br>
        <strong>Type:</strong> {{ $type }}<br>
        <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
    </div>

    <!-- Certificate Content -->
    <div class="content">
        <strong>Certificat:</strong>
        <div style="margin-top: 10px; line-height: 1.8;">
            {!! nl2br(e($contenu)) !!}
        </div>
    </div>

    <!-- Closing -->
    <div style="text-align: center; margin: 30px 0; font-style: italic;">
        <p>Je vous prie d'agréer, Monsieur le Président, l'expression de mes</p>
        <p>sentiments distingués,</p>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <p><strong>DR. {{ strtoupper($medecin_nom) }}</strong></p>
        <p><strong>Chirurgien</strong></p>
    </div>
</body>
</html> --}}