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
                <a href="{{ route('secretaire.certificats') }}"
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Certificats
                </a>
                <a href="{{ route('secretaire.ordonnances') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Ordonnances
                </a>
                <a href="{{ route('secretaire.remarques') }}"
                   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Remarques
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
                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" id="dateFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <span id="rdvCount">{{ $latest_rvs->total() }} rendez-vous au total</span>
                    </div>
                </div>
            </div>

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
                                    {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($rdv->date)->format('H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->medecin->nom ?? 'Inconnu' }} {{ $rdv->medecin->prenom ?? '' }}
                                    @if ($rdv->medecin && isset($rdv->medecin->specialite))
                                        <div class="text-xs text-gray-500">{{ $rdv->medecin->specialite }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->patient->nom ?? 'Inconnu' }} {{ $rdv->patient->prenom ?? '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($rdv->statut == 'confirmé')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i
                                                class="fas fa-check-circle mr-1"></i>Confirmé</span>
                                    @elseif($rdv->statut == 'en attente')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i
                                                class="fas fa-clock mr-1"></i>En attente</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i
                                                class="fas fa-times-circle mr-1"></i>Annulé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $rdv->motif ?? 'Aucun motif' }}</td>
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
            <form action="{{ route('rendezvous.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                    <select name="patient_id" id="patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionnez un patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="medecin_id" class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                    <select name="medecin_id" id="medecin_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="">Sélectionnez un médecin</option>
                        @foreach ($medecins as $medecin)
                            <option value="{{ $medecin->id }}">{{ $medecin->nom }} {{ $medecin->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" id="date" required min="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="heure" class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
                        <input type="time" name="heure" id="heure" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="en attente">En attente</option>
                        <option value="confirmé">Confirmé</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <textarea name="motif" id="motif" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Décrivez le motif du rendez-vous..."></textarea>
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
                            <option value="{{ $patient->id }}">{{ $patient->nom }}</option>
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
                    <textarea name="motif" id="edit_motif" rows="3" required
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
                }, 500); // Attendre la fin de l'animation de transition
            }
        }

        // Fonction pour afficher un message temporaire (pour les actions AJAX)
        function showTemporaryMessage(message, type = 'success') {
            // Supprimer les anciens messages temporaires
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

            // Insérer le message au début du main
            const main = document.querySelector('main');
            main.insertBefore(messageDiv, main.firstChild);

            // Masquer automatiquement après 5 secondes
            setTimeout(() => {
                hideMessage(messageDiv);
            }, 5000);
        }

        // Fonction de recherche et filtrage pour les rendez-vous
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

            // Afficher/masquer le message "aucun rendez-vous"
            if (noRdvRow) {
                noRdvRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            // Mettre à jour le compteur
            document.getElementById('rdvCount').textContent = visibleCount + ' rendez-vous' + (visibleCount > 1 ? 's' :
                '') + ' au total';
        }

        // Initialiser le masquage automatique au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();

            // Ajouter les événements de filtrage
            document.getElementById('searchInput').addEventListener('input', filterRendezVous);
            document.getElementById('statutFilter').addEventListener('change', filterRendezVous);
            document.getElementById('dateFilter').addEventListener('change', filterRendezVous);
        });

        // Fonction pour vérifier les conflits d'horaires côté client
        async function checkScheduleConflict(medecinId, date, heure, excludeId = null) {
            try {
                const response = await fetch('/api/check-schedule-conflict', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        medecin_id: medecinId,
                        date: date,
                        heure: heure,
                        exclude_id: excludeId
                    })
                });
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Erreur lors de la vérification des conflits:', error);
                return {
                    hasConflict: false
                };
            }
        }

        // Fonction pour valider la date et l'heure
        function validateDateTime(dateValue, timeValue) {
            const now = new Date();
            const selectedDateTime = new Date(dateValue + 'T' + timeValue);
            if (selectedDateTime <= now) {
                alert('La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.');
                return false;
            }
            return true;
        }

        // Fonction pour mettre à jour l'heure minimale
        function updateMinTime(dateInput, timeInput) {
            const selectedDate = dateInput.value;
            const today = new Date().toISOString().split('T')[0];
            if (selectedDate === today) {
                const now = new Date();
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(
                    2, '0');
                timeInput.min = currentTime;
            } else {
                timeInput.removeAttribute('min');
            }
        }

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            // Réinitialiser le formulaire
            document.querySelector('#addModal form').reset();
            // Configurer les contraintes de date/heure
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('heure');
            dateInput.addEventListener('change', function() {
                updateMinTime(dateInput, timeInput);
            });
            // Validation avant soumission
            document.querySelector('#addModal form').addEventListener('submit', function(e) {
                const dateValue = dateInput.value;
                const timeValue = timeInput.value;
                const medecinId = document.getElementById('medecin_id').value;
                if (!validateDateTime(dateValue, timeValue)) {
                    e.preventDefault();
                    return;
                }
                // Vérification des conflits d'horaires (optionnel côté client)
                // La validation principale se fait côté serveur
            });
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(rdv) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editForm').action = '/rendezvous/' + rdv.id;
            document.getElementById('edit_id').value = rdv.id;
            document.getElementById('edit_patient_id').value = rdv.patient_id;
            document.getElementById('edit_medecin_id').value = rdv.medecin_id;
            // Traitement de la date et heure
            const dateTime = new Date(rdv.date);
            const datePart = dateTime.toISOString().substring(0, 10);
            const timePart = dateTime.toTimeString().substring(0, 5);
            document.getElementById('edit_date').value = datePart;
            document.getElementById('edit_heure').value = timePart;
            document.getElementById('edit_statut').value = rdv.statut;
            document.getElementById('edit_motif').value = rdv.motif || '';

            // Configurer les contraintes de date/heure pour l'édition
            const editDateInput = document.getElementById('edit_date');
            const editTimeInput = document.getElementById('edit_heure');
            editDateInput.addEventListener('change', function() {
                updateMinTime(editDateInput, editTimeInput);
            });
            // Validation avant soumission pour l'édition
            document.getElementById('editForm').addEventListener('submit', function(e) {
                const dateValue = editDateInput.value;
                const timeValue = editTimeInput.value;
                const medecinId = document.getElementById('edit_medecin_id').value;
                if (!validateDateTime(dateValue, timeValue)) {
                    e.preventDefault();
                    return;
                }
                // Vérification des conflits d'horaires (optionnel côté client)
                // La validation principale se fait côté serveur
            });
            // Mettre à jour l'heure minimale immédiatement
            updateMinTime(editDateInput, editTimeInput);
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deleteRendezVous(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
                // Utiliser fetch pour une suppression AJAX
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
                            showTemporaryMessage(data.message, 'success');
                            // Recharger la page après un court délai
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
