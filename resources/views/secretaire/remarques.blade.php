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

        /* Style pour les nouvelles remarques */
        .nouvelle-remarque {
            background-color: #dcfce7 !important;
            border-left: 4px solid #16a34a !important;
            animation: pulseGreen 2s infinite;
        }

        .nouvelle-remarque:hover {
            background-color: #bbf7d0 !important;
        }

        @keyframes pulseGreen {
            0%, 100% {
                background-color: #dcfce7;
            }
            50% {
                background-color: #bbf7d0;
            }
        }

        /* Style pour l'indicateur "Nouveau" */
        .badge-nouveau {
            background-color: #16a34a;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-64 bg-cordes-dark shadow-xl z-50">
        <div class="flex items-center justify-center h-16 bg-cordes-light">
            <div class="flex items-center space-x-3">
                <img style="width: 180px; height:160px" src="{{ url('storage/uploads/logo_miacex.png') }}"/>
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
                        Consultations 
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
                        Paramètres
                    </a>
                @endif
                <a href="{{ route('secretaire.profile') }}"
                    class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-user mr-3 text-cordes-accent"></i>
                    Mon Profil
                </a>
            </div>
        </nav>
        <!-- Section utilisateur avec bouton de déconnexion -->
        <div class="absolute bottom-4 left-4 right-4">
            <div class="bg-gray-800 rounded-lg p-4 group cursor-pointer hover:bg-red-600 transition-colors duration-200">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <div class="flex items-center space-x-3" onclick="document.getElementById('logout-form').submit();">
                        <img src="https://cdn-icons-png.flaticon.com/512/17003/17003310.png" alt="User"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="text-white text-sm font-medium">{{ Auth::user()->nom ?? 'Utilisateur' }}</p>
                            <p class="text-gray-400 text-xs">{{ ucfirst(Auth::user()->role ?? '') }} — <span
                                    class="text-red-400">Se déconnecter</span></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CONTENU PRINCIPAL -->
    <div class="ml-64">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Remarques</h1>
                    <p class="text-gray-600 text-sm mt-1">
                        @if(Auth::user()->role === 'medecin')
                            Mes remarques médicales
                        @elseif(Auth::user()->role === 'secretaire')
                            @if(Auth::user()->medecin_id)
                                Aucune remarque trouvée pour le Dr. {{ optional(Auth::user()->medecin)->nom ?? 'votre médecin' }}
                            @else
                                Vous n'êtes assigné(e) à aucun médecin
                            @endif
                        @endif
                    </p>
                </div>
                @if(Auth::user()->role === 'medecin' || (Auth::user()->role === 'secretaire' && Auth::user()->medecin_id))
                    <button onclick="openGenerateModal()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter Remarque
                    </button>
                @endif
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
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" id="dateFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-sticky-note mr-2 text-purple-600"></i>
                        <span id="documentCount">
                            {{ count($documents ?? []) }} 
                            Remarque{{ count($documents ?? []) > 1 ? 's' : '' }}
                        </span>
                        @if(Auth::user()->role === 'medecin')
                            <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                Mes remarques uniquement
                            </span>
                        @endif
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
                                data-medecin="{{ $doc['medecin_nom'] ?? '' }}"
                                data-date="{{ $doc['date'] ?? '' }}"
                                data-remarque-id="{{ $doc['id'] }}"
                                onclick="marquerRemarqueCommeVue({{ $doc['id'] }})">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_cin'] ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doc['patient_nom'] ?? '' }}
                                    <span class="badge-nouveau" id="badge-{{ $doc['id'] }}" style="display: none;">NOUVEAU</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ Str::limit($doc['remarque'] ?? 'Non spécifié', 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span>Dr. {{ $doc['medecin_nom'] ?? '' }}</span>
                                        @if(Auth::user()->role === 'medecin' && isset($doc['medecin_id']) && $doc['medecin_id'] == Auth::id())
                                            <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Vous</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($doc['date']) ? \Carbon\Carbon::parse($doc['date'])->format('d/m/Y') : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='openViewModal(@json($doc)); event.stopPropagation();'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded hover:bg-blue-50"
                                            title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick='openEditModal(@json($doc)); event.stopPropagation();'
                                            class="text-yellow-600 hover:text-yellow-800 transition-colors p-2 rounded hover:bg-yellow-50"
                                            title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick='openDeleteModal(@json($doc)); event.stopPropagation();'
                                            class="text-red-600 hover:text-red-800 transition-colors p-2 rounded hover:bg-red-50"
                                            title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-sticky-note text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">
                                        @if(Auth::user()->role === 'medecin')
                                            Vous n'avez créé aucune remarque pour le moment
                                        @elseif(Auth::user()->role === 'secretaire')
                                            Aucune remarque trouvée pour votre médecin
                                        @else
                                            Aucune remarque disponible
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-400 mt-1">
                                        @if(Auth::user()->role === 'medecin' || (Auth::user()->role === 'secretaire' && Auth::user()->medecin_id))
                                            Cliquez sur "Ajouter Remarque" pour commencer
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL AJOUTER REMARQUE -->
    @if(Auth::user()->role === 'medecin' || (Auth::user()->role === 'secretaire' && Auth::user()->medecin_id))
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
            
            <form action="{{ route('secretaire.remarques.store') }}" method="POST" class="p-6">
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
                        @if(Auth::user()->role === 'medecin')
                            <input type="text" value="Dr. {{ Auth::user()->nom }}" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed outline-none">
                            <input type="hidden" name="medecin_id" value="{{ Auth::id() }}">
                            <p class="text-xs text-gray-500 mt-1">Vous êtes automatiquement défini comme médecin</p>
                        @elseif(Auth::user()->role === 'secretaire' && Auth::user()->medecin_id)
                            <input type="text" value="Dr. {{ Auth::user()->medecin->nom ?? 'Non défini' }}" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed outline-none">
                            <input type="hidden" name="medecin_id" value="{{ Auth::user()->medecin_id ?? '' }}">
                            <p class="text-xs text-gray-500 mt-1">Remarque créée pour le compte de votre médecin</p>
                        @endif
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
    @endif

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

    <!-- MODAL EDITER REMARQUE -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-edit mr-2 text-yellow-600"></i>Éditer Remarque
                </h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editRemarqueForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_remarque_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Patient
                        </label>
                        <select name="patient_id" id="edit_patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->cin }} - {{ $patient->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-md mr-1"></i>Médecin
                        </label>
                        <input type="text" id="edit_medecin_name" readonly 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed outline-none">
                        <input type="hidden" name="medecin_id" id="edit_medecin_id">
                        <p class="text-xs text-gray-500 mt-1">Le médecin ne peut pas être modifié</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-1"></i>Remarque
                        </label>
                        <textarea name="remarque" id="edit_remarque" rows="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Saisir la remarque médicale..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Date
                        </label>
                        <input type="date" name="date_remarque" id="edit_date_remarque" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SUPPRIMER REMARQUE -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl m-4">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-trash mr-2 text-red-600"></i>Supprimer Remarque
                </h2>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="deleteRemarqueForm" method="POST" class="p-6">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="delete_remarque_id">
                <p class="mb-6 text-gray-700">Êtes-vous sûr de vouloir supprimer cette remarque ? Cette action est irréversible.</p>
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </div>
            </form>
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

        // Clé pour le localStorage
        const STORAGE_KEY = 'remarques_vues';

        function getRemarquesVues() {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        }

        function saveRemarquesVues(remarquesVues) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(remarquesVues));
        }

        function marquerRemarqueCommeVue(remarqueId) {
            const remarquesVues = getRemarquesVues();
            if (!remarquesVues.includes(remarqueId)) {
                remarquesVues.push(remarqueId);
                saveRemarquesVues(remarquesVues);
            }
            
            const row = document.querySelector(`tr[data-remarque-id="${remarqueId}"]`);
            const badge = document.getElementById(`badge-${remarqueId}`);
            
            if (row) {
                row.classList.remove('nouvelle-remarque');
            }
            if (badge) {
                badge.style.display = 'none';
            }
        }

        function marquerNouvellesRemarques() {
            const remarquesVues = getRemarquesVues();
            const rows = document.querySelectorAll('.document-row');
            
            rows.forEach(row => {
                const remarqueId = parseInt(row.getAttribute('data-remarque-id'));
                if (!remarquesVues.includes(remarqueId)) {
                    row.classList.add('nouvelle-remarque');
                    const badge = document.getElementById(`badge-${remarqueId}`);
                    if (badge) {
                        badge.style.display = 'inline';
                    }
                }
            });
        }

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

        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function openViewModal(docData) {
            marquerRemarqueCommeVue(docData.id);
            
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

        function setupSearchAndFilter() {
            const searchInput = document.getElementById('searchInput');
            const dateFilter = document.getElementById('dateFilter');
            const tableRows = document.querySelectorAll('.document-row');
            const emptyRow = document.getElementById('emptyRow');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDate = dateFilter.value;
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const cin = (row.getAttribute('data-cin') || '').toLowerCase();
                    const patientName = (row.getAttribute('data-patient') || '').toLowerCase();
                    const rowDate = row.getAttribute('data-date') || '';
                    
                    const matchesSearch = cin.includes(searchTerm) || patientName.includes(searchTerm);
                    const matchesDate = !selectedDate || rowDate === selectedDate;
                    
                    if (matchesSearch && matchesDate) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyRow) {
                    if (visibleCount === 0 && tableRows.length > 0) {
                        emptyRow.style.display = '';
                        emptyRow.querySelector('p').textContent = 'Aucune remarque trouvée pour cette recherche';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                }

                document.getElementById('documentCount').textContent = `${visibleCount} Remarque${visibleCount > 1 ? 's' : ''}`;
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (dateFilter) dateFilter.addEventListener('change', filterTable);
        }

        function openEditModal(docData) {
            marquerRemarqueCommeVue(docData.id);
            
            document.getElementById('editRemarqueForm').reset();
            
            document.getElementById('edit_remarque_id').value = docData.id || '';
            document.getElementById('edit_medecin_id').value = docData.medecin_id || '';
            document.getElementById('edit_medecin_name').value = 'Dr. ' + (docData.medecin_nom || '');
            document.getElementById('edit_remarque').value = docData.remarque || '';
            document.getElementById('edit_date_remarque').value = docData.date ? new Date(docData.date).toISOString().split('T')[0] : '';
            
            const patientSelect = document.getElementById('edit_patient_id');
            if (patientSelect && docData.patient_id) {
                patientSelect.value = docData.patient_id;
                if (patientSelect.value != docData.patient_id) {
                    for (let i = 0; i < patientSelect.options.length; i++) {
                        if (patientSelect.options[i].value == docData.patient_id) {
                            patientSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                patientSelect.dispatchEvent(new Event('change'));
            }
            
            document.getElementById('editRemarqueForm').action = '/secretaire/remarques/' + (docData.id || '');
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editRemarqueForm').reset();
        }

        function openDeleteModal(docData) {
            marquerRemarqueCommeVue(docData.id);
            
            document.getElementById('delete_remarque_id').value = docData.id || '';
            document.getElementById('deleteRemarqueForm').action = '/secretaire/remarques/' + (docData.id || '');
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Event listeners for modals
        ['generateModal', 'viewModal', 'editModal', 'deleteModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGenerateModal();
                closeViewModal();
                closeEditModal();
                closeDeleteModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
            setupSearchAndFilter();
            marquerNouvellesRemarques();
            
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.querySelector('input[name="date_remarque"]');
            if (dateInput) {
                dateInput.value = today;
            }
        });
    </script>
</body>
</html>