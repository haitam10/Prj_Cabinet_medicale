<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Remarques</title>
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
                    <i class="fas fa-calendar-check mr-3 text-white"></i>
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
                @if (Auth::check() && Auth::user()->role === 'medecin')
                    <a href="{{ route('secretaire.dossier-medical') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-file-medical mr-3 text-white"></i>
                        Dossier Médical
                    </a>
                    <a href="{{ route('secretaire.calendrier') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-calendar-alt mr-3 text-gray-400 group-hover:text-white"></i>
                        Calendrier
                    </a>
                    <a href="{{ route('secretaire.certificats') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-file-medical mr-3 text-white"></i>
                        Certificats
                    </a>
                    <a href="{{ route('secretaire.ordonnances') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-prescription-bottle-medical mr-3 text-gray-400 group-hover:text-white"></i>
                        Ordonnances
                    </a>
                    <a href="{{ route('secretaire.remarques') }}"
                        class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                        <i class="fas fa-sticky-note mr-3 text-gray-400 group-hover:text-white"></i>
                        Remarques
                    </a>
                    <a href="{{ route('secretaire.papier') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-cog mr-3 text-gray-400 group-hover:text-white"></i>
                        Papier
                    </a>
                @endif
                <a href="{{ route('secretaire.profile') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-user mr-3 text-cordes-accent"></i>
                    Mon Profil
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Remarques</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des remarques médicales générées</p>
                </div>
                <button onclick="openGenerateModal()"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter Remarque
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
                        <i class="fas fa-sticky-note mr-2 text-purple-600"></i>
                        <span id="documentCount">
                            {{ count($documents ?? []) }} 
                            Remarque{{ count($documents ?? []) > 1 ? 's' : '' }}
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REMARQUE</th>
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
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ Str::limit($doc['remarque'] ?? 'Non spécifié', 50) }}
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
                                    <i class="fas fa-sticky-note text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucune remarque trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL AJOUTER REMARQUE -->
    <div id="generateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-sticky-note mr-2 text-purple-600"></i>Ajouter Remarque
                </h2>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- <form action="{{ route('remarque.store') ?? '#' }}" method="POST" class="p-6"> --}}
                <form action="#" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Patient
                        </label>
                        <select name="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un patient</option>
                            <!-- Add patients from controller -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-md mr-1"></i>Médecin
                        </label>
                        <select name="medecin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un médecin</option>
                            <option value="1">Dr. Hamza</option>
                            <option value="2">Dr. Reda</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-1"></i>Remarque
                        </label>
                        <textarea name="remarque" rows="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Saisir la remarque médicale..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Date
                        </label>
                        <input type="date" name="date_remarque" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL VISUALISATION REMARQUE -->
    <div id="viewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 id="modalTitle" class="text-2xl font-semibold text-gray-800">Remarque Médicale</h2>
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
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Date</label>
                                    <p id="documentDate" class="text-gray-900"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Document Content -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contenu de la Remarque</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Remarque</label>
                                <p id="remarque" class="text-gray-900 bg-white p-3 rounded border min-h-[120px]"></p>
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
                <h1 class="text-2xl font-bold text-gray-800 mb-2">REMARQUE MÉDICALE</h1>
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
                            <span class="font-medium text-gray-600 w-20">Date:</span>
                            <span id="printDocumentDate" class="text-gray-900"></span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Contenu</h3>
                    <div>
                        <span class="font-medium text-gray-600 block mb-1">Remarque:</span>
                        <p id="printRemarque" class="text-gray-900 border-l-4 border-purple-500 pl-2 text-sm"></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
                <p>Document généré le {{ date('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>

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
            
            document.getElementById('patientInfo').textContent = `${docData.patient_cin || ''} - ${docData.patient_nom || ''}`;
            document.getElementById('medecinInfo').textContent = `Dr. ${docData.medecin_nom || ''}`;
            document.getElementById('documentDate').textContent = docData.date ? new Date(docData.date).toLocaleDateString('fr-FR') : '';
            document.getElementById('remarque').textContent = docData.remarque || 'Non spécifié';
            
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
            
            document.getElementById('printPatientInfo').textContent = `${currentDocument.patient_cin || ''} - ${currentDocument.patient_nom || ''}`;
            document.getElementById('printMedecinInfo').textContent = `Dr. ${currentDocument.medecin_nom || ''}`;
            document.getElementById('printDocumentDate').textContent = currentDocument.date ? new Date(currentDocument.date).toLocaleDateString('fr-FR') : '';
            document.getElementById('printRemarque').textContent = currentDocument.remarque || 'Non spécifié';
        }

        // FIXED Search and Filter Functions
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
                        emptyRow.querySelector('p').textContent = 'Aucune remarque trouvée pour cette recherche';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                }

                // Update count
                document.getElementById('documentCount').textContent = `${visibleCount} Remarque${visibleCount > 1 ? 's' : ''}`;
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
            const dateInput = document.querySelector('input[name="date_remarque"]');
            if (dateInput) {
                dateInput.value = today;
            }
        });
    </script>
</body>
</html>