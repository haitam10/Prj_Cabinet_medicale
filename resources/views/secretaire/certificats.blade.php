<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Certificats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <a href="{{ route('secretaire.dashboard') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-home mr-3 text-cordes-accent group-hover:text-white"></i>
                    Dashboard
                </a>
                <a href="{{ route('secretaire.rendezvous') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-calendar-check mr-3 text-gray-400 group-hover:text-white"></i>
                    Rendez-vous
                </a>
                <a href="{{ route('secretaire.patients') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-user-injured mr-3 text-gray-400 group-hover:text-white"></i>
                    Patients
                </a>
                <a href="{{ route('secretaire.factures') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-file-invoice-dollar mr-3 text-gray-400 group-hover:text-white"></i>
                    Factures
                </a>
                <a href="{{ route('secretaire.paiements') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-credit-card mr-3 text-gray-400 group-hover:text-white"></i>
                    Paiements
                </a>
                <a href="{{ route('secretaire.certificats') }}"
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Certificats
                </a>
                <a href="{{ route('secretaire.ordonnances') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-prescription-bottle-medical mr-3 text-gray-400 group-hover:text-white"></i>
                    Ordonnances
                </a>
                <a href="{{ route('secretaire.remarques') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-sticky-note mr-3 text-gray-400 group-hover:text-white"></i>
                    Remarques
                </a>
                <a href="{{ route('secretaire.papier') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-cog mr-3 text-gray-400 group-hover:text-white"></i>
                    Papier
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Certificats</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des certificats médicaux générés</p>
                </div>
                <button onclick="openGenerateModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Générer Certificat
                </button>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher par CIN ou nom patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
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
                        <i class="fas fa-file-medical mr-2 text-green-600"></i>
                        <span id="documentCount">
                            {{ count($documents ?? []) }} 
                            Certificat{{ count($documents ?? []) > 1 ? 's' : '' }}
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
                        @forelse ($documents ?? [] as $doc)
                            <tr class="hover:bg-gray-50 transition-colors document-row" 
                                data-cin="{{ $doc['patient_cin'] ?? '' }}" 
                                data-patient="{{ $doc['patient_nom'] ?? '' }}" 
                                data-medecin="{{ $doc['medecin_nom'] ?? '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_cin'] ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_nom'] ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ $doc['certificat_type'] ?? 'Certificat' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Dr. {{ $doc['medecin_nom'] ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($doc['date']) ? \Carbon\Carbon::parse($doc['date'])->format('d/m/Y') : '' }}
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
                            <tr id="emptyRow">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-file-medical text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucun certificat trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL GÉNERER CERTIFICAT -->
    <div id="generateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Générer Certificat</h2>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('certificat.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                        <select name="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->cin }} - {{ $patient->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                        <select name="medecin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un médecin</option>
                            @foreach($medecins ?? [] as $medecin)
                            <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de certificat</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un type</option>
                            <option value="Repos">Repos</option>
                            <option value="Travail">Travail</option>
                            <option value="Sport">Sport</option>
                            <option value="École">École</option>
                            <option value="Voyage">Voyage</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                        <textarea name="contenu" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Contenu du certificat..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date_certificat" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Générer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL VISUALISATION CERTIFICAT -->
    <div id="viewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 id="modalTitle" class="text-2xl font-semibold text-gray-800">Certificat Médical</h2>
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
                                        <label class="block text-sm font-medium text-gray-600">Type de certificat</label>
                                        <p id="certificatType" class="text-gray-900"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Date</label>
                                        <p id="documentDate" class="text-gray-900"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Document Content -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contenu du Certificat</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Contenu</label>
                                <p id="contenu" class="text-gray-900 bg-white p-3 rounded border min-h-[120px]"></p>
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

    <!-- PRINT CONTENT (Hidden) - DEFAULT CERTIFICAT TEMPLATE -->
    <div id="printContent" class="hidden print:block">
        <div class="max-w-4xl mx-auto p-8 bg-white min-h-screen" style="font-family: 'Times New Roman', serif;">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mb-4">
                    <!-- Medical Symbol -->
                    <div class="mr-8">
                        <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50 10C30 10 15 25 15 45C15 65 30 80 50 80C70 80 85 65 85 45C85 25 70 10 50 10Z" stroke="black" stroke-width="2" fill="none"/>
                            <path d="M35 35L65 65M65 35L35 65" stroke="black" stroke-width="2"/>
                            <path d="M50 20L50 70M30 50L70 50" stroke="black" stroke-width="3"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h1 class="text-xl font-bold mb-2">Bureau médical</h1>
                        <div class="text-sm">
                            <p class="font-semibold">DR. <span id="printDoctorName">_________________</span></p>
                            <p class="font-semibold">MÉDECIN - CHIRURGIEN</p>
                            <p class="text-xs mt-1">MÉDECINE GÉNÉRALE, PÉDIATRIE, MALADIES RESPIRATOIRES ET CUTANÉES</p>
                            <p class="text-xs">CHIRURGIE VÉNÉRIENNE, MAJEURE ET MINEURE</p>
                            <p class="text-xs mt-1">Adresse.: <span id="printAddress">_________________</span> Tél.: <span id="printPhone">_________________</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date -->
            <div class="text-right mb-8">
                <p class="font-semibold">BONNE FOI, <span id="printDate"></span></p>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold tracking-wider">CERTIFICAT MÉDICAL</h2>
            </div>

            <!-- Doctor Info -->
            <div class="mb-6">
                <p class="font-semibold">YO, <span id="printDoctorNameFull">_________________________</span></p>
                <p class="font-semibold">MÉDECIN CHIRURGIEN</p>
            </div>

            <!-- Certificate Content -->
            <div class="mb-8 leading-relaxed">
                <p id="printCertificateContent" class="text-justify"></p>
            </div>

            <!-- Closing -->
            <div class="text-center mb-12">
                <p class="italic">Je vous prie d'agréer, Monsieur le Président, l'expression de mes</p>
                <p class="italic">sentiments distingués,</p>
            </div>

            <!-- Signature -->
            <div class="text-center">
                <div class="inline-block">
                    <div class="border-b-2 border-black w-48 mb-2"></div>
                    <p class="font-semibold">DR.</p>
                    <p class="font-semibold">Chirurgien</p>
                </div>
            </div>
        </div>
    </div>
           @if(session('print_document'))
            <script>
                let currentDocument = null;

                @if(session('print_certificat'))
                    currentDocument = @json(session('print_certificat'));
                @elseif(session('print_ordonnance'))
                    currentDocument = @json(session('print_ordonnance'));
                @endif

                if (currentDocument) {
                    preparePrintContent();
                    setTimeout(() => {
                        window.print();
                    }, 500);
                }
            </script>
        @endif

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentDocument = null;

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

        // Generate Modal Functions
        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function openViewModal(docData) {
            currentDocument = docData;
            
            document.getElementById('patientInfo').textContent = `${docData.patient_cin} - ${docData.patient_nom}`;
            document.getElementById('medecinInfo').textContent = `Dr. ${docData.medecin_nom}`;
            document.getElementById('certificatType').textContent = docData.certificat_type || docData.type || 'Non spécifié';
            document.getElementById('documentDate').textContent = new Date(docData.date).toLocaleDateString('fr-FR');
            document.getElementById('contenu').textContent = docData.contenu || 'Non spécifié';
            
            document.getElementById('viewModal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentDocument = null;
        }

        function openPrintModal(docData) {
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
            
            // Fill the default template with data
            document.getElementById('printDoctorName').textContent = currentDocument.medecin_nom || 'Hamza';
            document.getElementById('printDoctorNameFull').textContent = `Dr. ${currentDocument.medecin_nom || 'Hamza'}`;
            document.getElementById('printAddress').textContent = '123 Rue Médicale, Casablanca';
            document.getElementById('printPhone').textContent = '0522-123456';
            document.getElementById('printDate').textContent = new Date(currentDocument.date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).toUpperCase();
            document.getElementById('printCertificateContent').textContent = currentDocument.contenu || 'Non spécifié';
        }

        // Fixed Search and Filter Functions
        function setupSearchAndFilter() {
            const searchInput = document.getElementById('searchInput');
            const medecinFilter = document.getElementById('medecinFilter');
            const tableRows = document.querySelectorAll('.document-row');
            const emptyRow = document.getElementById('emptyRow');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedMedecin = medecinFilter.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const cin = (row.getAttribute('data-cin') || '').toLowerCase();
                    const patientName = (row.getAttribute('data-patient') || '').toLowerCase();
                    const medecinName = (row.getAttribute('data-medecin') || '').toLowerCase();
                    
                    const matchesSearch = cin.includes(searchTerm) || patientName.includes(searchTerm);
                    const matchesMedecin = !selectedMedecin || medecinName.includes(selectedMedecin);
                    
                    if (matchesSearch && matchesMedecin) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Handle empty state
                if (emptyRow) {
                    if (visibleCount === 0 && tableRows.length > 0) {
                        emptyRow.style.display = '';
                        emptyRow.querySelector('p').textContent = 'Aucun certificat trouvé pour cette recherche';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                }

                // Update count
                document.getElementById('documentCount').textContent = `${visibleCount} Certificat${visibleCount > 1 ? 's' : ''}`;
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (medecinFilter) medecinFilter.addEventListener('change', filterTable);
        }

        // Modal event listeners
        document.getElementById('generateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGenerateModal();
            }
        });

        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGenerateModal();
                closeViewModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
            setupSearchAndFilter();
            
            // Set current date
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.querySelector('input[name="date_certificat"]');
            if (dateInput) {
                dateInput.value = today;
            }
        });
    </script>
</body>
</html>