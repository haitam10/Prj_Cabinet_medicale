<div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <!-- Ajouter Patient Modal -->
    <div id="patientModal" class="modal hidden">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Ajouter Patient</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('patients.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                        <input type="text" name="prenom" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                        <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sexe</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="sexe" value="homme" required class="mr-2 text-cordes-accent">
                                <span>Homme</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sexe" value="femme" required class="mr-2 text-cordes-accent">
                                <span>Femme</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de Naissance</label>
                        <input type="date" name="date_naissance" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de Contact</label>
                        <input type="tel" name="contact" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Ajouter Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generer Facture Modal -->
    <div id="factureModal" class="modal hidden">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Générer Facture</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                {{-- <form action="{{ route('secretaire.factureStore') }}" method="POST" class="p-6 space-y-4"> --}}
                <form action="#" method="POST" class="p-6 space-y-4">

                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient (CNI)</label>
                        <div class="relative">
                            <select id="patientSelect" name="patient_id" class="w-full">
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
                        <input type="date" name="date" id="currentDate" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                        <input type="number" name="montant" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            Générer Facture
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                            <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
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
                        <input type="date" name="date" id="date" required
                            min="{{ date('Y-m-d') }}"
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
                    <textarea name="motif" id="motif" rows="3"
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

</div>
<!-- Load jQuery and Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Modal Functions

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










//////////////////////////
function openModal(modalId) {
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById(modalId).classList.remove('hidden');
    
    // Set current date for facture modal
    if (modalId === 'factureModal') {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('currentDate').value = today;
    }
}

function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.add('hidden');
    });
}

// Close modal when clicking overlay
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Initialize Select2 after DOM ready
$(document).ready(function() {
    $('#patientSelect').select2({
        placeholder: "CNI ou Nom...",
        allowClear: true,
        width: '100%' // ensures it fills the container
    });
});
</script>
