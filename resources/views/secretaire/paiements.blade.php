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
                        Dossier Médical
                    </a>
                    <a href="{{ route('secretaire.calendrier') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-calendar-alt mr-3 text-gray-400 group-hover:text-white"></i>
                        Calendrier
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
                    <p class="text-gray-600 text-sm mt-1">Liste des paiements enregistrés</p>
                </div>
                <button onclick="openModal('createPaiementModal')"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nouveau Paiement
                </button>
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
                        <input type="text" id="searchInput" placeholder="Rechercher un paiement..."
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
                        <i class="fas fa-credit-card mr-2"></i>
                        <span id="paiementCount">{{ $paiements->total() ?? 0 }}
                            paiement{{ ($paiements->total() ?? 0) > 1 ? 's' : '' }}</span>
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
                                Montant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="paiementsTableBody">
                        @forelse($paiements as $paiement)
                            <tr class="hover:bg-gray-50 transition-colors paiement-row"
                                data-search="{{ strtolower($paiement->facture->patient->nom ?? '') }}"
                                data-statut="{{ $paiement->statut }}" data-date="{{ $paiement->date_paiement }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $paiement->facture->patient->nom ?? 'N/A' }}
                                                {{ $paiement->facture->patient->prenom ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $paiement->facture->patient->cin ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($paiement->montant, 2) }} DH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($paiement->mode_paiement)
                                        @case('especes')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-money-bills mr-1"></i>Espèces
                                            </span>
                                        @break

                                        @case('carte_bancaire')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-credit-card mr-1"></i>Carte
                                            </span>
                                        @break

                                        @case('cheque')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-money-check mr-1"></i>Chèque
                                            </span>
                                        @break

                                        @case('virement')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-university mr-1"></i>Virement
                                            </span>
                                        @break

                                        @case('paypal')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fab fa-paypal mr-1"></i>PayPal
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($paiement->mode_paiement) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if ($paiement->statut === 'paye') bg-green-100 text-green-800 @elseif($paiement->statut === 'en_attente') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                        @switch($paiement->statut)
                                            @case('paye')
                                                <i class="fas fa-check mr-1"></i>Payé
                                            @break

                                            @case('en_attente')
                                                <i class="fas fa-clock mr-1"></i>En attente
                                            @break

                                            @case('echoue')
                                                <i class="fas fa-times mr-1"></i>Échoué
                                            @break

                                            @default
                                                {{ ucfirst($paiement->statut) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showPaiement({{ $paiement->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50"
                                            title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if ($paiement->statut !== 'paye')
                                            <button onclick="editPaiement({{ $paiement->id }})"
                                                class="text-yellow-600 hover:text-yellow-800 transition-colors p-1 rounded hover:bg-yellow-50"
                                                title="Modifier le paiement">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deletePaiement({{ $paiement->id }})"
                                                class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                                title="Supprimer le paiement">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 p-1 rounded"
                                                title="Paiement payé - Non modifiable">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <button onclick="deletePaiement({{ $paiement->id }})"
                                                class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                                title="Supprimer le paiement">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr id="noPaiementRow">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-credit-card text-4xl mb-2 text-gray-300"></i>
                                        <p class="text-lg">Aucun paiement trouvé</p>
                                        <p class="text-sm mt-1">Commencez par ajouter un nouveau paiement.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($paiements->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $paiements->links() }}
                        </div>
                    @endif
                </div>
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
                <form method="POST" action="{{ route('paiements.store') }}" class="space-y-4">
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
                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer
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
                                    Facture #{{ $facture->id }} - {{ $facture->patient->nom ?? 'N/A' }}
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

        <!-- Modal Voir Paiement -->
        <div id="showPaiementModal"
            class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Détails du paiement</h2>
                    <button onclick="closeModal('showPaiementModal')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Patient:</label>
                            <p class="text-sm text-gray-900" id="show_patient"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CIN:</label>
                            <p class="text-sm text-gray-900" id="show_cin"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant:</label>
                            <p class="text-lg font-bold text-green-600" id="show_montant"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de Paiement:</label>
                            <p class="text-sm text-gray-900" id="show_date"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mode de Paiement:</label>
                            <p class="text-sm text-gray-900" id="show_mode"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut:</label>
                            <p class="text-sm text-gray-900" id="show_statut"></p>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button onclick="closeModal('showPaiementModal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Configuration CSRF pour les requêtes AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
            function filterPaiements() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const statutFilter = document.getElementById('statutFilter').value;
                const dateFilter = document.getElementById('dateFilter').value;
                const rows = document.querySelectorAll('.paiement-row');
                const noPaiementRow = document.getElementById('noPaiementRow');
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

                // Afficher/masquer le message "aucun paiement"
                if (noPaiementRow) {
                    noPaiementRow.style.display = visibleCount === 0 ? '' : 'none';
                }

                // Mettre à jour le compteur
                document.getElementById('paiementCount').textContent = visibleCount + ' paiement' + (visibleCount > 1 ? 's' :
                    '');
            }

            // Fonction pour afficher les détails d'un paiement
            function showPaiement(id) {
                fetch(`/paiements/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('show_patient').textContent = data.facture && data.facture.patient ?
                            `${data.facture.patient.nom} ${data.facture.patient.prenom}` : 'N/A';
                        document.getElementById('show_cin').textContent = data.facture && data.facture.patient ?
                            data.facture.patient.cin : 'N/A';
                        document.getElementById('show_montant').textContent = `${parseFloat(data.montant).toFixed(2)} DH`;
                        document.getElementById('show_date').textContent = new Date(data.date_paiement).toLocaleDateString(
                            'fr-FR');
                        document.getElementById('show_mode').innerHTML = getModeIcon(data.mode_paiement);
                        document.getElementById('show_statut').innerHTML = getStatutBadge(data.statut);
                        openModal('showPaiementModal');
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des détails du paiement');
                    });
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
                if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
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
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-credit-card mr-1"></i>Carte Bancaire</span>';
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
                // Ajouter les événements de filtrage
                document.getElementById('searchInput').addEventListener('input', filterPaiements);
                document.getElementById('statutFilter').addEventListener('change', filterPaiements);
                document.getElementById('dateFilter').addEventListener('change', filterPaiements);

                // Auto-dismiss messages after 5 seconds
                setTimeout(function() {
                    const messages = document.querySelectorAll('#successMessage, #errorMessage');
                    messages.forEach(function(message) {
                        if (message) {
                            message.style.display = 'none';
                        }
                    });
                }, 5000);
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
                    closeModal('showPaiementModal');
                }
            });
        </script>
    </body>

    </html>
