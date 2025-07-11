<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Factures</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                <span class="text-white text-xl font-bold">Espace Secrétaire</span>
            </div>
        </div>
        <nav class="mt-8 px-4">
            <div class="space-y-2">
                <a href="{{ route('secretaire.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-home mr-3 text-cordes-accent group-hover:text-white"></i>
                    Dashboard
                </a>
                <a href="{{ route('secretaire.rendezvous') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-users mr-3 text-gray-400 group-hover:text-white"></i>
                    Rendez-vous
                </a>
                <a href="{{ route('secretaire.patients') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-chart-bar mr-3 text-gray-400 group-hover:text-white"></i>
                    Patients
                </a>
                <a href="{{ route('secretaire.factures') }}" class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-shopping-cart mr-3 text-white"></i>
                    Factures
                </a>
                <a href="{{ route('secretaire.docs') }}" class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
                    <i class="fas fa-file-medical mr-3 text-white"></i>
                    Documents
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Factures</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des Factures enregistrés</p>
                </div>
                <button onclick="openFactureModal()"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Génerer facture
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

            {{-- @if ($errors->any())
                <div id="validationErrors" class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher par patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="statutFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="en attente">En attente</option>
                            <option value="payée">Payée</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-file-invoice mr-2"></i>
                        <span id="factureCount">{{ $factures->total() }} Facture{{ $factures->total() > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded-xl">
               <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PATIENT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MEDECIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SECRETAIRE</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MONTANT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ajouté le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($factures as $facture)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $facture->patient->cin ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $facture->patient->nom ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $facture->medecin->nom ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $facture->secretaire->nom ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $facture->statut === 'payée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($facture->statut) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($facture->montant, 2) }} DH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($facture->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='editFacture({{ json_encode($facture) }})'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteFacture({{ $facture->id }})"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                            title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-file-invoice-dollar text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucune facture trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($factures->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $factures->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

     <!-- MODAL GENERER FACTURE -->
    <div id="factureModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Générer Facture</h2>
                <button onclick="closeFactureModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="{{ route('facture.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient (CNI)</label>
                    <div class="relative">
                        <select id="patientSelect" name="patient_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-accent focus:border-transparent">
                            <option value="">Tapez CNI ou nom...</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->cin }} | {{ $patient->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                    <select name="medecin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="">Sélectionner un médecin</option>
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">Dr.{{ $medecin->nom }} | N° {{ $medecin->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secrétaire</label>
                    <select name="secretaire_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="">Sélectionner un secrétaire</option>
                        @foreach($secretaires as $secretaire)
                        <option value="{{ $secretaire->id }}">Sec.{{ $secretaire->nom }} | N° {{ $secretaire->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="date" id="currentDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        <option value="en attente">En attente</option>
                        <option value="payée">Payée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                    <input type="number" name="montant" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeFactureModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        <i class="fas fa-save mr-2"></i>Générer Facture
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL MODIFIER FACTURE -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                    <select id="edit_patient_id" name="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="">Sélectionner un patient</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->cin }} | {{ $patient->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                    <select id="edit_medecin_id" name="medecin_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="">Sélectionner un médecin</option>
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">Dr.{{ $medecin->nom }} | N° {{ $medecin->id }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secrétaire</label>
                    <select id="edit_secretaire_id" name="secretaire_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="">Sélectionner un secrétaire</option>
                        @foreach($secretaires as $secretaire)
                        <option value="{{ $secretaire->id }}">Sec.{{ $secretaire->nom }} | N° {{ $secretaire->id }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="edit_statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        <option value="en attente">En attente</option>
                        <option value="payée">Payée</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="edit_date" name="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                    <input type="number" id="edit_montant" name="montant" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
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

    <!-- MODAL CONFIRMATION SUPPRESSION -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 m-4">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Confirmer la suppression</h3>
                    <p class="text-gray-600 text-sm">Cette action est irréversible.</p>
                </div>
            </div>
            <p class="text-gray-700 mb-6">Êtes-vous sûr de vouloir supprimer cette facture ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>


    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let factureToDelete = null;

        // Fonction pour masquer automatiquement les messages après 5 secondes
        function autoHideMessages() {
            const messages = [
                document.getElementById('successMessage'),
                document.getElementById('errorMessage'),
                document.getElementById('validationErrors')
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

        function showTemporaryMessage(message, type = 'success') {
            const existingTemp = document.querySelector('.temp-message');
            if (existingTemp) {
                existingTemp.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `temp-message mb-4 p-4 rounded-lg border transition-opacity duration-500 ${
                type === 'success' 
                    ? 'bg-green-100 text-green-800 border-green-200' 
                    : 'bg-red-100 text-red-800 border-red-200'
            }`;
            
            messageDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                ${message}
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

        // FACTURE Modal Functions
        function openFactureModal() {
            document.getElementById('factureModal').classList.remove('hidden');
            document.querySelector('#factureModal form').reset();
            // Set current date
            document.getElementById('currentDate').value = new Date().toISOString().split('T')[0];
        }

        function closeFactureModal() {
            document.getElementById('factureModal').classList.add('hidden');
        }

        // EDIT Modal Functions
        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

       

        // Edit Facture Function
        function editFacture(facture) {
            // Populate the edit form with facture data
            document.getElementById('edit_id').value = facture.id;
            document.getElementById('edit_patient_id').value = facture.patient_id;
            document.getElementById('edit_medecin_id').value = facture.medecin_id;
            document.getElementById('edit_secretaire_id').value = facture.secretaire_id;
            document.getElementById('edit_statut').value = facture.statut;
            const dateTime = new Date(facture.created_at);
            const datePart = dateTime.toISOString().substring(0, 10);
            document.getElementById('edit_date').value = datePart;
            document.getElementById('edit_montant').value = facture.montant;
            
            // Set form action
            document.getElementById('editForm').action = `/factures/${facture.id}`;
            
            openEditModal();
        }


         // DELETE Modal Functions
        let deleteFactureId = null;

        // Open modal and store ID
        function openDeleteModal(id) {
            deleteFactureId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteFactureId = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Called from delete icon button
        function deleteFacture(id) {
            openDeleteModal(id);
        }

        // Confirm Delete Function (called from "Supprimer" button)
        function confirmDelete() {
            if (!deleteFactureId) return;

            console.log('Deleting facture with ID:', deleteFactureId);

            fetch(`{{ url('/factures') }}/${deleteFactureId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                closeDeleteModal();
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


        // Search and Filter Functions
        function setupSearchAndFilter() {
            const searchInput = document.getElementById('searchInput');
            const statutFilter = document.getElementById('statutFilter');
            const tableRows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedStatut = statutFilter.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    if (row.cells.length === 1) return; // Skip "no data" row
                    
                    const patientName = row.cells[1].textContent.toLowerCase();
                    const cin = row.cells[0].textContent.toLowerCase();
                    const statut = row.cells[4].textContent.toLowerCase();
                    
                    const matchesSearch = patientName.includes(searchTerm) || cin.includes(searchTerm);
                    const matchesStatut = !selectedStatut || statut.includes(selectedStatut);
                    
                    if (matchesSearch && matchesStatut) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update count
                document.getElementById('factureCount').textContent = `${visibleCount} Facture${visibleCount > 1 ? 's' : ''}`;
            }

            searchInput.addEventListener('input', filterTable);
            statutFilter.addEventListener('change', filterTable);
        }

        // Event Listeners for clicking outside modals
        document.getElementById('factureModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFactureModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFactureModal();
                closeEditModal();
                closeDeleteModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
            setupSearchAndFilter();
            
            // Initialize Select2 for patient selection in facture modal
            if (document.getElementById('patientSelect')) {
                $('#patientSelect').select2({
                    placeholder: 'Tapez CNI ou nom...',
                    allowClear: true,
                    width: '100%'
                });
            }
            
            // Sidebar navigation active state
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    navLinks.forEach(l => l.classList.remove('bg-gray-700', 'text-white'));
                    navLinks.forEach(l => l.classList.add('text-gray-300'));
                    this.classList.add('bg-gray-700', 'text-white');
                    this.classList.remove('text-gray-300');
                });
            });

            // Set active nav link for factures page
            const facturesLink = document.querySelector('a[href*="factures"]');
            if (facturesLink) {
                facturesLink.classList.add('bg-gray-700', 'text-white');
                facturesLink.classList.remove('text-gray-300');
            }
        });
    </script>
</body>
</html>