<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Ordonnances</title>
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
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-gray-400 group-hover:text-white"></i>
                    Certificats
                </a>
                <a href="{{ route('secretaire.ordonnances') }}"
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-prescription-bottle-medical mr-3 text-white"></i>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Ordonnances</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des ordonnances médicales générées</p>
                </div>
                <button onclick="openGenerateModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Générer Ordonnance
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
                        <i class="fas fa-prescription-bottle-medical mr-2 text-blue-600"></i>
                        <span id="documentCount">
                            {{ count($documents ?? []) }} 
                            Ordonnance{{ count($documents ?? []) > 1 ? 's' : '' }}
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MÉDICAMENTS</th>
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
                                    {{ Str::limit($doc['medicaments'] ?? 'Non spécifié', 50) }}
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
                                    <i class="fas fa-prescription-bottle-medical text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucune ordonnance trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL GÉNERER ORDONNANCE -->
    <div id="generateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-prescription-bottle-medical mr-2 text-blue-600"></i>Générer Ordonnance
                </h2>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('ordonnance.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Patient
                        </label>
                        <select name="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->cin }} - {{ $patient->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-md mr-1"></i>Médecin
                        </label>
                        <select name="medecin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            <option value="">Sélectionner un médecin</option>
                            @foreach($medecins ?? [] as $medecin)
                            <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-pills mr-1"></i>Médicaments
                        </label>
                        <textarea name="medicaments" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Liste des médicaments prescrits..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-1"></i>Instructions
                        </label>
                        <textarea name="instructions" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Instructions pour le patient..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-1"></i>Durée du traitement
                        </label>
                        <input type="text" name="duree_traitement" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Ex: 7 jours, 2 semaines...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Date
                        </label>
                        <input type="date" name="date_ordonnance" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Générer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL VISUALISATION ORDONNANCE -->
    <div id="viewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 id="modalTitle" class="text-2xl font-semibold text-gray-800">Ordonnance Médicale</h2>
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
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contenu de l'Ordonnance</h3>
                            
                            <div class="space-y-4">
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

    <!-- HIDDEN IFRAME FOR PRINTING - NO TEMPLATE INCLUSION -->
    <iframe id="printFrame" style="display: none;"></iframe>

    <!-- AUTO PRINT SCRIPT -->
    @if(session('print_document') && session('print_ordonnance'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const printData = @json(session('print_ordonnance'));
                printOrdonnance(printData);
            });
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
            // Fetch the complete data from the new API endpoint to ensure template info is available
            fetch(`/api/ordonnance/${docData.id}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(fullDocData => {
                    currentDocument = fullDocData; // Store the full data for viewing and printing
                    
                    document.getElementById('patientInfo').textContent =
                        `${fullDocData.patient.cin || ''} - ${fullDocData.patient.nom || ''} ${fullDocData.patient.prenom || ''}`;

                    document.getElementById('medecinInfo').textContent =
                        `Dr. ${fullDocData.medecin.nom || ''} ${fullDocData.medecin.prenom || ''}`;

                    document.getElementById('documentDate').textContent =
                        fullDocData.ordonnance.date_ordonnance
                            ? new Date(fullDocData.ordonnance.date_ordonnance).toLocaleDateString('fr-FR')
                            : '';

                    document.getElementById('instructions').textContent =
                        fullDocData.ordonnance.instructions || 'Non spécifié';

                    document.getElementById('medicaments').textContent =
                        fullDocData.ordonnance.medicaments || 'Non spécifié';

                    document.getElementById('dureeTraitement').textContent =
                        fullDocData.ordonnance.duree_traitement || 'Non spécifié';

                    document.getElementById('viewModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données de l\'ordonnance pour visualisation:', error);
                    alert('Erreur lors de la récupération des données de l\'ordonnance pour visualisation.');
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentDocument = null;
        }

        function openPrintModal(docData) {
            // docData from the table might not have the full template info.
            // Fetch the complete data from the new API endpoint.
            fetch(`/api/ordonnance/${docData.id}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(fullDocData => {
                    currentDocument = fullDocData; // Store the full data
                    printOrdonnance(fullDocData);
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données de l\'ordonnance pour impression:', error);
                    alert('Erreur lors de la récupération des données de l\'ordonnance pour impression.');
                });
        }

        function printCurrentDocument() {
            if (currentDocument) {
                printOrdonnance(currentDocument);
            }
        }

       function printOrdonnance(data) {
    console.log(data); // For debugging purposes

    const template = data.template || {};

    let prescriptionDate = null;
    if (data.date) {
        prescriptionDate = new Date(data.date);
    } else if (data.ordonnance && data.ordonnance.date_ordonnance) {
         prescriptionDate = new Date(data.ordonnance.date_ordonnance);
    } else {
        prescriptionDate = new Date(); // Fallback to current date
    }

    const ordonnanceHTML = `
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
                    font-family: 'Times New Roman', serif;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh;
                    background-color: #fff;
                    color: #000;
                    font-size: 11pt;
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
                    justify-content: space-between; /* Distribute space between items */
                    align-items: center; /* Vertically center items */
                    margin-bottom: 5px;
                    padding-bottom: 10px;
                    /* Removed position: relative as logo will be within a flex item */
                }

                .doctor-info {
                    font-size: 10pt;
                    line-height: 1.4;
                    width: 25%; /* Set width to 30% */
                    text-align: left; /* Center the text */
                }
                .cabinet-info {
                    font-size: 10pt;
                    line-height: 1.3;
                    width: 25%;
                    text-align: center; /* Center the text */
                }
                /* Removed explicit text-align for doctor-info and cabinet-info as it's now in the combined rule */

                .doctor-info strong, .cabinet-info strong {
                    font-size: 12pt;
                }

                .logo-container {
                    width: 50%; /* 40% width for the logo container */
                }

                .caduceus-icon {
                    width: 80%; /* Bigger */
                    height: 80%; /* Bigger */
                    opacity: 0.8;
                    /* Removed absolute positioning to work with flexbox */
                    /* Removed left, transform, top */
                    display: block; /* Ensures image respects text-align center */
                    margin: 0 auto; /* Centers the image if it's smaller than its container */
                }
                
                .caduceus-large {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 400px; /* Much Bigger */
                    height: 400px; /* Much Bigger */
                    opacity: 0.06; /* Slightly more faint for a watermark effect */
                    z-index: 0;
                }

                .divider {
                    border-bottom: 1px solid #000;
                    margin: 15px 0 30px 0;
                }

                .title {
                    text-align: center;
                    font-size: 14pt;
                    font-weight: bold;
                    margin: 20px 0 40px 0;
                    text-decoration: underline;
                }

                .date-location {
                    text-align: right;
                    margin-top: 20px;
                    margin-bottom: 30px;
                    font-size: 11pt;
                }

                .date-input-line {
                    display: inline-block;
                    width: 25px;
                    /* Removed border-bottom */
                    text-align: center;
                    vertical-align: bottom;
                    line-height: 1.2;
                    height: 1.2em;
                }

                .patient-name-line {
                    margin-top: 15px;
                    margin-bottom: 40px;
                    font-size: 11pt;
                }
                .patient-name-input-line {
                    display: inline-block;
                    width: 250px;
                    /* Removed border-bottom */
                    padding-left: 5px;
                    vertical-align: bottom;
                    line-height: 1.2;
                    height: 1.2em;
                }

                .content-area {
                    flex-grow: 1;
                    position: relative;
                    z-index: 1;
                    padding-top: 0px;
                    min-height: 150px;
                }
                .prescription-text {
                    margin-top: 10px;
                    min-height: 150px;
                    line-height: 1.6;
                    font-size: 11pt;
                    white-space: pre-line;
                }

                .footer {
                    border-top: 1px solid #000;
                    padding-top: 10px;
                    margin-top: auto;
                    text-align: center;
                    font-size: 9pt;
                    line-height: 1.3;
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
                    <div class="logo-container"> ${data.template.logo_file_path ?
                            `<img src="${window.location.origin}/storage/${data.template.logo_file_path}" alt="Caduceus" class="caduceus-icon">` :
                            `<img src="${window.location.origin}/public/uploads/cm_logo_default.png" alt="Caduceus" class="caduceus-icon">`
                        }
                    </div>
                    
                    <div class="cabinet-info">
                        <strong>${data.cabinet.nom_cabinet || 'Cabinet Nom'}</strong><br>
                        Adresse : ${data.cabinet.addr_cabinet || 'Votre Adresse'}<br>
                        ${data.cabinet.descr_cabinet || ''}<br>
                        Tél : ${data.cabinet.tel_cabinet || '0522 000 000'}
                    </div>
                </div>

                <div class="divider"></div>

                <div class="title">
                    ORDONNANCE MEDICALE
                </div>

                <div class="date-location">
                    Fait à : Salé Le
                    <span class="date-input-line">${prescriptionDate.getDate().toString().padStart(2, '0')}</span> /
                    <span class="date-input-line">${(prescriptionDate.getMonth() + 1).toString().padStart(2, '0')}</span> /
                    <span class="date-input-line">${prescriptionDate.getFullYear().toString()}</span>
                </div>

                <div class="patient-name-line">
                    Nom & Prénom : <span class="patient-name-input-line">${data.patient.nom || ''}</span>
                </div>

                <div class="content-area">
                    ${template.logo_file_path ?
                        `<img src="${window.location.origin}/storage/${template.logo_file_path}"  alt="Caduceus Large" class="caduceus-large">` :
                        `<img src="${window.location.origin}/public/uploads/cm_logo_default.png" alt="Caduceus Large" class="caduceus-large">`
                    }
                    <div class="prescription-text">
                        ${data.ordonnance.medicaments || ''}<br>
                        ${data.ordonnance.instructions || ''}<br>
                        Pendant <strong>${data.ordonnance.duree_traitement || ''}</strong>
                    </div>
                </div>

                <div class="footer">
                    Adresse: ${data.cabinet.addr_cabinet || '123, Avenue adresse , ville'} - Tél: ${data.cabinet.tel_cabinet || '0522 000 000 - 0522 000 000'}<br>
                    ${data.medecin.email || 'Adressemail@gmail.com'}
                </div>
            </div>
        </body>
        </html>
    `;

    const printFrame = document.getElementById('printFrame');
    printFrame.contentDocument.open();
    printFrame.contentDocument.write(ordonnanceHTML);
    printFrame.contentDocument.close();

    setTimeout(() => {
        printFrame.contentWindow.print();
    }, 500);
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
                        emptyRow.querySelector('p').textContent = 'Aucune ordonnance trouvée pour cette recherche';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                }

                // Update count
                document.getElementById('documentCount').textContent = `${visibleCount} Ordonnance${visibleCount > 1 ? 's' : ''}`;
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
            const dateInput = document.querySelector('input[name="date_ordonnance"]');
            if (dateInput) {
                dateInput.value = today;
            }
        });
    </script>
</body>
</html>
