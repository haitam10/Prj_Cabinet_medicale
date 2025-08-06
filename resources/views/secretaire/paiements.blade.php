<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Paiements</title>
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
        .modal {
            display: none;
        }

        .modal.show {
            display: flex;
        }

        /* Styles pour les nouveaux paiements */
        .nouveau-paiement {
            background-color: #dcfce7 !important;
            border-left: 4px solid #16a34a !important;
            animation: pulse-green 2s infinite;
        }

        .nouveau-paiement:hover {
            background-color: #bbf7d0 !important;
        }

        @keyframes pulse-green {

            0%,
            100% {
                background-color: #dcfce7;
            }

            50% {
                background-color: #bbf7d0;
            }
        }

        .badge-nouveau {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            background-color: #16a34a;
            color: white;
            font-size: 10px;
            font-weight: bold;
            border-radius: 9999px;
            margin-left: 6px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Styles pour l'historique des paiements */
        .payment-history-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .payment-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            background-color: #f9fafb;
        }

        .payment-card:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-64 bg-cordes-dark shadow-xl z-50">
        <div class="flex items-center justify-center h-16 bg-cordes-light">
            <div class="flex items-center space-x-3">
                <img style="width: 180px; height:160px" src="{{ url('storage/uploads/logo_miacex.png') }}" />
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
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
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
            <div
                class="bg-gray-800 rounded-lg p-4 group cursor-pointer hover:bg-red-600 transition-colors duration-200">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <div class="flex items-center space-x-3" onclick="document.getElementById('logout-form').submit();">
                        <img src="https://cdn-icons-png.flaticon.com/512/17003/17003310.png" alt="User"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="text-white text-sm font-medium">
                                {{ Auth::user()->nom ?? 'Utilisateur' }}
                            </p>
                            <p class="text-gray-400 text-xs">
                                {{ ucfirst(Auth::user()->role ?? '') }} — <span class="text-red-400">Se
                                    déconnecter</span>
                            </p>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Paiements</h1>
                    <p class="text-gray-600 text-sm mt-1">
                        @if(Auth::user()->role === 'medecin')
                            Liste de vos patients avec historique de paiements
                        @elseif(Auth::user()->role === 'secretaire')
                            Liste des patients de {{ Auth::user()->medecin->nom ?? 'votre médecin' }} avec historique de paiements
                        @else
                            Liste des patients avec historique de paiements
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openModal('createPaiementModal')"
                        class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nouveau Paiement
                    </button>
                    <button onclick="openModal('manualAssignmentModal')"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>Affectation Manuelle
                    </button>
                </div>
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

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="statutFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="paye">Payé</option>
                            <option value="en_attente">En attente</option>
                            <option value="echoue">Échoué</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" id="dateFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-users mr-2"></i>
                        <span id="patientCount">{{ count($patientsGroupes) }}
                            patient{{ count($patientsGroupes) > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CIN
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre de Paiements
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Payé
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dernier Paiement
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut Global
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="patientsTableBody">
                        @forelse($patientsGroupes as $patientData)
                            <tr class="hover:bg-gray-50 transition-colors patient-row cursor-pointer"
                                data-search="{{ strtolower($patientData['patient']->nom ?? '') }}"
                                data-statut="{{ $patientData['statut_global'] }}" 
                                data-date="{{ $patientData['dernier_paiement']->date_paiement ?? '' }}"
                                data-patient-id="{{ $patientData['patient']->id }}"
                                data-has-new-payments="{{ $patientData['has_new_payments'] ? '1' : '0' }}"
                                onclick="handlePatientClick(this, {{ $patientData['patient']->id }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $patientData['patient']->nom ?? 'N/A' }}
                                                {{ $patientData['patient']->prenom ?? '' }}
                                                <span class="badge-nouveau nouveau-badge" id="badge-{{ $patientData['patient']->id }}" style="display: none;">
                                                    NOUVEAU
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $patientData['patient']->cin ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $patientData['nombre_paiements'] }} paiement{{ $patientData['nombre_paiements'] > 1 ? 's' : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    {{ number_format($patientData['total_paye'], 2) }} DH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $patientData['dernier_paiement'] ? \Carbon\Carbon::parse($patientData['dernier_paiement']->date_paiement)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patientData['statut_global'] === 'paye')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Tous payés
                                        </span>
                                    @elseif($patientData['statut_global'] === 'mixte')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Mixte
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times mr-1"></i>En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="event.stopPropagation(); showPatientPayments({{ $patientData['patient']->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50"
                                            title="Voir l'historique des paiements">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr id="noPatientRow">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-credit-card text-4xl mb-2 text-gray-300"></i>
                                        <p class="text-lg">Aucun patient avec paiements trouvé</p>
                                        <p class="text-sm mt-1">Commencez par ajouter un nouveau paiement.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <br>
                <br>
                @if (Auth::check() && Auth::user()->role === 'medecin')
                    <!-- Charges Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 style="margin-left:15px" class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">
                                <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>Charges
                            </h3>
                            <button type="button" onclick="openCreateChargeModal()"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Ajouter Charge
                            </button>
                          
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Valeur
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ajouté le
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                           
                                            $chargeIndex = 1;
                                        @endphp

                                        @forelse($charges as $charge)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $chargeIndex++ }}</td>
                                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $charge['type'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $charge['key'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($charge['value'], 2) }} DH</td>
                                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $charge['created_at'])->format('d/m/Y | H:i') }}
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <button type="button" onclick="openEditChargeModal('{{ $charge['id'] }}')"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" onclick="deleteCharge('{{ $charge['id'] }}')"
                                                        class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                    Aucune charge trouvée
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>

        <!-- Modal Créer Paiement -->
        <div id="createPaiementModal"
            class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau paiement</h2>
                    <button onclick="closeModal('createPaiementModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('secretaire.paiements.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="facture_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Facture <span class="text-red-500">*</span>
                        </label>
                        <select id="facture_id" name="facture_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="">Sélectionner une facture</option>
                            @foreach ($factures ?? [] as $facture)
                                <option value="{{ $facture->id }}" data-montant="{{ $facture->montant }}">
                                    Facture - {{ $facture->patient->nom ?? 'N/A' }}
                                    {{ $facture->patient->prenom ?? '' }} ({{ number_format($facture->montant, 2) }} DH)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">
                                Montant (DH) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="montant" name="montant" step="0.01" min="0" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        </div>
                        <div>
                            <label for="date_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                Date de Paiement <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="date_paiement" name="date_paiement" value="{{ date('Y-m-d') }}"
                                required readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent bg-gray-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                Mode de Paiement <span class="text-red-500">*</span>
                            </label>
                            <select id="mode_paiement" name="mode_paiement" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="">Sélectionner un mode</option>
                                <option value="especes">💵 Espèces</option>
                                <option value="carte_bancaire">💳 Carte Bancaire</option>
                                <option value="cheque">📝 Chèque</option>
                                <option value="virement">🏦 Virement</option>
                                <option value="paypal">💻 PayPal</option>
                            </select>
                        </div>
                        <div>
                            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select id="statut" name="statut" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="">Sélectionner un statut</option>
                                <option value="paye">✅ Payé</option>
                                <option value="en_attente">⏳ En attente</option>
                                <option value="echoue">❌ Échoué</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('createPaiementModal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors"
                            onclick="markNewPaymentAdded()">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Affectation Manuelle -->
        <div id="manualAssignmentModal"
            class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Affectation Manuelle - Créer Facture et Paiement</h2>
                    <button onclick="closeModal('manualAssignmentModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('secretaire.paiements.store-manual-assignment') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Section Facture -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">
                            <i class="fas fa-file-invoice-dollar mr-2 text-blue-600"></i>Informations de la Facture
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="manual_patient_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Patient <span class="text-red-500">*</span>
                                </label>
                                <select id="manual_patient_id" name="patient_id" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner un patient</option>
                                    @foreach ($patients ?? [] as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom ?? '' }} ({{ $patient->cin }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="manual_medecin_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Médecin <span class="text-red-500">*</span>
                                </label>
                                <select name="medecin_id" id="manual_medecin_id" required
                                    @if(Auth::user()->role === 'medecin') readonly @endif
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner un médecin</option>
                                    @foreach ($medecins ?? [] as $medecin)
                                        <option value="{{ $medecin->id }}" 
                                            @if(Auth::user()->role === 'medecin' && Auth::id() === $medecin->id) selected @endif>
                                            {{ $medecin->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="manual_secretaire_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Secrétaire
                                </label>
                                <select name="secretaire_id" id="manual_secretaire_id"
                                    @if(Auth::user()->role === 'secretaire') readonly @endif
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner un secrétaire</option>
                                    @foreach ($secretaires ?? [] as $secretaire)
                                        <option value="{{ $secretaire->id }}"
                                            @if(Auth::user()->role === 'secretaire' && Auth::id() === $secretaire->id) selected @endif>
                                            {{ $secretaire->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="manual_facture_statut" class="block text-sm font-medium text-gray-700 mb-1">
                                    Statut Facture <span class="text-red-500">*</span>
                                </label>
                                <select name="facture_statut" id="manual_facture_statut" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="payée">Payée</option>
                                    <option value="annulée">Annulée</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="manual_facture_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date Facture <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="facture_date" id="manual_facture_date" required readonly 
                                    value="{{ date('Y-m-d') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <!-- Section Paiement -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">
                            <i class="fas fa-credit-card mr-2 text-green-600"></i>Informations du Paiement
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="manual_montant" class="block text-sm font-medium text-gray-700 mb-1">
                                    Montant (DH) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="manual_montant" name="montant" step="0.01" min="0" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            </div>

                            <div>
                                <label for="manual_date_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date de Paiement <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="manual_date_paiement" name="date_paiement" value="{{ date('Y-m-d') }}"
                                    required readonly
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent bg-gray-50">
                            </div>

                            <div>
                                <label for="manual_mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mode de Paiement <span class="text-red-500">*</span>
                                </label>
                                <select id="manual_mode_paiement" name="mode_paiement" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner un mode</option>
                                    <option value="especes">💵 Espèces</option>
                                    <option value="carte_bancaire">💳 Carte Bancaire</option>
                                    <option value="cheque">📝 Chèque</option>
                                    <option value="virement">🏦 Virement</option>
                                    <option value="paypal">💻 PayPal</option>
                                </select>
                            </div>

                            <div>
                                <label for="manual_paiement_statut" class="block text-sm font-medium text-gray-700 mb-1">
                                    Statut Paiement <span class="text-red-500">*</span>
                                </label>
                                <select id="manual_paiement_statut" name="paiement_statut" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner un statut</option>
                                    <option value="paye">✅ Payé</option>
                                    <option value="en_attente">⏳ En attente</option>
                                    <option value="echoue">❌ Échoué</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('manualAssignmentModal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            onclick="markNewPaymentAdded()">
                            <i class="fas fa-save mr-2"></i>Créer Facture et Paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Modifier Paiement -->
        <div id="editPaiementModal"
            class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Modifier le paiement</h2>
                    <button onclick="closeModal('editPaiementModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="editPaiementForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_facture_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Facture <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_facture_id" name="facture_id" required disabled
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent bg-gray-50">
                            <option value="">Sélectionner une facture</option>
                            @foreach ($factures ?? [] as $facture)
                                <option value="{{ $facture->id }}" data-montant="{{ $facture->montant }}">
                                    {{ $facture->patient->nom ?? 'N/A' }}
                                    {{ $facture->patient->prenom ?? '' }} ({{ number_format($facture->montant, 2) }} DH)
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="facture_id" id="hidden_edit_facture_id">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_montant" class="block text-sm font-medium text-gray-700 mb-1">
                                Montant (DH) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="edit_montant" name="montant" step="0.01" min="0"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        </div>
                        <div>
                            <label for="edit_date_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                Date de Paiement <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="edit_date_paiement" name="date_paiement" required readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent bg-gray-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                Mode de Paiement <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_mode_paiement" name="mode_paiement" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="">Sélectionner un mode</option>
                                <option value="especes">💵 Espèces</option>
                                <option value="carte_bancaire">💳 Carte Bancaire</option>
                                <option value="cheque">📝 Chèque</option>
                                <option value="virement">🏦 Virement</option>
                                <option value="paypal">💻 PayPal</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_statut" class="block text-sm font-medium text-gray-700 mb-1">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_statut" name="statut" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="">Sélectionner un statut</option>
                                <option value="paye">✅ Payé</option>
                                <option value="en_attente">⏳ En attente</option>
                                <option value="echoue">❌ Échoué</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal('editPaiementModal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Historique des Paiements -->
        <div id="paymentHistoryModal"
            class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white w-full max-w-6xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Historique des Paiements</h2>
                    <button onclick="closeModal('paymentHistoryModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900" id="patient-name"></h3>
                            <p class="text-sm text-gray-600" id="patient-info"></p>
                        </div>
                    </div>
                </div>

                <div id="payments-container" class="space-y-4">
                    <!-- Le contenu sera rempli par JavaScript -->
                </div>

                <div class="flex justify-end pt-4">
                    <button onclick="closeModal('paymentHistoryModal')"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
        @if (Auth::check() && Auth::user()->role === 'medecin')
        <div id="createChargeModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-1/2">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-2xl font-bold text-gray-900">Ajouter une Charge</h3>
                    <button type="button" onclick="closeModal('createChargeModal')" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
                </div>
                <form id="createChargeForm" action="{{ route('secretaire.charges.create') }}" method="POST">
                    @csrf
                    <div id="charge-fields" class="space-y-4">
                        <div class="flex items-center space-x-2 charge-field">
                            <div class="flex-1">
                                <label for="create_charge_type_0" class="block text-sm font-medium text-gray-700 sr-only">Type</label>
                                <select name="charges[0][type]" id="create_charge_type_0" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sélectionner un type</option>
                                    <option value="Cabinet charges">Charges Cabinet</option>
                                    <option value="Salaires">Salaires</option>
                                    <option value="Crédits">Crédits</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <input type="text" name="charges[0][key]" placeholder="Nom de la charge (ex: Loyer)" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                            <input type="number" step="0.01" name="charges[0][value]" placeholder="Valeur" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-4">
                        <button type="button" onclick="addChargeField()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus-circle mr-2"></i>Ajouter un champ
                        </button>
                        <div class="space-x-2">
                            <button type="button" onclick="closeModal('createChargeModal')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Charge Modal -->
        <div id="editChargeModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl w-1/2">
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-2xl font-bold text-gray-900">Modifier la Charge</h3>
                    <button type="button" onclick="closeModal('editChargeModal')" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
                </div>
                <form id="editChargeForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_charge_id">
                    <div class="space-y-4">
                        <div>
                            <label for="edit_charge_type" class="block text-sm font-medium text-gray-700">Type de Charge</label>
                            <select name="type" id="edit_charge_type" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un type</option>
                                <option value="Cabinet charges">Charges Cabinet</option>
                                <option value="Salaires">Salaires</option>
                                <option value="Crédits">Crédits</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_charge_key" class="block text-sm font-medium text-gray-700">Nom de la Charge</label>
                            <input type="text" name="key" id="edit_charge_key" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="edit_charge_value" class="block text-sm font-medium text-gray-700">Valeur</label>
                            <input type="number" step="0.01" name="value" id="edit_charge_value" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('editChargeModal')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <script>
            // Configuration CSRF pour les requêtes AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Clé pour le localStorage - gestion des patients vus
            const STORAGE_KEY = 'paiements_patients_vus';

            // Fonction pour obtenir les patients vus depuis le localStorage
            function getPatientsVus() {
                const stored = localStorage.getItem(STORAGE_KEY);
                return stored ? JSON.parse(stored) : [];
            }

            // Fonction pour sauvegarder les patients vus dans le localStorage
            function savePatientsVus(patientsVus) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(patientsVus));
            }

            // Fonction pour marquer un patient comme vu
            function marquerPatientCommeVu(patientId) {
                const patientsVus = getPatientsVus();
                if (!patientsVus.includes(patientId)) {
                    patientsVus.push(patientId);
                    savePatientsVus(patientsVus);
                }
                
                // Retirer les classes et badges de "nouveau"
                const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
                const badge = document.getElementById(`badge-${patientId}`);
                
                if (row) {
                    row.classList.remove('nouveau-paiement');
                }
                if (badge) {
                    badge.style.display = 'none';
                }
            }

            // Fonction pour marquer les nouveaux patients au chargement de la page
            function marquerNouveauxPatients() {
                const patientsVus = getPatientsVus();
                const rows = document.querySelectorAll('.patient-row');
                
                rows.forEach(row => {
                    const patientId = parseInt(row.getAttribute('data-patient-id'));
                    const hasNewPayments = row.getAttribute('data-has-new-payments') === '1';
                    
                    // Si le patient a de nouveaux paiements ET n'a pas été vu
                    if (hasNewPayments && !patientsVus.includes(patientId)) {
                        // Marquer comme nouveau patient
                        row.classList.add('nouveau-paiement');
                        const badge = document.getElementById(`badge-${patientId}`);
                        if (badge) {
                            badge.style.display = 'inline';
                        }
                    }
                });
            }

            // Fonction pour marquer qu'un nouveau paiement a été ajouté
            function markNewPaymentAdded() {
                // Vider le localStorage pour forcer l'affichage des nouveaux paiements
                localStorage.removeItem(STORAGE_KEY);
            }

            // Fonction pour gérer le clic sur un patient
            function handlePatientClick(row, patientId) {
                // Marquer le patient comme vu
                marquerPatientCommeVu(patientId);
                
                // Afficher l'historique des paiements
                showPatientPayments(patientId);
            }

            // Fonctions pour gérer les modals
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Fonction de recherche et filtrage
            function filterPatients() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const statutFilter = document.getElementById('statutFilter').value;
                const dateFilter = document.getElementById('dateFilter').value;
                const rows = document.querySelectorAll('.patient-row');
                const noPatientRow = document.getElementById('noPatientRow');
                let visibleCount = 0;

                rows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    const statutData = row.getAttribute('data-statut');
                    const dateData = row.getAttribute('data-date');

                    const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                    const matchesStatut = !statutFilter || statutData === statutFilter || 
                                        (statutFilter === 'paye' && statutData === 'paye') ||
                                        (statutFilter === 'en_attente' && (statutData === 'en_attente' || statutData === 'mixte'));
                    const matchesDate = !dateFilter || dateData === dateFilter;

                    if (matchesSearch && matchesStatut && matchesDate) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Afficher/masquer le message "aucun patient"
                if (noPatientRow) {
                    noPatientRow.style.display = visibleCount === 0 ? '' : 'none';
                }

                // Mettre à jour le compteur
                document.getElementById('patientCount').textContent = visibleCount + ' patient' + (visibleCount > 1 ? 's' : '');
            }

            // Fonction pour afficher l'historique des paiements d'un patient
            function showPatientPayments(patientId) {
                fetch(`/secretaire/patients/${patientId}/payment-history`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Remplir les informations du patient
                        document.getElementById('patient-name').textContent = 
                            `${data.patient.nom} ${data.patient.prenom || ''}`;
                        document.getElementById('patient-info').textContent = 
                            `CIN: ${data.patient.cin || 'N/A'} • ${data.payments.length} paiement${data.payments.length > 1 ? 's' : ''}`;

                        // Créer le contenu de l'historique
                        const paymentsContainer = document.getElementById('payments-container');
                        paymentsContainer.innerHTML = '';

                        if (data.payments.length === 0) {
                            paymentsContainer.innerHTML = `
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-credit-card text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucun paiement trouvé pour ce patient</p>
                                </div>
                            `;
                        } else {
                            // Organiser les paiements par groupes de 3
                            for (let i = 0; i < data.payments.length; i += 3) {
                                const paymentsGroup = data.payments.slice(i, i + 3);
                                const groupDiv = document.createElement('div');
                                groupDiv.className = 'payment-history-grid';

                                paymentsGroup.forEach((payment, index) => {
                                    const paymentCard = document.createElement('div');
                                    paymentCard.className = 'payment-card';
                                    
                                    const statutBadge = getStatutBadge(payment.statut);
                                    const modeBadge = getModeIcon(payment.mode_paiement);
                                    const dateFormatted = new Date(payment.date_paiement).toLocaleDateString('fr-FR');
                                    
                                    // Utiliser un numéro séquentiel au lieu de l'ID
                                    const paymentNumber = i + index + 1;

                                    paymentCard.innerHTML = `
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-medium text-gray-900">Paiement #${paymentNumber}</h4>
                                            ${statutBadge}
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Montant:</span>
                                                <span class="text-sm font-medium text-green-600">${parseFloat(payment.montant).toFixed(2)} DH</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Date:</span>
                                                <span class="text-sm text-gray-900">${dateFormatted}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Mode:</span>
                                                <div class="text-xs">${modeBadge}</div>
                                            </div>
                                            <div class="flex justify-end space-x-2 mt-3 pt-2 border-t border-gray-200">
                                                ${payment.statut !== 'paye' ? `
                                                    <button onclick="editPaymentFromHistory(${payment.id})" 
                                                        class="text-yellow-600 hover:text-yellow-800 transition-colors p-1 rounded hover:bg-yellow-50"
                                                        title="Modifier">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                ` : ''}
                                                <button onclick="deletePaymentFromHistory(${payment.id})" 
                                                    class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                    
                                    groupDiv.appendChild(paymentCard);
                                });

                                paymentsContainer.appendChild(groupDiv);
                            }
                        }

                        openModal('paymentHistoryModal');
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement de l\'historique des paiements');
                    });
            }

            // Fonction pour modifier un paiement depuis l'historique
            function editPaymentFromHistory(paymentId) {
                closeModal('paymentHistoryModal');
                editPaiement(paymentId);
            }

            // Fonction pour supprimer un paiement depuis l'historique
            function deletePaymentFromHistory(paymentId) {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
                    deletePaiement(paymentId);
                    closeModal('paymentHistoryModal');
                }
            }

            // Fonction pour modifier un paiement
            function editPaiement(id) {
                fetch(`/paiements/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Vérifier si le paiement est payé
                        if (data.statut === 'paye') {
                            alert('Impossible de modifier un paiement payé.');
                            return;
                        }

                        document.getElementById('editPaiementForm').action = `/paiements/${id}`;
                        document.getElementById('edit_facture_id').value = data.facture_id;
                        document.getElementById('hidden_edit_facture_id').value = data.facture_id;
                        document.getElementById('edit_montant').value = data.montant;
                        document.getElementById('edit_date_paiement').value = data.date_paiement;
                        document.getElementById('edit_mode_paiement').value = data.mode_paiement;
                        document.getElementById('edit_statut').value = data.statut;
                        openModal('editPaiementModal');
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des données du paiement');
                    });
            }

            // Fonction pour supprimer un paiement
            function deletePaiement(id) {
                fetch('/paiements/' + id, {
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
                            alert(data.message);
                            window.location.reload();
                        } else if (data.error) {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suppression.');
                    });
            }

            // Fonction pour générer le badge de statut
            function getStatutBadge(statut) {
                switch (statut) {
                    case 'paye':
                    case 'payé':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Payé</span>';
                    case 'en_attente':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>En attente</span>';
                    case 'echoue':
                    case 'échoué':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Échoué</span>';
                    default:
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${statut}</span>`;
                }
            }

            // Fonction pour générer l'icône du mode de paiement
            function getModeIcon(mode) {
                switch (mode) {
                    case 'especes':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-money-bills mr-1"></i>Espèces</span>';
                    case 'carte_bancaire':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-credit-card mr-1"></i>Carte</span>';
                    case 'cheque':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-money-check mr-1"></i>Chèque</span>';
                    case 'virement':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-university mr-1"></i>Virement</span>';
                    case 'paypal':
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><i class="fab fa-paypal mr-1"></i>PayPal</span>';
                    default:
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${mode}</span>`;
                }
            }


                        function openCreateChargeModal() {
                const chargeFieldsContainer = document.getElementById('charge-fields');
                chargeFieldsContainer.innerHTML = `
                    <div class="flex items-center space-x-2 charge-field">
                        <div class="flex-1">
                            <label for="create_charge_type_0" class="block text-sm font-medium text-gray-700 sr-only">Type</label>
                            <select name="charges[0][type]" id="create_charge_type_0" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un type</option>
                                <option value="Cabinet charges">Charges Cabinet</option>
                                <option value="Salaires">Salaires</option>
                                <option value="Crédits">Crédits</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <input type="text" name="charges[0][key]" placeholder="Nom de la charge (ex: Loyer)" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                        <input type="number" step="0.01" name="charges[0][value]" placeholder="Valeur" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                    </div>
                `;
                // Reset chargeFieldIndex for new modal opening
                chargeFieldIndex = 1;
                openModal('createChargeModal');
            }

            let chargeFieldIndex = 1; // Keep this global or accessible
            function addChargeField() {
                const chargeFieldsContainer = document.getElementById('charge-fields');
                const newField = document.createElement('div');
                newField.className = 'flex items-center space-x-2 charge-field';
                newField.innerHTML = `
                    <div class="flex-1">
                        <label for="create_charge_type_${chargeFieldIndex}" class="block text-sm font-medium text-gray-700 sr-only">Type</label>
                        <select name="charges[${chargeFieldIndex}][type]" id="create_charge_type_${chargeFieldIndex}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionner un type</option>
                            <option value="Cabinet charges">Charges Cabinet</option>
                            <option value="Salaires">Salaires</option>
                            <option value="Crédits">Crédits</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <input type="text" name="charges[${chargeFieldIndex}][key]" placeholder="Nom de la charge (ex: Loyer)" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                    <input type="number" step="0.01" name="charges[${chargeFieldIndex}][value]" placeholder="Valeur" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-cordes-blue focus:border-cordes-blue">
                    <button type="button" onclick="removeChargeField(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-minus-circle"></i></button>
                `;
                chargeFieldsContainer.appendChild(newField);
                chargeFieldIndex++;
            }

            function removeChargeField(button) {
                const field = button.closest('.charge-field');
                field.remove();
            }

            function openEditChargeModal(id) {
                fetch(`/secretaire/charges/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Charge non trouvée ou erreur de serveur.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('editChargeForm').action = `/secretaire/charges/${id}`;
                        document.getElementById('edit_charge_id').value = data.id;
                        document.getElementById('edit_charge_type').value = data.type; // Set the type dropdown value
                        document.getElementById('edit_charge_key').value = data.key;
                        document.getElementById('edit_charge_value').value = data.value;
                        openModal('editChargeModal');
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des données de la charge:', error);
                        // Use a custom message box instead of alert()
                        alert('Erreur lors du chargement des données de la charge.'); // Temporary: Replace with custom message box
                    });
            }

            function deleteCharge(id) {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')) {
                    fetch(`/secretaire/charges/${id}`, {
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
                                alert(data.message);
                                window.location.reload();
                            } else if (data.error) {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la suppression de la charge.');
                        });
                }
            }

            // Auto-remplir le montant basé sur la facture sélectionnée
            document.getElementById('facture_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.dataset.montant) {
                    document.getElementById('montant').value = selectedOption.dataset.montant;
                }
            });

            document.getElementById('edit_facture_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.dataset.montant) {
                    document.getElementById('edit_montant').value = selectedOption.dataset.montant;
                }
            });

            // Initialiser les événements au chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                // Marquer les nouveaux patients au chargement
                marquerNouveauxPatients();

                // Ajouter les événements de filtrage
                document.getElementById('searchInput').addEventListener('input', filterPatients);
                document.getElementById('statutFilter').addEventListener('change', filterPatients);
                document.getElementById('dateFilter').addEventListener('change', filterPatients);

                // Auto-dismiss messages after 5 seconds
                setTimeout(function() {
                    const messages = document.querySelectorAll('#successMessage, #errorMessage');
                    messages.forEach(function(message) {
                        if (message) {
                            message.style.display = 'none';
                        }
                    });
                }, 5000);

                // Configuration des champs selon le rôle
                @if(Auth::user()->role === 'medecin')
                    const medecinSelect = document.getElementById('manual_medecin_id');
                    if (medecinSelect) {
                        medecinSelect.style.backgroundColor = '#f3f4f6';
                        medecinSelect.style.cursor = 'not-allowed';
                    }
                @endif

                @if(Auth::user()->role === 'secretaire')
                    const secretaireSelect = document.getElementById('manual_secretaire_id');
                    if (secretaireSelect) {
                        secretaireSelect.style.backgroundColor = '#f3f4f6';
                        secretaireSelect.style.cursor = 'not-allowed';
                    }
                @endif
            });

            // Fermer les modales en cliquant à l'extérieur
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    closeModal(e.target.id);
                }
            });

            // Fermer les modales avec la touche Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal('createPaiementModal');
                    closeModal('editPaiementModal');
                    closeModal('paymentHistoryModal');
                    closeModal('manualAssignmentModal');
                }
            });
        </script>
    </body>

</html>