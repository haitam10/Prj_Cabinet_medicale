<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Certificats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />*
    
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
        /* ONLY print styles - NO template styles in main page */
        @media print {
            body * {
                visibility: hidden;
            }
            #printFrame {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
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

    <div class="ml-64">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Certificats</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des certificats médicaux générés</p>
                </div>
                <button id="generateCertificatBtn"
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
                                        <button class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded hover:bg-blue-50 view-certificat-btn"
                                            title="Voir" data-doc-id="{{ $doc['id'] }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-800 transition-colors p-2 rounded hover:bg-green-50 print-certificat-btn"
                                            title="Imprimer" data-doc-id="{{ $doc['id'] }}">
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

    <div id="templateSelectionModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl m-4">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Sélectionner un Modèle</h2>
                <button onclick="closeTemplateSelectionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Choisir un modèle</label>
                        <select id="templateSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un modèle...</option>
                        </select>
                    </div>
                    <div id="templatePreview" class="hidden bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <img id="templateLogo" src="/placeholder.svg" alt="Logo" class="w-12 h-12 object-cover rounded">
                            <div>
                                <p id="templateName" class="font-medium text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button onclick="closeTemplateSelectionModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button onclick="proceedWithSelectedTemplate()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </button>
            </div>
        </div>
    </div>

    <iframe id="printFrame" style="display: none;"></iframe>

    @if(session('print_document') && session('print_certificat'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const printData = @json(session('print_certificat'));
                printCertificat(printData);
            });
        </script>
    @endif

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentDocument = null;

        // Default template as requested
        const defaultTemplate = {
            id: 'default',
            name: 'Modèle par défaut',
            logo_file_path: 'uploads/okz6IeWL6Tc8ws7w6DzvCGeECccdMxOIYfeVUy0p.png',
            descr_head: 'Je soussigné(e), atteste que le patient suivant :',
            descr_body: 'présente un état nécessitant un arrêt temporaire de ses activités.',
            descr_footer: 'Document remis à la personne concernée pour usage administratif.'
        };

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
            // Fetch the complete data from the new API endpoint to ensure template info is available
            fetch(`/api/certificat/${docData.id}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(fullDocData => {
                    currentDocument = fullDocData; // Store the full data for viewing and printing

                    document.getElementById('patientInfo').textContent = `${fullDocData.patient.cin} - ${fullDocData.patient.nom}`;
                    document.getElementById('medecinInfo').textContent = `Dr. ${fullDocData.medecin.nom}`;
                    document.getElementById('certificatType').textContent = fullDocData.certificat.type || fullDocData.type || 'Non spécifié';
                    document.getElementById('documentDate').textContent = new Date(fullDocData.certificat.date_certificat).toLocaleDateString('fr-FR');
                    document.getElementById('contenu').textContent = fullDocData.certificat.contenu || 'Non spécifié';
                    
                    document.getElementById('viewModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données du certificat pour visualisation:', error);
                    alert('Erreur lors de la récupération des données du certificat pour visualisation.');
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentDocument = null;
        }

        let pendingPrintDocument = null;
        let availableTemplates = [];

        function openPrintModal(docData) {
            console.log('Opening print modal for document:', docData);
            
            // First fetch the complete document data
            fetch(`/api/certificat/${docData.id}/data`)
                .then(response => {
                    console.log('Document data response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(fullDocData => {
                    console.log('Document data received:', fullDocData);
                    
                    // Validate document data structure
                    if (!fullDocData || !fullDocData.patient || !fullDocData.medecin || !fullDocData.certificat) {
                        throw new Error('Document data structure is invalid');
                    }
                    
                    pendingPrintDocument = fullDocData;
                    
                    // Now fetch available templates by type (certificat)
                    return fetch(`/papier/template/certificat`);
                })
                .then(response => {
                    console.log('Templates response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(templates => {
                    console.log('Templates received:', templates);
                    availableTemplates = templates || [];
                    
                    // Populate the template dropdown
                    const templateSelect = document.getElementById('templateSelect');
                    templateSelect.innerHTML = '<option value="">Sélectionner un modèle...</option>';
                    
                    // Add default template first
                    const defaultOption = document.createElement('option');
                    defaultOption.value = 'default';
                    defaultOption.textContent = defaultTemplate.name;
                    templateSelect.appendChild(defaultOption);
                    
                    // Add available templates
                    if (availableTemplates && availableTemplates.length > 0) {
                        availableTemplates.forEach(template => {
                            const option = document.createElement('option');
                            option.value = template.id;
                            option.textContent = template.name;
                            templateSelect.appendChild(option);
                        });
                    }
                    
                    // Show the template selection modal
                    document.getElementById('templateSelectionModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error in openPrintModal:', error);
                    
                    // If we have document data but template fetch failed, use default template
                    if (pendingPrintDocument) {
                        console.log('Using default template due to error');
                        pendingPrintDocument.template = defaultTemplate;
                        currentDocument = pendingPrintDocument;
                        printCertificat(pendingPrintDocument);
                    } else {
                        alert('Erreur lors du chargement des données du document. Veuillez réessayer.');
                    }
                });
        }

        function closeTemplateSelectionModal() {
            document.getElementById('templateSelectionModal').classList.add('hidden');
            document.getElementById('templatePreview').classList.add('hidden');
            // Don't reset pendingPrintDocument here to avoid losing data
        }

        function proceedWithSelectedTemplate() {
            console.log('Proceeding with selected template');
            console.log('pendingPrintDocument:', pendingPrintDocument);
            
            const selectedTemplateId = document.getElementById('templateSelect').value;
            console.log('Selected template ID:', selectedTemplateId);

            if (!selectedTemplateId) {
                alert('Veuillez sélectionner un modèle.');
                return;
            }

            if (!pendingPrintDocument) {
                console.error('pendingPrintDocument is null');
                alert("Erreur: Données du document perdues. Veuillez réessayer.");
                closeTemplateSelectionModal();
                return;
            }

            let selectedTemplate;
            
            if (selectedTemplateId === 'default') {
                selectedTemplate = defaultTemplate;
            } else {
                selectedTemplate = availableTemplates.find(t => t.id == selectedTemplateId);
            }

            if (!selectedTemplate) {
                console.error('Selected template not found');
                alert("Erreur: Modèle sélectionné introuvable.");
                return;
            }

            console.log('Selected template:', selectedTemplate);

            // Attach template to document
            pendingPrintDocument.template = selectedTemplate;
            currentDocument = pendingPrintDocument;
            
            closeTemplateSelectionModal();
            printCertificat(pendingPrintDocument);
        }

        function printCurrentDocument() {
            console.log('Printing current document:', currentDocument);
            
            if (!currentDocument) {
                alert("Aucun document valide à imprimer.");
                return;
            }

            // If no template, use default
            if (!currentDocument.template) {
                console.log('No template found, using default');
                currentDocument.template = defaultTemplate;
            }
            
            printCertificat(currentDocument);
        }

        function printCertificat(data) {
            console.log('printCertificat called with data:', data);
            
            if (!data) {
                console.error("Invalid document data:", data);
                alert("Impossible d'imprimer : données du document invalides.");
                return;
            }

            // Validate required data structure
            if (!data.patient || !data.medecin || !data.certificat) {
                console.error("Missing required data structure:", data);
                alert("Impossible d'imprimer : structure des données invalide.");
                return;
            }

            // Use default template if no template is provided
            if (!data.template) {
                console.warn("Template manquant, utilisation du modèle par défaut");
                data.template = defaultTemplate;
            }

            console.log('Using template:', data.template);

            // Helper function to decode Unicode escape sequences and HTML entities
            function decodeHtmlAndUnicode(str) {
                if (!str) return '';
                const textarea = document.createElement('textarea');
                textarea.innerHTML = str; // This decodes HTML entities

                return textarea.value.replace(/\\u[\dA-F]{4}/gi,
                    function (match) {
                        return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
                    });
            }

            const decodedDescrHead = decodeHtmlAndUnicode(data.template.descr_head);
            const decodedDescrBody = decodeHtmlAndUnicode(data.template.descr_body);
            const decodedDescrFooter = decodeHtmlAndUnicode(data.template.descr_footer);

            const certificatHTML = `
                <!DOCTYPE html>
                    <html lang="fr">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Certificat Médical</title>
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
                                padding: 2.5cm 2.5cm 1.5cm 2.5cm;
                                box-sizing: border-box;
                                position: relative;
                                display: flex;
                                flex-direction: column;
                            }

                            .header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 5px;
                                padding-bottom: 10px;
                            }

                            .doctor-info {
                                font-size: 10pt;
                                line-height: 1.4;
                                width: 25%;
                                text-align: left;
                            }
                            .cabinet-info {
                                font-size: 10pt;
                                line-height: 1.3;
                                width: 25%;
                                text-align: center;
                            }

                            .doctor-info strong, .cabinet-info strong {
                                font-size: 12pt;
                            }

                            .logo-container {
                                width: 50%;
                            }

                            .caduceus-icon {
                                width: 80%;
                                height: 80%;
                                opacity: 0.8;
                                display: block;
                                margin: 0 auto;
                            }
                            
                            .caduceus-large {
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 400px;
                                height: 400px;
                                opacity: 0.06;
                                z-index: 0;
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

                            .date-input-line {
                                display: inline-block;
                                width: 30px;
                                border-bottom: 1px solid #000;
                                text-align: center;
                            }

                            .doctor-name-signature-line {
                                margin-top: 30px;
                                font-size: 14px;
                            }
                            .patient-name-input-line {
                                display: inline-block;
                                width: 300px;
                                border-bottom: 1px solid #000;
                                padding-left: 5px;
                            }

                            .content-area {
                                flex-grow: 1;
                                position: relative;
                                z-index: 1;
                                padding-top: 20px;
                                min-height: 150px;
                            }

                            .footer {
                                border-top: 1px solid #000;
                                padding-top: 10px;
                                margin-top: auto;
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
                                    <strong>Dr. ${data.medecin.nom || 'Nom & Prénom'}</strong><br>
                                    Médecin ${data.medecin.specialite || 'Générale'}<br>
                                    Tél : ${data.medecin.telephone || '0522 000 000'}<br>
                                    ${data.medecin.email || 'Adressemail@gmail.com'}
                                </div>
                                <div class="logo-container">
                                    ${data.template.logo_file_path ?
                                    `<img src="${window.location.origin}/storage/${data.template.logo_file_path}" alt="Logo" class="caduceus-icon">` :
                                    `<img src="${window.location.origin}/public/uploads/cm_logo_default.png" alt="Logo" class="caduceus-icon">`
                                }
                                </div>
                                
                                <div class="cabinet-info">
                                    <strong>${data.cabinet?.nom_cabinet || 'Cabinet Nom'}</strong><br>
                                    Adresse : ${data.cabinet?.addr_cabinet || 'Votre Adresse'}<br>
                                    ${data.cabinet?.descr_cabinet || ''}<br>
                                    Tél : ${data.cabinet?.tel_cabinet || '0522 000 000'}
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="title">
                                CERTIFICAT MÉDICAL
                            </div>

                            <div style="margin-bottom: 20px;">
                                ${decodedDescrHead}<br><br>
                                ${data.patient.nom ? `<strong>${data.patient.sexe === 'M' ? 'Mr' : 'Mme'} ${data.patient.nom}</strong>` : ''} ${decodedDescrBody}<br><br>
                                ${decodedDescrFooter}
                            </div>
                            <br><br>
                            <div class="date-location">
                                Fait à : Salé, Le 
                                ${
                                    data.certificat.date_certificat ?
                                    (() => {
                                        const d = new Date(data.certificat.date_certificat);
                                        const day = d.getDate().toString().padStart(2, '0');
                                        const month = (d.getMonth() + 1).toString().padStart(2, '0');
                                        const year = d.getFullYear();
                                        return `
                                            <span class="date-input-line">${day}</span> /
                                            <span class="date-input-line">${month}</span> /
                                            <span class="date-input-line">${year}</span>
                                        `;
                                    })() :
                                    `
                                        <span class="date-input-line">&nbsp;&nbsp;</span> /
                                        <span class="date-input-line">&nbsp;&nbsp;</span> /
                                        <span class="date-input-line">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    `
                                }
                            </div>

                            <div class="content-area">
                                ${data.template.logo_file_path ? 
                                    `<img src="${window.location.origin}/storage/${data.template.logo_file_path}" alt="Logo Large" class="caduceus-large">` : 
                                    `<img src="${window.location.origin}/public/uploads/cm_logo_default.png" alt="Logo Large" class="caduceus-large">`
                                }
                            </div>

                            <div class="footer">
                                Adresse: ${data.cabinet?.addr_cabinet || '123, Avenue adresse , ville'} - Tél: ${data.cabinet?.tel_cabinet || '0522 000 000 - 0522 000 000'}<br>
                                ${data.medecin?.email || ''}
                            </div>
                        </div>
                    </body>
                    </html>
                `;

            console.log('Generated HTML for printing');
            
            const printFrame = document.getElementById('printFrame');
            
            printFrame.contentDocument.open();
            printFrame.contentDocument.write(certificatHTML);
            printFrame.contentDocument.close();

            setTimeout(() => {
                printFrame.contentWindow.print();
            }, 500);
        }

        // Search and Filter Functions
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

        document.getElementById('templateSelectionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTemplateSelectionModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGenerateModal();
                closeViewModal();
                closeTemplateSelectionModal();
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

            // Event listeners for buttons
            const generateBtn = document.getElementById('generateCertificatBtn');
            if (generateBtn) {
                generateBtn.addEventListener('click', openGenerateModal);
            }

            // For the "Voir" buttons in the table
            document.querySelectorAll('.view-certificat-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const docId = this.dataset.docId;
                    openViewModal({ id: docId });
                });
            });

            // For the "Imprimer" buttons in the table
            document.querySelectorAll('.print-certificat-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const docId = this.dataset.docId;
                    openPrintModal({ id: docId });
                });
            });

            // Template selection preview functionality
            document.getElementById('templateSelect').addEventListener('change', function() {
                const selectedTemplateId = this.value;
                const templatePreview = document.getElementById('templatePreview');
                
                if (selectedTemplateId) {
                    let selectedTemplate;
                    
                    if (selectedTemplateId === 'default') {
                        selectedTemplate = defaultTemplate;
                    } else {
                        selectedTemplate = availableTemplates.find(t => t.id == selectedTemplateId);
                    }
                    
                    if (selectedTemplate) {
                        document.getElementById('templateName').textContent = selectedTemplate.name;
                        const logoImg = document.getElementById('templateLogo');
                        if (selectedTemplate.logo_file_path) {
                            logoImg.src = `${window.location.origin}/storage/${selectedTemplate.logo_file_path}`;
                        } else {
                            logoImg.src = `${window.location.origin}/public/uploads/cm_logo_default.png`;
                        }
                        templatePreview.classList.remove('hidden');
                    }
                } else {
                    templatePreview.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>