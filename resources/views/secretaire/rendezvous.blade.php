<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Rendez-vous</title>
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
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Rendez-vous</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des rendez-vous enregistrés</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter RDV
                </button>
            </div>
        </header>

        <main class="p-6">
            <!-- ZONE DES MESSAGES - Système amélioré et corrigé -->
            <div id="messages-container" class="space-y-4 mb-6">
                <!-- Messages de succès -->
                @if (session('success'))
                    <div id="successMessage"
                        class="alert-message p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-all duration-500 opacity-100 transform translate-y-0">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="font-medium">{{ session('success') }}</p>
                            </div>
                            <button onclick="closeMessage('successMessage')"
                                class="ml-4 text-green-600 hover:text-green-800 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Messages d'erreur -->
                @if (session('error'))
                    <div id="errorMessage"
                        class="alert-message p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-all duration-500 opacity-100 transform translate-y-0">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="font-medium">{{ session('error') }}</p>
                            </div>
                            <button onclick="closeMessage('errorMessage')"
                                class="ml-4 text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Erreurs de validation -->
                @if ($errors->any())
                    <div id="validationErrors"
                        class="alert-message p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-all duration-500 opacity-100 transform translate-y-0">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                @if ($errors->count() == 1)
                                    <p class="font-medium">{{ $errors->first() }}</p>
                                @else
                                    <p class="font-medium mb-2">Erreurs de validation :</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <button onclick="closeMessage('validationErrors')"
                                class="ml-4 text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un rendez-vous..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="statutFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="confirmé">Confirmé</option>
                            <option value="en attente">En attente</option>
                            <option value="annulé">Annulé</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i
                            class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" id="dateFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <span id="rdvCount">{{ $latest_rvs->total() }} rendez-vous au total</span>
                    </div>
                </div>
            </div>

            <!-- Tableau des rendez-vous -->
            <div class="overflow-x-auto bg-white shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Médecin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="rdvsTableBody">
                        @forelse ($latest_rvs as $rdv)
                            <tr class="hover:bg-gray-50 transition-colors rdv-row"
                                data-search="{{ strtolower($rdv->patient->nom ?? '') }} {{ strtolower($rdv->patient->prenom ?? '') }}"
                                data-statut="{{ $rdv->statut }}"
                                data-date="{{ \Carbon\Carbon::parse($rdv->date)->format('Y-m-d') }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($rdv->date)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->medecin->nom ?? 'Inconnu' }} {{ $rdv->medecin->prenom ?? '' }}
                                    @if ($rdv->medecin && isset($rdv->medecin->specialite))
                                        <div class="text-xs text-gray-500">{{ $rdv->medecin->specialite }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->patient->nom ?? 'Inconnu' }} {{ $rdv->patient->prenom ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($rdv->statut == 'confirmé')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Confirmé
                                        </span>
                                    @elseif($rdv->statut == 'en attente')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>En attente
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $rdv->motif ?? 'Aucun motif' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='openEditModal(@json($rdv))'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRendezVous({{ $rdv->id }})"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noRdvRow">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300"></i>
                                    <p>Aucun rendez-vous trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($latest_rvs->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $latest_rvs->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- MODAL AJOUTER RDV -->
    <div id="addModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Ajouter un rendez-vous</h2>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="{{ route('rendezvous.store') }}" method="POST" class="space-y-4" id="addForm">
                @csrf
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                    <select name="patient_id" id="patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionnez un patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                      <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                            <input type="hidden" name="medecin_id" value="{{ Auth::id() }}">
                            <input type="text" value="Dr. {{ Auth::user()->nom ?? 'Médecin' }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed outline-none">
                        </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" id="date" required min="{{ date('Y-m-d') }}"
                            value="{{ old('date') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="heure" class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
                        <input type="time" name="heure" id="heure" required value="{{ old('heure') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="en attente" {{ old('statut') == 'en attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="confirmé" {{ old('statut') == 'confirmé' ? 'selected' : '' }}>Confirmé</option>
                        <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <textarea name="motif" id="motif" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Décrivez le motif du rendez-vous...">{{ old('motif') }}</textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()"
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

    <!-- MODAL MODIFIER RDV -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Modifier le rendez-vous</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label for="edit_patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                    <select name="patient_id" id="edit_patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionnez un patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_medecin_id" class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                    <select name="medecin_id" id="edit_medecin_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionnez un médecin</option>
                        @foreach ($medecins as $medecin)
                            <option value="{{ $medecin->id }}">{{ $medecin->nom }} {{ $medecin->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" id="edit_date" required min="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="edit_heure" class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
                        <input type="time" name="heure" id="edit_heure" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                </div>
                <div>
                    <label for="edit_statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="edit_statut" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="en attente">En attente</option>
                        <option value="confirmé">Confirmé</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div>
                    <label for="edit_motif" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <textarea name="motif" id="edit_motif" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Décrivez le motif du rendez-vous..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
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

    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Variables globales
        let hasValidationErrors = false;
        let hasSessionError = false;

        // Fonction pour obtenir la date et l'heure actuelles
        function getCurrentDateTime() {
            const now = new Date();
            return {
                date: now.toISOString().split('T')[0],
                time: now.toTimeString().substring(0, 5)
            };
        }

        // Fonction pour valider la date et l'heure
        function validateDateTime(dateInput, timeInput) {
            const selectedDate = dateInput.value;
            const selectedTime = timeInput.value;
            const currentDateTime = getCurrentDateTime();

            if (!selectedDate || !selectedTime) {
                return {
                    valid: false,
                    message: 'Veuillez sélectionner une date et une heure.'
                };
            }

            // Créer des objets Date pour la comparaison
            const selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
            const currentDateTimeObj = new Date();

            if (selectedDateTime <= currentDateTimeObj) {
                return {
                    valid: false,
                    message: 'La date et l\'heure du rendez-vous doivent être postérieures à maintenant.'
                };
            }

            return {
                valid: true
            };
        }

        // Fonction pour afficher les erreurs de validation
        function showValidationError(message) {
            const existingError = document.getElementById('datetime-error');
            if (existingError) {
                existingError.remove();
            }

            const errorDiv = document.createElement('div');
            errorDiv.id = 'datetime-error';
            errorDiv.className = 'p-3 bg-red-100 border border-red-400 text-red-700 rounded mb-4';
            errorDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>${message}</span>
            </div>
        `;

            const form = document.getElementById('addForm');
            form.insertBefore(errorDiv, form.firstChild);

            // Faire défiler vers l'erreur
            errorDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Supprimer l'erreur après 5 secondes
            setTimeout(() => {
                if (errorDiv && errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        }

        // Fonction pour fermer un message spécifique
        function closeMessage(messageId) {
            const message = document.getElementById(messageId);
            if (message) {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    message.remove();
                }, 300);
            }
        }

        // Fonction pour afficher un message temporaire (AJAX)
        function showMessage(text, type = 'success') {
            // Supprimer les anciens messages temporaires
            const existingTemp = document.querySelector('.temp-message');
            if (existingTemp) {
                existingTemp.remove();
            }

            const messageContainer = document.getElementById('messages-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `temp-message p-4 rounded-lg border transition-all duration-500 opacity-0 transform translate-y-2 ${
type === 'success' 
                    ? 'bg-green-100 text-green-800 border-green-200' 
                    : 'bg-red-100 text-red-800 border-red-200'
}`;

            messageDiv.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-${type === 'success' ? 'green' : 'red'}-600 text-lg"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-medium">${text}</p>
                    </div>
                    <button onclick="this.closest('.temp-message').remove()" class="ml-4 text-${type === 'success' ? 'green' : 'red'}-600 hover:text-${type === 'success' ? 'green' : 'red'}-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            messageContainer.appendChild(messageDiv);

            // Animation d'apparition
            requestAnimationFrame(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
            });

            // Scroll vers le message
            messageDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Auto-fermeture après 5 secondes
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.style.opacity = '0';
                    messageDiv.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        if (messageDiv.parentNode) {
                            messageDiv.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Fonction pour l'auto-fermeture des messages
        function setupAutoCloseMessages() {
            const messages = document.querySelectorAll('.alert-message');
            messages.forEach(message => {
                // Scroll vers le message si c'est un message d'erreur
                if (message.classList.contains('bg-red-100')) {
                    message.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.opacity = '0';
                        message.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            if (message.parentNode) {
                                message.remove();
                            }
                        }, 300);
                    }
                }, 8000);
            });
        }

        // Fonction de recherche et filtrage
        function filterRendezVous() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statutFilter = document.getElementById('statutFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;

            const rows = document.querySelectorAll('.rdv-row');
            const noRdvRow = document.getElementById('noRdvRow');
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

            if (noRdvRow) {
                noRdvRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            document.getElementById('rdvCount').textContent =
                `${visibleCount} rendez-vous${visibleCount !== 1 ? '' : ''} trouvé${visibleCount !== 1 ? 's' : ''}`;
        }

        // Fonctions pour les modales
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            // Supprimer les erreurs précédentes
            const existingError = document.getElementById('datetime-error');
            if (existingError) {
                existingError.remove();
            }

            // Scroll vers le haut pour voir les messages
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            // Supprimer les erreurs de validation
            const existingError = document.getElementById('datetime-error');
            if (existingError) {
                existingError.remove();
            }
        }

        function openEditModal(rdv) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editForm').action = `{{ url('/rendezvous') }}/${rdv.id}`;
            document.getElementById('edit_id').value = rdv.id;
            document.getElementById('edit_patient_id').value = rdv.patient_id;
            document.getElementById('edit_medecin_id').value = rdv.medecin_id;

            const dateTime = new Date(rdv.date);
            const datePart = dateTime.toISOString().substring(0, 10);
            const timePart = dateTime.toTimeString().substring(0, 5);
            document.getElementById('edit_date').value = datePart;
            document.getElementById('edit_heure').value = timePart;
            document.getElementById('edit_statut').value = rdv.statut;
            document.getElementById('edit_motif').value = rdv.motif || '';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deleteRendezVous(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
                fetch(`{{ url('/rendezvous') }}/${id}`, {
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
                            showMessage(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else if (data.error) {
                            showMessage(data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showMessage('Erreur lors de la suppression.', 'error');
                    });
            }
        }

        // Validation en temps réel pour les champs de date et heure
        function setupDateTimeValidation() {
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('heure');
            const editDateInput = document.getElementById('edit_date');
            const editTimeInput = document.getElementById('edit_heure');

            // Fonction pour valider en temps réel
            function validateRealTime(dateField, timeField) {
                const validation = validateDateTime(dateField, timeField);
                if (!validation.valid && dateField.value && timeField.value) {
                    dateField.setCustomValidity(validation.message);
                    timeField.setCustomValidity(validation.message);
                } else {
                    dateField.setCustomValidity('');
                    timeField.setCustomValidity('');
                }
            }

            // Événements pour le modal d'ajout
            if (dateInput && timeInput) {
                dateInput.addEventListener('change', () => validateRealTime(dateInput, timeInput));
                timeInput.addEventListener('change', () => validateRealTime(dateInput, timeInput));
            }

            // Événements pour le modal d'édition
            if (editDateInput && editTimeInput) {
                editDateInput.addEventListener('change', () => validateRealTime(editDateInput, editTimeInput));
                editTimeInput.addEventListener('change', () => validateRealTime(editDateInput, editTimeInput));
            }
        }

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé');

            // Configuration de l'auto-fermeture des messages
            setupAutoCloseMessages();

            // Configuration de la validation date/heure
            setupDateTimeValidation();

            // Événements de filtrage
            document.getElementById('searchInput').addEventListener('input', filterRendezVous);
            document.getElementById('statutFilter').addEventListener('change', filterRendezVous);
            document.getElementById('dateFilter').addEventListener('change', filterRendezVous);

            // Validation du formulaire d'ajout
            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    const dateInput = document.getElementById('date');
                    const timeInput = document.getElementById('heure');
                    const validation = validateDateTime(dateInput, timeInput);

                    if (!validation.valid) {
                        e.preventDefault();
                        showValidationError(validation.message);
                        return false;
                    }
                });
            }

            // Validation du formulaire d'édition
            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    const dateInput = document.getElementById('edit_date');
                    const timeInput = document.getElementById('edit_heure');
                    const validation = validateDateTime(dateInput, timeInput);

                    if (!validation.valid) {
                        e.preventDefault();
                        showValidationError(validation.message);
                        return false;
                    }
                });
            }

            // Vérifier s'il y a des erreurs pour rouvrir le modal
            @if ($errors->any() && old('_token'))
                hasValidationErrors = true;
                setTimeout(() => {
                    openAddModal();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 100);
            @endif

            @if (session('error') && old('_token'))
                hasSessionError = true;
                setTimeout(() => {
                    openAddModal();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 100);
            @endif

            @if (session('success'))
                // Scroll vers le haut pour voir le message de succès
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            @endif
        });

        // Fermer les modales avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        // Fermer en cliquant à l'extérieur
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
    </script>
</body>

</html>
