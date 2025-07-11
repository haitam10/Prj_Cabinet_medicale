<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Documents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "cordes-blue": "#1e40af",
                        "cordes-dark": "#1e293b",
                        "cordes-light": "#f8fafc",
                        "cordes-accent": "#3b82f6",
                    },
                },
            },
        };
    </script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printContent, #printContent * {
                visibility: visible;
            }
            #printContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-64 bg-cordes-dark shadow-xl z-50">
        <div class="flex items-center justify-center h-16 bg-cordes-blue">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-cube text-cordes-blue text-lg"></i>
                </div>
                <span class="text-white text-xl font-bold">Espace Secrétaire</span>
            </div>
        </div>
        <nav class="mt-8 px-4">
            <div class="space-y-2">
                <a href="{{ route('secretaire.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-home mr-3 text-cordes-accent group-hover:text-white"></i>
                    Dashboard
                </a>
                <a href="{{ route('secretaire.rendezvous') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-users mr-3 text-gray-400 group-hover:text-white"></i>
                    Rendez-vous
                </a>
                <a href="{{ route('secretaire.patients') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-chart-bar mr-3 text-gray-400 group-hover:text-white"></i>
                    Patients
                </a>
                <a href="{{ route('secretaire.factures') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-shopping-cart mr-3 text-gray-400 group-hover:text-white"></i>
                    Factures
                </a>
                <a href="{{ route('secretaire.docs') }}" class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Documents
                </a>
            </div>
        </nav>
        <div class="absolute bottom-4 left-4 right-4">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/17003/17003310.png" alt="Secrétaire"
                        class="w-10 h-10 rounded-full" />
                    <div>
                        <p class="text-white text-sm font-medium">Secrétaire</p>
                        <p class="text-gray-400 text-xs">Connecté</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENU PRINCIPAL -->
    <div class="ml-64">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Documents</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des documents médicaux générés</p>
                </div>
            </div>
        </header>

        <main class="p-6">
            @if (session('success'))
                <div id="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-opacity duration-500">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div id="errorMessage" class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher par patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="typeFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les types</option>
                            <option value="ordonnance">Ordonnance</option>
                            <option value="certificat">Certificat</option>
                            <option value="remarque">Remarque</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <i class="fas fa-user-md absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="medecinFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les médecins</option>
                            <option value="Hamza">Dr. Hamza</option>
                            <option value="Reda">Dr. Reda</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-file-medical mr-2"></i>
                        <span id="documentCount">
                            {{ count($documents['ordonnances']) + count($documents['certificats']) + count($documents['remarques']) }} 
                            Document{{ (count($documents['ordonnances']) + count($documents['certificats']) + count($documents['remarques'])) > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded-xl">
               <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PATIENT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOCUMENT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MÉDECIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AJOUTÉ LE</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $allDocuments = [];
                            
                            // Add ordonnances
                            foreach($documents['ordonnances'] as $ordonnance) {
                                $allDocuments[] = $ordonnance;
                            }
                            
                            // Add certificats
                            foreach($documents['certificats'] as $certificat) {
                                $allDocuments[] = $certificat;
                            }
                            
                            // Add remarques
                            foreach($documents['remarques'] as $remarque) {
                                $allDocuments[] = $remarque;
                            }
                            
                            // Sort by date (newest first)
                            usort($allDocuments, function($a, $b) {
                                return strtotime($b['date']) - strtotime($a['date']);
                            });
                        @endphp

                        @forelse ($allDocuments as $doc)
                            <tr class="hover:bg-gray-50 transition-colors" data-type="{{ $doc['type'] }}" data-medecin="{{ $doc['medecin_nom'] }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_cin'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_nom'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold 
                                        @if($doc['type'] === 'ordonnance') bg-blue-100 text-blue-800
                                        @elseif($doc['type'] === 'certificat') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        {{ ucfirst($doc['type']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Dr. {{ $doc['medecin_nom'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($doc['date'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='openViewModal(@json($doc))'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded hover:bg-blue-50"
                                            title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick='openPrintModal(@json($doc))'
                                            class="text-green-600 hover:text-green-800 transition-colors p-2 rounded hover:bg-green-50"
                                            title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-file-medical text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucun document trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL VISUALISATION DOCUMENT -->
    <div id="viewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 id="modalTitle" class="text-2xl font-semibold text-gray-800"></h2>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Side - Patient & Doctor Info -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations Patient</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Patient</label>
                                    <p id="patientInfo" class="text-gray-900 font-medium"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Médecin</label>
                                    <p id="medecinInfo" class="text-gray-900"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Type de document</label>
                                        <p id="documentType" class="text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Date</label>
                                        <p id="documentDate" class="text-gray-900"></p>
                                    </div>
                                </div>
                                <!-- Additional field for certificat type -->
                                <div id="certificatTypeDiv" class="hidden">
                                    <label class="block text-sm font-medium text-gray-600">Type de certificat</label>
                                    <p id="certificatType" class="text-gray-900"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Document Content -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contenu du Document</h3>
                            
                            <!-- For Ordonnance -->
                            <div id="ordonnanceContent" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Instructions</label>
                                    <p id="instructions" class="text-gray-900 bg-white p-3 rounded border"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Médicaments</label>
                                    <p id="medicaments" class="text-gray-900 bg-white p-3 rounded border"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Durée du traitement</label>
                                    <p id="dureeTraitement" class="text-gray-900 bg-white p-3 rounded border"></p>
                                </div>
                            </div>
                            
                            <!-- For Certificat -->
                            <div id="certificatContent" class="hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Contenu</label>
                                    <p id="contenu" class="text-gray-900 bg-white p-3 rounded border min-h-[120px]"></p>
                                </div>
                            </div>
                            
                            <!-- For Remarque -->
                            <div id="remarqueContent" class="hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Remarque</label>
                                    <p id="remarque" class="text-gray-900 bg-white p-3 rounded border min-h-[120px]"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button onclick="closeViewModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Fermer
                </button>
                <button onclick="printCurrentDocument()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </button>
            </div>
        </div>
    </div>

    <!-- PRINT CONTENT (Hidden) -->
    <div id="printContent" class="hidden print:block">
        <div class="max-w-4xl mx-auto p-6 bg-white min-h-screen">
            <div class="text-center mb-6 border-b-2 border-gray-300 pb-4">
                <h1 id="printTitle" class="text-2xl font-bold text-gray-800 mb-2"></h1>
                <p class="text-gray-600">Cabinet Médical</p>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Informations Patient</h3>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-20">Patient:</span>
                            <span id="printPatientInfo" class="text-gray-900"></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-20">Médecin:</span>
                            <span id="printMedecinInfo" class="text-gray-900"></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-20">Type:</span>
                            <span id="printDocumentType" class="text-gray-900"></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-20">Date:</span>
                            <span id="printDocumentDate" class="text-gray-900"></span>
                        </div>
                        <div id="printCertificatTypeDiv" class="hidden flex">
                            <span class="font-medium text-gray-600 w-20">Type cert.:</span>
                            <span id="printCertificatType" class="text-gray-900"></span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Contenu</h3>
                    
                    <!-- For Ordonnance -->
                    <div id="printOrdonnanceContent" class="hidden space-y-3">
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Instructions:</span>
                            <p id="printInstructions" class="text-gray-900 border-l-4 border-blue-500 pl-2 text-sm"></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Médicaments:</span>
                            <p id="printMedicaments" class="text-gray-900 border-l-4 border-blue-500 pl-2 text-sm"></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Durée du traitement:</span>
                            <p id="printDureeTraitement" class="text-gray-900 border-l-4 border-blue-500 pl-2 text-sm"></p>
                        </div>
                    </div>
                    
                    <!-- For Certificat -->
                    <div id="printCertificatContent" class="hidden">
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Contenu:</span>
                            <p id="printContenu" class="text-gray-900 border-l-4 border-green-500 pl-2 text-sm"></p>
                        </div>
                    </div>
                    
                    <!-- For Remarque -->
                    <div id="printRemarqueContent" class="hidden">
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Remarque:</span>
                            <p id="printRemarque" class="text-gray-900 border-l-4 border-purple-500 pl-2 text-sm"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
                <p>Document généré le {{ date('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentDocument = null;

        // Fonction pour masquer automatiquement les messages après 5 secondes
        function autoHideMessages() {
            const messages = [
                document.getElementById('successMessage'),
                document.getElementById('errorMessage')
            ];

            messages.forEach(message => {
                if (message) {
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '<i class="fas fa-times"></i>';
                    closeButton.className = 'float-right text-current opacity-70 hover:opacity-100 transition-opacity ml-2';
                    closeButton.onclick = () => hideMessage(message);
                    message.appendChild(closeButton);

                    setTimeout(() => {
                        hideMessage(message);
                    }, 5000);
                }
            });
        }

        function hideMessage(messageElement) {
            if (messageElement) {
                messageElement.style.opacity = '0';
                setTimeout(() => {
                    messageElement.style.display = 'none';
                }, 500);
            }
        }

        // VIEW Modal Functions - Fixed function name conflict
        function openViewModal(docData) {
            console.log('Document received:', docData);
            currentDocument = docData;
            
            // Set modal title based on document type
            const titles = {
                'ordonnance': 'Ordonnance Médicale',
                'certificat': 'Certificat Médical',
                'remarque': 'Remarque Médicale'
            };
            
            // Use DOM methods properly
            const modalTitle = document.getElementById('modalTitle');
            modalTitle.textContent = titles[docData.type] || 'Document Médical';
            
            // Fill patient and doctor info
            document.getElementById('patientInfo').textContent = `${docData.patient_cin} - ${docData.patient_nom}`;
            document.getElementById('medecinInfo').textContent = `Dr. ${docData.medecin_nom}`;
            document.getElementById('documentType').textContent = docData.type.charAt(0).toUpperCase() + docData.type.slice(1);
            document.getElementById('documentDate').textContent = new Date(docData.date).toLocaleDateString('fr-FR');
            
            // Show/hide content based on document type
            const ordonnanceContent = document.getElementById('ordonnanceContent');
            const certificatContent = document.getElementById('certificatContent');
            const remarqueContent = document.getElementById('remarqueContent');
            const certificatTypeDiv = document.getElementById('certificatTypeDiv');
            
            // Hide all content sections first
            ordonnanceContent.classList.add('hidden');
            certificatContent.classList.add('hidden');
            remarqueContent.classList.add('hidden');
            certificatTypeDiv.classList.add('hidden');
            
            if (docData.type === 'ordonnance') {
                ordonnanceContent.classList.remove('hidden');
                document.getElementById('instructions').textContent = docData.instructions || 'Non spécifié';
                document.getElementById('medicaments').textContent = docData.medicaments || 'Non spécifié';
                document.getElementById('dureeTraitement').textContent = docData.duree_traitement || 'Non spécifié';
            } else if (docData.type === 'certificat') {
                certificatContent.classList.remove('hidden');
                certificatTypeDiv.classList.remove('hidden');
                document.getElementById('certificatType').textContent = docData.certificat_type || 'Non spécifié';
                document.getElementById('contenu').textContent = docData.contenu || 'Non spécifié';
            } else if (docData.type === 'remarque') {
                remarqueContent.classList.remove('hidden');
                document.getElementById('remarque').textContent = docData.remarque || 'Non spécifié';
            }
            
            document.getElementById('viewModal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentDocument = null;
        }

        // PRINT Functions - Fixed to generate single page
        function openPrintModal(docData) {
            console.log('Print document:', docData);
            currentDocument = docData;
            preparePrintContent();
            window.print();
        }

        function printCurrentDocument() {
            if (currentDocument) {
                preparePrintContent();
                window.print();
            }
        }

        function preparePrintContent() {
            if (!currentDocument) return;
            
            console.log('Preparing print for:', currentDocument);
            
            const titles = {
                'ordonnance': 'ORDONNANCE MÉDICALE',
                'certificat': 'CERTIFICAT MÉDICAL',
                'remarque': 'REMARQUE MÉDICALE'
            };
            
            document.getElementById('printTitle').textContent = titles[currentDocument.type] || 'DOCUMENT MÉDICAL';
            document.getElementById('printPatientInfo').textContent = `${currentDocument.patient_cin} - ${currentDocument.patient_nom}`;
            document.getElementById('printMedecinInfo').textContent = `Dr. ${currentDocument.medecin_nom}`;
            document.getElementById('printDocumentType').textContent = currentDocument.type.charAt(0).toUpperCase() + currentDocument.type.slice(1);
            document.getElementById('printDocumentDate').textContent = new Date(currentDocument.date).toLocaleDateString('fr-FR');
            
            const printOrdonnanceContent = document.getElementById('printOrdonnanceContent');
            const printCertificatContent = document.getElementById('printCertificatContent');
            const printRemarqueContent = document.getElementById('printRemarqueContent');
            const printCertificatTypeDiv = document.getElementById('printCertificatTypeDiv');
            
            // Hide all print content sections first
            printOrdonnanceContent.classList.add('hidden');
            printCertificatContent.classList.add('hidden');
            printRemarqueContent.classList.add('hidden');
            printCertificatTypeDiv.classList.add('hidden');
            
            if (currentDocument.type === 'ordonnance') {
                printOrdonnanceContent.classList.remove('hidden');
                document.getElementById('printInstructions').textContent = currentDocument.instructions || 'Non spécifié';
                document.getElementById('printMedicaments').textContent = currentDocument.medicaments || 'Non spécifié';
                document.getElementById('printDureeTraitement').textContent = currentDocument.duree_traitement || 'Non spécifié';
            } else if (currentDocument.type === 'certificat') {
                printCertificatContent.classList.remove('hidden');
                printCertificatTypeDiv.classList.remove('hidden');
                document.getElementById('printCertificatType').textContent = currentDocument.certificat_type || 'Non spécifié';
                document.getElementById('printContenu').textContent = currentDocument.contenu || 'Non spécifié';
            } else if (currentDocument.type === 'remarque') {
                printRemarqueContent.classList.remove('hidden');
                document.getElementById('printRemarque').textContent = currentDocument.remarque || 'Non spécifié';
            }
        }

        // Search and Filter Functions
        function setupSearchAndFilter() {
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const medecinFilter = document.getElementById('medecinFilter');
            const tableRows = document.querySelectorAll('tbody tr[data-type]');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedType = typeFilter.value.toLowerCase();
                const selectedMedecin = medecinFilter.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const cin = row.cells[0].textContent.toLowerCase();
                    const patientName = row.cells[1].textContent.toLowerCase();
                    const documentType = row.getAttribute('data-type').toLowerCase();
                    const medecinName = row.getAttribute('data-medecin').toLowerCase();
                    
                    const matchesSearch = patientName.includes(searchTerm) || cin.includes(searchTerm);
                    const matchesType = !selectedType || documentType === selectedType;
                    const matchesMedecin = !selectedMedecin || medecinName === selectedMedecin;
                    
                    if (matchesSearch && matchesType && matchesMedecin) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                document.getElementById('documentCount').textContent = `${visibleCount} Document${visibleCount > 1 ? 's' : ''}`;
            }

            searchInput.addEventListener('input', filterTable);
            typeFilter.addEventListener('change', filterTable);
            medecinFilter.addEventListener('change', filterTable);
        }

        // Event Listeners for clicking outside modals
        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewModal();
            }
        });

        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeViewModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
            setupSearchAndFilter();
        });
    </script>
</body>
</html>