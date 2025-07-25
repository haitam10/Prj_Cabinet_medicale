<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Factures</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        /* Styles pour l'impression */
        @media print {
            body>*:not(#printArea) {
                display: none !important;
            }
            
            #printArea {
                display: block !important;
                width: 100%;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
                color: #333;
            }
            
            #printArea h1,
            #printArea h2,
            #printArea h3 {
                color: #000;
            }
            
            #printArea table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            
            #printArea th,
            #printArea td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            
            #printArea th {
                background-color: #f2f2f2;
            }
            
            #printArea .text-right {
                text-align: right;
            }
            
            #printArea .text-center {
                text-align: center;
            }
            
            /* Masquer les éléments avec la classe no-print */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-64 bg-cordes-dark shadow-xl z-50 no-print">
        <div class="flex items-center justify-center h-16 bg-cordes-blue">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-cube text-cordes-blue text-lg"></i>
                </div>
                <span class="text-white text-xl font-bold">C-M</span>
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
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
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

        <!-- Section utilisateur avec bouton de déconnexion -->
        <div class="absolute bottom-4 left-4 right-4">
            <div
                class="bg-gray-800 rounded-lg p-4 group cursor-pointer hover:bg-red-600 transition-colors duration-200">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <div class="flex items-center space-x-3" onclick="document.getElementById('logout-form').submit();">
                        <img src="https://cdn-icons-png.flaticon.com/512/17003/17003310.png" alt="User"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="text-white text-sm font-medium">{{ Auth::user()->nom ?? 'Utilisateur' }}</p>
                            <p class="text-gray-400 text-xs">{{ ucfirst(Auth::user()->role ?? '') }} — <span
                                    class="text-red-400">Se
                                    déconnecter</span></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CONTENU PRINCIPAL -->
    <div class="ml-64">
        <header class="bg-white shadow-sm border-b border-gray-200 no-print">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Factures</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des factures enregistrées</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter Facture</button>
            </div>
        </header>

        <main class="p-6">
            @if (session('success'))
                <div id="successMessage"
                    class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-opacity duration-500">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div id="errorMessage"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div id="validationErrors"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher une facture..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="statutFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="payée">Payée</option>
                            <option value="en_attente">En attente</option>
                            <option value="annulée">Annulée</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" id="dateFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        <span id="factureCount">{{ $factures->total() }}
                            facture{{ $factures->total() > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Médecin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Créée le</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-print">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="facturesTableBody">
                        @forelse ($factures as $facture)
                            <tr class="hover:bg-gray-50 transition-colors facture-row"
                                data-search="{{ strtolower($facture->patient->nom ?? '') }}"
                                data-statut="{{ $facture->statut }}" data-date="{{ $facture->date }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $facture->patient->nom ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $facture->patient->cin ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $facture->medecin->nom ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($facture->montant, 2) }} DH</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if ($facture->statut === 'payée') bg-green-100 text-green-800 @elseif($facture->statut === 'en_attente') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $facture->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium no-print">
                                    <div class="flex space-x-2">
                                        @if ($facture->statut !== 'payée')
                                            <button onclick='openEditModal(@json($facture))'
                                                class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50"
                                                title="Modifier la facture">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteFacture({{ $facture->id }})"
                                                class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                                title="Supprimer la facture">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 p-1 rounded" title="Facture payée - Non modifiable">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                        
                                        @if ($facture->statut === 'payée')
                                            <button onclick="printFacture({{ $facture->id }})"
                                                class="text-green-600 hover:text-green-800 transition-colors p-1 rounded hover:bg-green-50"
                                                title="Imprimer la facture">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <button onclick="deleteFacture({{ $facture->id }})"
                                                class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                                title="Supprimer la facture">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noFactureRow">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-file-invoice text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucune facture trouvée</p>
                                    <p class="text-sm mt-1">Commencez par ajouter une nouvelle facture.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($factures->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 no-print">
                        {{ $factures->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- MODAL AJOUTER FACTURE -->
    <div id="addModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden no-print">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Ajouter une nouvelle facture</h2>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('factures.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                    <select name="patient_id" id="patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom }} ({{ $patient->cin }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="medecin_id" class="block text-sm font-medium text-gray-700 mb-1">Médecin *</label>
                    <select name="medecin_id" id="medecin_id" required
                        @if(Auth::user()->role === 'medecin') readonly @endif
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un médecin</option>
                        @foreach ($medecins as $medecin)
                            <option value="{{ $medecin->id }}"
                                 @if(Auth::user()->role === 'medecin' && Auth::id() === $medecin->id) selected @endif>
                                {{ $medecin->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="secretaire_id" class="block text-sm font-medium text-gray-700 mb-1">Secrétaire</label>
                    <select name="secretaire_id" id="secretaire_id"
                        @if(Auth::user()->role === 'secretaire') readonly @endif
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un secrétaire</option>
                        @foreach ($secretaires as $secretaire)
                            <option value="{{ $secretaire->id }}"
                                @if(Auth::user()->role === 'secretaire' && Auth::id() === $secretaire->id) selected @endif>
                                {{ $secretaire->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant (DH)
                            *</label>
                        <input type="number" name="montant" id="montant" step="0.01" min="0" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="statut" id="statut" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="">Sélectionner</option>
                            <option value="en_attente">En attente</option>
                            <option value="payée">Payée</option>
                            <option value="annulée">Annulée</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" id="date" required readonly
                        value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                        <i class="fas fa-save mr-2"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL MODIFIER FACTURE -->
    <div id="editModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden no-print">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Modifier la facture</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <div>
                    <label for="edit_patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient
                        *</label>
                    <select name="patient_id" id="edit_patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom }} ({{ $patient->cin }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_medecin_id" class="block text-sm font-medium text-gray-700 mb-1">Médecin
                        *</label>
                    <select name="medecin_id" id="edit_medecin_id" required
                        @if(Auth::user()->role === 'medecin') readonly @endif
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un médecin</option>
                        @foreach ($medecins as $medecin)
                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_secretaire_id"
                        class="block text-sm font-medium text-gray-700 mb-1">Secrétaire</label>
                    <select name="secretaire_id" id="edit_secretaire_id"
                        @if(Auth::user()->role === 'secretaire') readonly @endif
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionner un secrétaire</option>
                        @foreach ($secretaires as $secretaire)
                            <option value="{{ $secretaire->id }}">{{ $secretaire->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_montant" class="block text-sm font-medium text-gray-700 mb-1">Montant (DH)
                            *</label>
                        <input type="number" name="montant" id="edit_montant" step="0.01" min="0"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="edit_statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="statut" id="edit_statut" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="">Sélectionner</option>
                            <option value="en_attente">En attente</option>
                            <option value="payée">Payée</option>
                            <option value="annulée">Annulée</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" id="edit_date" required readonly
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                        <i class="fas fa-save mr-2"></i>Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ZONE D'IMPRESSION (cachée par défaut) -->
    <div id="printArea" style="display: none;">
        <!-- Le contenu de la facture sera injecté ici par JavaScript -->
    </div>

    <script>
        console.log('Script de factures chargé !');

        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fonction pour masquer automatiquement les messages après 5 secondes
        function autoHideMessages() {
            const messages = [
                document.getElementById('successMessage'),
                document.getElementById('errorMessage'),
                document.getElementById('validationErrors')
            ];
            messages.forEach(message => {
                if (message) {
                    // Ajouter un bouton de fermeture
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '<i class="fas fa-times"></i>';
                    closeButton.className =
                        'float-right text-current opacity-70 hover:opacity-100 transition-opacity ml-2';
                    closeButton.onclick = () => hideMessage(message);
                    message.appendChild(closeButton);

                    // Masquer automatiquement après 5 secondes
                    setTimeout(() => {
                        hideMessage(message);
                    }, 5000);
                }
            });
        }

        // Fonction pour masquer un message avec animation
        function hideMessage(messageElement) {
            if (messageElement) {
                messageElement.style.opacity = '0';
                setTimeout(() => {
                    messageElement.style.display = 'none';
                }, 500);
            }
        }

        // Fonction pour afficher un message temporaire
        function showTemporaryMessage(message, type = 'success') {
            const existingTemp = document.querySelector('.temp-message');
            if (existingTemp) {
                existingTemp.remove();
            }
            const messageDiv = document.createElement('div');
            messageDiv.className = `temp-message mb-4 p-4 rounded-lg border transition-opacity duration-500 ${type === 'success'
                    ? 'bg-green-100 text-green-800 border-green-200'
                    : 'bg-red-100 text-red-800 border-red-200'}`;
            messageDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}
                <button onclick="hideMessage(this.parentElement)" class="float-right text-current opacity-70 hover:opacity-100 transition-opacity ml-2">
                    <i class="fas fa-times"></i>
                </button>
            `;
            const main = document.querySelector('main');
            main.insertBefore(messageDiv, main.firstChild);
            setTimeout(() => {
                hideMessage(messageDiv);
            }, 5000);
        }

        // Fonction de recherche et filtrage
        function filterFactures() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statutFilter = document.getElementById('statutFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('.facture-row');
            const noFactureRow = document.getElementById('noFactureRow');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const statutData = row.getAttribute('data-statut');
                const dateData = row.getAttribute('data-date');

                const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                const matchesStatut = !statutFilter || statutData === statutFilter;
                const matchesDate = !dateFilter || dateData === dateFilter;

                if (matchesSearch && matchesStatut && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Afficher/masquer le message "aucune facture"
            if (noFactureRow) {
                noFactureRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            // Mettre à jour le compteur
            document.getElementById('factureCount').textContent = visibleCount + ' facture' + (visibleCount > 1 ? 's' : '');
        }

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.querySelector('#addModal form').reset();

            // Auto-sélection selon le rôle de l'utilisateur connecté
            @if(Auth::user()->role === 'medecin')
                document.getElementById('medecin_id').value = '{{ Auth::id() }}';
                document.getElementById('medecin_id').style.backgroundColor = '#f3f4f6';
                document.getElementById('medecin_id').style.cursor = 'not-allowed';
            @endif

            @if(Auth::user()->role === 'secretaire')
                document.getElementById('secretaire_id').value = '{{ Auth::id() }}';
                document.getElementById('secretaire_id').style.backgroundColor = '#f3f4f6';
                document.getElementById('secretaire_id').style.cursor = 'not-allowed';
            @endif
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(facture) {
            // Vérifier si la facture est payée
            if (facture.statut === 'payée') {
                showTemporaryMessage('Impossible de modifier une facture payée.', 'error');
                return;
            }

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editForm').action = '/factures/' + facture.id;
            document.getElementById('edit_id').value = facture.id;
            document.getElementById('edit_patient_id').value = facture.patient_id;
            document.getElementById('edit_montant').value = facture.montant;
            document.getElementById('edit_statut').value = facture.statut;
            document.getElementById('edit_date').value = facture.date;

            // Gestion des champs médecin et secrétaire selon le rôle
            @if(Auth::user()->role === 'medecin')
                document.getElementById('edit_medecin_id').value = '{{ Auth::id() }}';
                document.getElementById('edit_medecin_id').style.backgroundColor = '#f3f4f6';
                document.getElementById('edit_medecin_id').style.cursor = 'not-allowed';
                document.getElementById('edit_secretaire_id').value = facture.secretaire_id || '';
            @elseif(Auth::user()->role === 'secretaire')
                document.getElementById('edit_medecin_id').value = facture.medecin_id;
                document.getElementById('edit_secretaire_id').value = '{{ Auth::id() }}';
                document.getElementById('edit_secretaire_id').style.backgroundColor = '#f3f4f6';
                document.getElementById('edit_secretaire_id').style.cursor = 'not-allowed';
            @else
                document.getElementById('edit_medecin_id').value = facture.medecin_id;
                document.getElementById('edit_secretaire_id').value = facture.secretaire_id || '';
            @endif
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deleteFacture(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
                fetch('/factures/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            showTemporaryMessage(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else if (data.error) {
                            showTemporaryMessage(data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showTemporaryMessage('Erreur lors de la suppression.', 'error');
                    });
            }
        }

        // Fonction pour imprimer la facture avec données dynamiques
        function printFacture(factureId) {
            console.log('printFacture appelée avec ID:', factureId);
            
            // Appel AJAX pour récupérer les détails complets de la facture
            fetch(`/factures/${factureId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Données de la facture récupérées:', data);
                generateInvoiceContent(data);
            })
            .catch(error => {
                console.error('Erreur lors de la récupération de la facture:', error);
                showTemporaryMessage('Erreur lors de la récupération des données de la facture.', 'error');
            });
        }

        // Fonction pour générer le contenu de la facture avec les données dynamiques
        function generateInvoiceContent(facture) {
            const printArea = document.getElementById('printArea');
            if (!printArea) {
                console.error('Élément de zone d\'impression non trouvé !');
                return;
            }

            // Récupérer les données du cabinet (depuis le blade)
            const cabinetData = @json($cabinet ?? null);
            
            // Utiliser les données du cabinet de la base de données ou des valeurs par défaut
            const cabinetName = cabinetData?.nom_cabinet || 'Cabinet Médical';
            const cabinetAddress = cabinetData?.addr_cabinet || 'Adresse non renseignée';
            const cabinetPhone = cabinetData?.tel_cabinet || 'Téléphone non renseigné';
            const cabinetEmail = 'contact@cabinet.ma'; // Email par défaut

            const patientName = facture.patient?.nom || 'N/A';
            const patientCin = facture.patient?.cin || 'N/A';
            const patientContact = facture.patient?.contact || 'N/A';
            const patientAddress = facture.patient?.adresse || 'N/A';
            
            const medecinName = facture.medecin?.nom || 'N/A';
            const medecinSpecialite = facture.medecin?.specialite || '';
            
            const secretaireName = facture.secretaire?.nom || 'N/A';
            const utilisateurName = facture.utilisateur?.nom || 'N/A';

            const formattedDate = new Date(facture.date).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const createdAt = new Date(facture.created_at).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const invoiceContent = `
                <div style="max-width: 800px; margin: 0 auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h1 style="font-size: 28px; margin-bottom: 5px; color: #1e40af;">Facture Médicale</h1>
                        <p style="font-size: 14px; color: #555;">Facture N° ${facture.id} - Date d'émission: ${formattedDate}</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                        <div>
                            <h2 style="font-size: 18px; margin-bottom: 10px; color: #333;">Informations du Cabinet</h2>
                            <p style="font-size: 14px; line-height: 1.6;">
                                <strong>${cabinetName}</strong><br>
                                ${cabinetAddress}<br>
                                Téléphone: ${cabinetPhone}<br>
                                Email: ${cabinetEmail}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <h2 style="font-size: 18px; margin-bottom: 10px; color: #333;">Détails de la Facture</h2>
                            <p style="font-size: 14px; line-height: 1.6;">
                                Statut: <span style="font-weight: bold; color: green;">${ucfirst(facture.statut.replace('_', ' '))}</span><br>
                                Créée le: ${createdAt}<br>
                                Par: ${utilisateurName}
                            </p>
                        </div>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <h2 style="font-size: 18px; margin-bottom: 10px; color: #333;">Informations du Patient</h2>
                        <p style="font-size: 14px; line-height: 1.6;">
                            <strong>Nom:</strong> ${patientName}<br>
                            <strong>CIN:</strong> ${patientCin}<br>
                            <strong>Contact:</strong> ${patientContact}<br>
                            <strong>Adresse:</strong> ${patientAddress}
                        </p>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <h2 style="font-size: 18px; margin-bottom: 10px; color: #333;">Personnel Médical</h2>
                        <p style="font-size: 14px; line-height: 1.6;">
                            <strong>Médecin Traitant:</strong> ${medecinName}${medecinSpecialite ? ' - ' + medecinSpecialite : ''}<br>
                            <strong>Secrétaire:</strong> ${secretaireName}
                        </p>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <thead>
                            <tr>
                                <th style="padding: 15px; border: 1px solid #ddd; background-color: #f8f9fa; text-align: left; font-size: 14px;">Description</th>
                                <th style="padding: 15px; border: 1px solid #ddd; background-color: #f8f9fa; text-align: center; font-size: 14px;">Quantité</th>
                                <th style="padding: 15px; border: 1px solid #ddd; background-color: #f8f9fa; text-align: right; font-size: 14px;">Montant (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 15px; border: 1px solid #ddd;">Consultation / Prestation Médicale</td>
                                <td style="padding: 15px; border: 1px solid #ddd; text-align: center;">1</td>
                                <td style="padding: 15px; border: 1px solid #ddd; text-align: right;">${number_format(facture.montant, 2)}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="padding: 15px; border: 1px solid #ddd; text-align: right; font-weight: bold; background-color: #f8f9fa;">Total à payer:</td>
                                <td style="padding: 15px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 18px; color: #1e40af; background-color: #f8f9fa;">${number_format(facture.montant, 2)} DH</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div style="text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px;">
                        <p><strong>Merci de votre confiance</strong></p>
                        <p style="margin-top: 10px;">Ce document constitue une facture et fait foi de paiement.</p>
                        <p style="margin-top: 5px;">Pour toute question, n'hésitez pas à nous contacter.</p>
                    </div>
                </div>
            `;
            
            printArea.innerHTML = invoiceContent;
            console.log('Contenu de la zone d\'impression défini.');

            const originalAfterPrint = window.onafterprint;
            window.onafterprint = function() {
                printArea.innerHTML = '';
                window.onafterprint = originalAfterPrint;
                console.log('Zone d\'impression nettoyée après impression.');
            };

            window.print();
            console.log('window.print() appelée.');
        }

        // Helper function for ucfirst (from PHP)
        function ucfirst(str) {
            if (typeof str !== 'string' || str.length === 0) {
                return '';
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Helper function for number_format (from PHP)
        function number_format(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousandsSep === 'undefined') ? ' ' : thousandsSep,
                dec = (typeof decPoint === 'undefined') ? '.' : decPoint,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };

            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // Initialiser le masquage automatique au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
            // Ajouter les événements de filtrage
            document.getElementById('searchInput').addEventListener('input', filterFactures);
            document.getElementById('statutFilter').addEventListener('change', filterFactures);
            document.getElementById('dateFilter').addEventListener('change', filterFactures);

            // Appliquer les styles readonly aux champs selon le rôle
            @if(Auth::user()->role === 'medecin')
                const medecinSelect = document.getElementById('medecin_id');
                if (medecinSelect) {
                    medecinSelect.style.backgroundColor = '#f3f4f6';
                    medecinSelect.style.cursor = 'not-allowed';
                }
                const editMedecinSelect = document.getElementById('edit_medecin_id');
                if (editMedecinSelect) {
                    editMedecinSelect.style.backgroundColor = '#f3f4f6';
                    editMedecinSelect.style.cursor = 'not-allowed';
                }
            @endif

            @if(Auth::user()->role === 'secretaire')
                const secretaireSelect = document.getElementById('secretaire_id');
                if (secretaireSelect) {
                    secretaireSelect.style.backgroundColor = '#f3f4f6';
                    secretaireSelect.style.cursor = 'not-allowed';
                }
                const editSecretaireSelect = document.getElementById('edit_secretaire_id');
                if (editSecretaireSelect) {
                    editSecretaireSelect.style.backgroundColor = '#f3f4f6';
                    editSecretaireSelect.style.cursor = 'not-allowed';
                }
            @endif
        });

        // Fermer les modales en cliquant à l'extérieur
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Fermer les modales avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>
</body>
</html>