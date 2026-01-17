<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance Médicale</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #fff;
            color: #000;
        }

        .container {
            width: 21cm; /* A4 width */
            height: 29.7cm; /* A4 height */
            margin: 0 auto;
            padding: 2cm; /* Adjust padding to match the image's margins */
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .doctor-info, .cabinet-info {
            font-size: 14px;
            line-height: 1.6;
        }

        .doctor-info {
            text-align: left;
        }

        .cabinet-info {
            text-align: right;
        }

        .doctor-info strong, .cabinet-info strong {
            font-size: 16px;
        }

        .caduceus-icon {
            width: 70px;
            height: 70px;
            opacity: 0.7; /* Adjust opacity to match the faded look in the image */
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 50px; /* Adjust vertical position */
        }
        
        .caduceus-large {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px; /* Larger size */
            height: 300px;
            opacity: 0.1; /* Very faint */
            z-index: 0; /* Behind other content */
        }


        .divider {
            border-bottom: 1px solid #000;
            margin: 20px 0;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 40px 0;
            text-decoration: underline;
        }

        .date-location {
            text-align: right;
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .doctor-name-signature {
            margin-top: 30px;
            font-size: 14px;
        }

        .content-area {
            flex-grow: 1;
            position: relative; /* For positioning the large caduceus */
            z-index: 1; /* To keep content above the background caduceus */
        }

        .footer {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: auto; /* Pushes the footer to the bottom */
            text-align: center;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="doctor-info">
                <strong>Dr. Nom & Prénom</strong><br>
                Médecin Générale<br>
                Tél : 0522 000 000<br>
                Adressemail@gmail.com
            </div>
            <img src="Prj_Cabinet_medicale/public/uploads/stEGnvZqsBzaB3HmR7oLucCIdTlu3N1WZZmle0SK.png" alt="Caduceus" class="caduceus-icon">
            <div class="cabinet-info">
                <strong>Cabinet Nom</strong><br>
                Adresse :Votre Adresse<br>
                ici<br>
                Tél : 0522 000 000
            </div>
        </div>

        <div class="divider"></div>

        <div class="title">
            Ordonnance Médicale
        </div>

        <div class="date-location">
            Fait à : ................................ Le ...... / ...... / ..........
        </div>

        <div class="doctor-name-signature">
            Nom & Prénom : ......................................................
        </div>

        <div class="content-area">
            <img src="http://localhost/Prj_Cabinet_medicale/storage/app/public/uploads/HfJbohux6MBvPDLA6K1gVBtV8mUiofUSrhOLPo9" alt="Caduceus Large" class="caduceus-large">
            </div>

        <div class="footer">
            Adresse : 123, Avenue adresse , ville - Tél : 0522 000 000 - 0522 000 000<br>
            Adressemail@gmail.com
        </div>
    </div>
</body>
</html>