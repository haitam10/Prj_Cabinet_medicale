<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Dossier Médical</title>
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

                @if (Auth::check() && Auth::user()->role === 'medecin')
                    <a href="{{ route('secretaire.dossier-medical') }}"
                        class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Dossier Médical</h1>
                    <p class="text-gray-600 text-sm mt-1">Gestion complète du dossier médical des patients</p>
                </div>
            </div>
        </header>

        <main class="p-6">
            <!-- Messages d'alerte -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Sélection du patient -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-circle mr-2 text-cordes-blue"></i>
                    Sélection du Patient
                </h2>
                <form method="GET" action="{{ route('secretaire.dossier-medical') }}"
                    class="flex items-center space-x-4">
                    <div class="flex-1">
                        <select name="patient_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                            onchange="this.form.submit()">
                            <option value="">-- Sélectionner un patient --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}"
                                    {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom }} - {{ $patient->cin }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @if ($selectedPatient)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="font-semibold text-blue-900">Patient sélectionné :</h3>
                        <p class="text-blue-800">{{ $selectedPatient->nom }} - CIN: {{ $selectedPatient->cin }}</p>
                        <p class="text-blue-700 text-sm">Né(e) le:
                            {{ \Carbon\Carbon::parse($selectedPatient->date_naissance)->format('d/m/Y') }} | Sexe:
                            {{ ucfirst($selectedPatient->sexe) }}</p>
                    </div>
                @endif
            </div>

            @if ($selectedPatient)
                <!-- CONSULTATIONS -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-stethoscope mr-2 text-cordes-blue"></i>
                        Consultations
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.consultation.store') }}" method="POST"
                        class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if ($isCurrentUserMedecin)
                                    <input type="text" value="{{ $currentUser->nom }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none">
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                    consultation</label>
                                <input type="date" name="date_consultation" required value="{{ $today }}"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                                <input type="text" name="motif" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Symptômes</label>
                                <textarea name="symptomes" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnostic</label>
                                <textarea name="diagnostic" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Traitement</label>
                                <textarea name="traitement" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer Consultation
                        </button>
                    </form>

                    <!-- Liste des consultations -->
                    @if ($consultations->count() > 0)
                        <div class="space-y-3">
                            @foreach ($consultations as $consultation)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span
                                                    class="font-semibold">{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('d/m/Y') }}</span>
                                                <span class="text-gray-600">Dr.
                                                    {{ $consultation->medecin->nom }}</span>
                                            </div>
                                            <p class="text-gray-700 mb-1"><strong>Motif:</strong>
                                                {{ $consultation->motif }}</p>
                                            @if ($consultation->symptomes)
                                                <p class="text-gray-700 mb-1"><strong>Symptômes:</strong>
                                                    {{ $consultation->symptomes }}</p>
                                            @endif
                                            @if ($consultation->diagnostic)
                                                <p class="text-gray-700 mb-1"><strong>Diagnostic:</strong>
                                                    {{ $consultation->diagnostic }}</p>
                                            @endif
                                            @if ($consultation->traitement)
                                                <p class="text-gray-700"><strong>Traitement:</strong>
                                                    {{ $consultation->traitement }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="editConsultation({{ $consultation->id }}, '{{ \Carbon\Carbon::parse($consultation->date_consultation)->format('Y-m-d') }}', '{{ addslashes($consultation->motif) }}', '{{ addslashes($consultation->symptomes) }}', '{{ addslashes($consultation->diagnostic) }}', '{{ addslashes($consultation->traitement) }}', {{ $consultation->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form
                                                action="{{ route('secretaire.dossier-medical.consultation.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id"
                                                    value="{{ $consultation->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette consultation ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucune consultation enregistrée.</p>
                    @endif
                </div>

                <!-- Modal de modification consultation -->
                <div id="editConsultationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier la Consultation</h3>
                                <form action="{{ route('secretaire.dossier-medical.consultation.update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_consultation_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            @if ($isCurrentUserMedecin)
                                                <input type="text" value="{{ $currentUser->nom }}" readonly
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <select name="medecin_id" id="edit_consultation_medecin_id" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                    <option value="">-- Sélectionner un médecin --</option>
                                                    @foreach ($medecins as $medecin)
                                                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                                consultation</label>
                                            <input type="date" name="date_consultation"
                                                id="edit_consultation_date" required max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                                            <input type="text" name="motif" id="edit_consultation_motif"
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Symptômes</label>
                                            <textarea name="symptomes" id="edit_consultation_symptomes" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Diagnostic</label>
                                            <textarea name="diagnostic" id="edit_consultation_diagnostic" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Traitement</label>
                                            <textarea name="traitement" id="edit_consultation_traitement" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditConsultationModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HABITUDES DE VIE -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-heart mr-2 text-cordes-blue"></i>
                        Habitudes de Vie
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.habitude.store') }}" method="POST"
                        class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                <input type="text" value="{{ $currentUser->nom ?? 'Médecin' }}" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                @if ($isCurrentUserMedecin)
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'habitude</label>
                                <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Tabac">Tabac</option>
                                    <option value="Alcool">Alcool</option>
                                    <option value="Sport">Sport</option>
                                    <option value="Alimentation">Alimentation</option>
                                    <option value="Sommeil">Sommeil</option>
                                    <option value="Drogues">Drogues</option>
                                    <option value="Médicaments">Médicaments</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence</label>
                                <select name="frequence"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Quotidien">Quotidien</option>
                                    <option value="Hebdomadaire">Hebdomadaire</option>
                                    <option value="Mensuel">Mensuel</option>
                                    <option value="Occasionnel">Occasionnel</option>
                                    <option value="Arrêté">Arrêté</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                                <input type="text" name="quantite"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Ex: 10 cigarettes/jour, 2 verres/semaine">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                                <input type="date" name="date_debut" max="{{ $today }}"
                                    id="date_debut_add"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    onchange="updateDateFinMin('date_debut_add', 'date_fin_add')">
                                <p class="text-xs text-gray-500 mt-1">Ne peut pas dépasser aujourd'hui</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                                <input type="date" name="date_fin" id="date_fin_add" max="{{ $today }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                <p class="text-xs text-gray-500 mt-1">Doit être postérieure à la date de début</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" required rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Décrivez l'habitude en détail"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                <textarea name="commentaire" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer Habitude
                        </button>
                    </form>

                    <!-- Liste des habitudes -->
                    @if ($habitudes->count() > 0)
                        <div class="space-y-3">
                            @foreach ($habitudes as $habitude)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span class="font-semibold">{{ $habitude->type }}</span>
                                                @if ($habitude->frequence)
                                                    <span
                                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $habitude->frequence }}</span>
                                                @endif
                                                <span class="text-gray-600">Dr. {{ $habitude->medecin->nom }}</span>
                                            </div>
                                            <p class="text-gray-700 mb-1"><strong>Description:</strong>
                                                {{ $habitude->description }}</p>
                                            @if ($habitude->quantite)
                                                <p class="text-gray-700 mb-1"><strong>Quantité:</strong>
                                                    {{ $habitude->quantite }}</p>
                                            @endif
                                            @if ($habitude->date_debut)
                                                <p class="text-gray-700 mb-1">
                                                    <strong>Période:</strong>
                                                    Du
                                                    {{ \Carbon\Carbon::parse($habitude->date_debut)->format('d/m/Y') }}
                                                    @if ($habitude->date_fin)
                                                        au
                                                        {{ \Carbon\Carbon::parse($habitude->date_fin)->format('d/m/Y') }}
                                                    @else
                                                        (en cours)
                                                    @endif
                                                </p>
                                            @endif
                                            @if ($habitude->commentaire)
                                                <p class="text-gray-700"><strong>Commentaire:</strong>
                                                    {{ $habitude->commentaire }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="editHabitude({{ $habitude->id }}, '{{ $habitude->type }}', '{{ addslashes($habitude->description) }}', '{{ $habitude->frequence }}', '{{ $habitude->quantite }}', '{{ \Carbon\Carbon::parse($habitude->date_debut)->format('Y-m-d') ?? '' }}', '{{ \Carbon\Carbon::parse($habitude->date_fin)->format('Y-m-d') ?? '' }}', '{{ addslashes($habitude->commentaire) }}', {{ $habitude->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('secretaire.dossier-medical.habitude.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $habitude->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette habitude ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucune habitude de vie enregistrée.</p>
                    @endif
                </div>

                <!-- Modal de modification habitude -->
                <div id="editHabitudeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier l'Habitude de Vie</h3>
                                <form action="{{ route('secretaire.dossier-medical.habitude.update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_habitude_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            <input type="text" value="{{ $currentUser->nom ?? 'Médecin' }}"
                                                readonly
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                            @if ($isCurrentUserMedecin)
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <input type="hidden" name="medecin_id"
                                                    id="edit_habitude_medecin_id">
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type
                                                d'habitude</label>
                                            <select name="type" id="edit_habitude_type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Tabac">Tabac</option>
                                                <option value="Alcool">Alcool</option>
                                                <option value="Sport">Sport</option>
                                                <option value="Alimentation">Alimentation</option>
                                                <option value="Sommeil">Sommeil</option>
                                                <option value="Drogues">Drogues</option>
                                                <option value="Médicaments">Médicaments</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Fréquence</label>
                                            <select name="frequence" id="edit_habitude_frequence"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Quotidien">Quotidien</option>
                                                <option value="Hebdomadaire">Hebdomadaire</option>
                                                <option value="Mensuel">Mensuel</option>
                                                <option value="Occasionnel">Occasionnel</option>
                                                <option value="Arrêté">Arrêté</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                                            <input type="text" name="quantite" id="edit_habitude_quantite"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Ex: 10 cigarettes/jour, 2 verres/semaine">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                                début</label>
                                            <input type="date" name="date_debut" id="edit_habitude_date_debut"
                                                max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                onchange="updateDateFinMin('edit_habitude_date_debut', 'edit_habitude_date_fin')">
                                            <p class="text-xs text-gray-500 mt-1">Ne peut pas dépasser aujourd'hui</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                                fin</label>
                                            <input type="date" name="date_fin" id="edit_habitude_date_fin"
                                                max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                            <p class="text-xs text-gray-500 mt-1">Doit être postérieure à la date de
                                                début</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" id="edit_habitude_description" required rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Décrivez l'habitude en détail"></textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                            <textarea name="commentaire" id="edit_habitude_commentaire" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditHabitudeModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EXAMENS BIOLOGIQUES -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-vial mr-2 text-cordes-blue"></i>
                        Examens Biologiques
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.examen.store') }}" method="POST"
                        class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if ($isCurrentUserMedecin)
                                    <input type="text" value="{{ $currentUser->nom }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'examen</label>
                                <input type="date" name="date_examen" required value="{{ $today }}"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'examen</label>
                                <input type="text" name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Ex: Glycémie, NFS, etc.">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                                <input type="text" name="unite"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Ex: g/L, mg/dL">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Résultat</label>
                                <input type="text" name="resultat" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valeurs de
                                    référence</label>
                                <input type="text" name="valeurs_reference"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                <textarea name="commentaire" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer Examen
                        </button>
                    </form>

                    <!-- Liste des examens -->
                    @if ($examens->count() > 0)
                        <div class="space-y-3">
                            @foreach ($examens as $examen)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span class="font-semibold">{{ $examen->type }}</span>
                                                <span
                                                    class="text-gray-600">{{ \Carbon\Carbon::parse($examen->date_examen)->format('d/m/Y') }}</span>
                                                <span class="text-gray-600">Dr. {{ $examen->medecin->nom }}</span>
                                            </div>
                                            <p class="text-gray-700 mb-1"><strong>Résultat:</strong>
                                                {{ $examen->resultat }} {{ $examen->unite }}</p>
                                            @if ($examen->valeurs_reference)
                                                <p class="text-gray-700 mb-1"><strong>Valeurs de référence:</strong>
                                                    {{ $examen->valeurs_reference }}</p>
                                            @endif
                                            @if ($examen->commentaire)
                                                <p class="text-gray-700"><strong>Commentaire:</strong>
                                                    {{ $examen->commentaire }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="editExamen({{ $examen->id }}, '{{ $examen->type }}', '{{ $examen->resultat }}', '{{ $examen->unite }}', '{{ $examen->valeurs_reference }}', '{{ \Carbon\Carbon::parse($examen->date_examen)->format('Y-m-d') }}', '{{ addslashes($examen->commentaire) }}', {{ $examen->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('secretaire.dossier-medical.examen.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $examen->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun examen biologique enregistré.</p>
                    @endif
                </div>

                <!-- Modal de modification examen -->
                <div id="editExamenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier l'Examen Biologique</h3>
                                <form action="{{ route('secretaire.dossier-medical.examen.update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_examen_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            @if ($isCurrentUserMedecin)
                                                <input type="text" value="{{ $currentUser->nom }}" readonly
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <select name="medecin_id" id="edit_examen_medecin_id" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                    <option value="">-- Sélectionner un médecin --</option>
                                                    @foreach ($medecins as $medecin)
                                                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date
                                                d'examen</label>
                                            <input type="date" name="date_examen" id="edit_examen_date" required
                                                max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type
                                                d'examen</label>
                                            <input type="text" name="type" id="edit_examen_type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Ex: Glycémie, NFS, etc.">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                                            <input type="text" name="unite" id="edit_examen_unite"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Ex: g/L, mg/dL">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Résultat</label>
                                            <input type="text" name="resultat" id="edit_examen_resultat" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Valeurs de
                                                référence</label>
                                            <input type="text" name="valeurs_reference"
                                                id="edit_examen_valeurs_reference"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                            <textarea name="commentaire" id="edit_examen_commentaire" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditExamenModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IMAGERIE MÉDICALE -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-x-ray mr-2 text-cordes-blue"></i>
                        Imagerie Médicale
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.imagerie.store') }}" method="POST"
                        class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if ($isCurrentUserMedecin)
                                    <input type="text" value="{{ $currentUser->nom }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'examen</label>
                                <input type="date" name="date_examen" required value="{{ $today }}"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'imagerie</label>
                                <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Radiographie">Radiographie</option>
                                    <option value="Radio pulmonaire">Radio pulmonaire</option>
                                    <option value="Radio osseuse">Radio osseuse</option>
                                    <option value="Radio dentaire">Radio dentaire</option>
                                    <option value="Scanner">Scanner</option>
                                    <option value="Scanner thoracique">Scanner thoracique</option>
                                    <option value="Scanner abdominal">Scanner abdominal</option>
                                    <option value="Scanner cérébral">Scanner cérébral</option>
                                    <option value="IRM">IRM</option>
                                    <option value="IRM cérébrale">IRM cérébrale</option>
                                    <option value="IRM rachidienne">IRM rachidienne</option>
                                    <option value="IRM articulaire">IRM articulaire</option>
                                    <option value="Échographie">Échographie</option>
                                    <option value="Échographie abdominale">Échographie abdominale</option>
                                    <option value="Échographie pelvienne">Échographie pelvienne</option>
                                    <option value="Échographie cardiaque">Échographie cardiaque</option>
                                    <option value="Échographie obstétricale">Échographie obstétricale</option>
                                    <option value="Mammographie">Mammographie</option>
                                    <option value="Scintigraphie">Scintigraphie</option>
                                    <option value="Fibroscopie">Fibroscopie</option>
                                    <option value="Coloscopie">Coloscopie</option>
                                    <option value="Endoscopie">Endoscopie</option>
                                    <option value="Artériographie">Artériographie</option>
                                    <option value="Coronarographie">Coronarographie</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone examinée</label>
                                <input type="text" name="zone_examinee" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Ex: Thorax, Abdomen, etc.">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Résultat</label>
                                <textarea name="resultat" required rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                <textarea name="commentaire" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer Imagerie
                        </button>
                    </form>

                    <!-- Liste des imageries -->
                    @if ($imageries->count() > 0)
                        <div class="space-y-3">
                            @foreach ($imageries as $imagerie)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span class="font-semibold">{{ $imagerie->type }}</span>
                                                <span class="text-gray-600">{{ $imagerie->zone_examinee }}</span>
                                                <span
                                                    class="text-gray-600">{{ \Carbon\Carbon::parse($imagerie->date_examen)->format('d/m/Y') }}</span>
                                                <span class="text-gray-600">Dr. {{ $imagerie->medecin->nom }}</span>
                                            </div>
                                            <p class="text-gray-700 mb-1"><strong>Résultat:</strong>
                                                {{ $imagerie->resultat }}</p>
                                            @if ($imagerie->commentaire)
                                                <p class="text-gray-700"><strong>Commentaire:</strong>
                                                    {{ $imagerie->commentaire }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="editImagerie({{ $imagerie->id }}, '{{ $imagerie->type }}', '{{ $imagerie->zone_examinee }}', '{{ addslashes($imagerie->resultat) }}', '{{ \Carbon\Carbon::parse($imagerie->date_examen)->format('Y-m-d') }}', '{{ addslashes($imagerie->commentaire) }}', {{ $imagerie->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('secretaire.dossier-medical.imagerie.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $imagerie->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette imagerie ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucune imagerie médicale enregistrée.</p>
                    @endif
                </div>

                <!-- Modal de modification imagerie -->
                <div id="editImagerieModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier l'Imagerie Médicale</h3>
                                <form action="{{ route('secretaire.dossier-medical.imagerie.update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_imagerie_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            @if ($isCurrentUserMedecin)
                                                <input type="text" value="{{ $currentUser->nom }}" readonly
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <select name="medecin_id" id="edit_imagerie_medecin_id" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                    <option value="">-- Sélectionner un médecin --</option>
                                                    @foreach ($medecins as $medecin)
                                                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date
                                                d'examen</label>
                                            <input type="date" name="date_examen" id="edit_imagerie_date" required
                                                max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type
                                                d'imagerie</label>
                                            <select name="type" id="edit_imagerie_type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Radiographie">Radiographie</option>
                                                <option value="Radio pulmonaire">Radio pulmonaire</option>
                                                <option value="Radio osseuse">Radio osseuse</option>
                                                <option value="Radio dentaire">Radio dentaire</option>
                                                <option value="Scanner">Scanner</option>
                                                <option value="Scanner thoracique">Scanner thoracique</option>
                                                <option value="Scanner abdominal">Scanner abdominal</option>
                                                <option value="Scanner cérébral">Scanner cérébral</option>
                                                <option value="IRM">IRM</option>
                                                <option value="IRM cérébrale">IRM cérébrale</option>
                                                <option value="IRM rachidienne">IRM rachidienne</option>
                                                <option value="IRM articulaire">IRM articulaire</option>
                                                <option value="Échographie">Échographie</option>
                                                <option value="Échographie abdominale">Échographie abdominale</option>
                                                <option value="Échographie pelvienne">Échographie pelvienne</option>
                                                <option value="Échographie cardiaque">Échographie cardiaque</option>
                                                <option value="Échographie obstétricale">Échographie obstétricale
                                                </option>
                                                <option value="Mammographie">Mammographie</option>
                                                <option value="Scintigraphie">Scintigraphie</option>
                                                <option value="Fibroscopie">Fibroscopie</option>
                                                <option value="Coloscopie">Coloscopie</option>
                                                <option value="Endoscopie">Endoscopie</option>
                                                <option value="Artériographie">Artériographie</option>
                                                <option value="Coronarographie">Coronarographie</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Zone
                                                examinée</label>
                                            <input type="text" name="zone_examinee" id="edit_imagerie_zone"
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Ex: Thorax, Abdomen, etc.">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Résultat</label>
                                            <textarea name="resultat" id="edit_imagerie_resultat" required rows="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                            <textarea name="commentaire" id="edit_imagerie_commentaire" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditImagerieModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VACCINATIONS -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-syringe mr-2 text-cordes-blue"></i>
                        Vaccinations
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.vaccination.store') }}" method="POST"
                        class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if ($isCurrentUserMedecin)
                                    <input type="text" value="{{ $currentUser->nom }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du vaccin</label>
                                <input type="text" name="nom" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                    placeholder="Ex: COVID-19, Grippe, etc.">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de vaccination</label>
                                <input type="date" name="date_vaccination" required value="{{ $today }}"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de rappel</label>
                                <input type="date" name="date_rappel"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                <textarea name="commentaire" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer Vaccination
                        </button>
                    </form>

                    <!-- Liste des vaccinations -->
                    @if ($vaccinations->count() > 0)
                        <div class="space-y-3">
                            @foreach ($vaccinations as $vaccination)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span class="font-semibold">{{ $vaccination->nom }}</span>
                                                <span
                                                    class="text-gray-600">{{ \Carbon\Carbon::parse($vaccination->date_vaccination)->format('d/m/Y') }}</span>
                                                <span class="text-gray-600">Dr.
                                                    {{ $vaccination->medecin->nom }}</span>
                                            </div>
                                            @if ($vaccination->date_rappel)
                                                <p class="text-gray-700 mb-1"><strong>Date de rappel:</strong>
                                                    {{ \Carbon\Carbon::parse($vaccination->date_rappel)->format('d/m/Y') }}
                                                </p>
                                            @endif
                                            @if ($vaccination->commentaire)
                                                <p class="text-gray-700"><strong>Commentaire:</strong>
                                                    {{ $vaccination->commentaire }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button
                                                onclick="editVaccination({{ $vaccination->id }}, '{{ $vaccination->nom }}', '{{ \Carbon\Carbon::parse($vaccination->date_vaccination)->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($vaccination->date_rappel)->format('Y-m-d') ?? '' }}', '{{ addslashes($vaccination->commentaire) }}', {{ $vaccination->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form
                                                action="{{ route('secretaire.dossier-medical.vaccination.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id"
                                                    value="{{ $vaccination->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vaccination ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucune vaccination enregistrée.</p>
                    @endif
                </div>

                <!-- Modal de modification vaccination -->
                <div id="editVaccinationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier la Vaccination</h3>
                                <form action="{{ route('secretaire.dossier-medical.vaccination.update') }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_vaccination_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            @if ($isCurrentUserMedecin)
                                                <input type="text" value="{{ $currentUser->nom }}" readonly
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <select name="medecin_id" id="edit_vaccination_medecin_id" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                    <option value="">-- Sélectionner un médecin --</option>
                                                    @foreach ($medecins as $medecin)
                                                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du
                                                vaccin</label>
                                            <input type="text" name="nom" id="edit_vaccination_nom" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"
                                                placeholder="Ex: COVID-19, Grippe, etc.">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                                vaccination</label>
                                            <input type="date" name="date_vaccination" id="edit_vaccination_date"
                                                required max="{{ $today }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de
                                                rappel</label>
                                            <input type="date" name="date_rappel"
                                                id="edit_vaccination_date_rappel"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                            <textarea name="commentaire" id="edit_vaccination_commentaire" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditVaccinationModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FICHIERS MÉDICAUX -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-file-medical-alt mr-2 text-cordes-blue"></i>
                        Fichiers Médicaux
                    </h2>
                    <!-- Formulaire d'ajout -->
                    <form action="{{ route('secretaire.dossier-medical.fichier.store') }}" method="POST"
                        enctype="multipart/form-data" class="mb-6 bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if ($isCurrentUserMedecin)
                                    <input type="text" value="{{ $currentUser->nom }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                    <input type="hidden" name="medecin_id" value="{{ $currentUser->id }}">
                                @else
                                    <select name="medecin_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        <option value="">-- Sélectionner un médecin --</option>
                                        @foreach ($medecins as $medecin)
                                            <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type de fichier</label>
                                <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Ordonnance">Ordonnance</option>
                                    <option value="Prescription médicale">Prescription médicale</option>
                                    <option value="Certificat">Certificat</option>
                                    <option value="Certificat médical">Certificat médical</option>
                                    <option value="Certificat d'aptitude">Certificat d'aptitude</option>
                                    <option value="Certificat de décès">Certificat de décès</option>
                                    <option value="Compte-rendu">Compte-rendu</option>
                                    <option value="Compte-rendu opératoire">Compte-rendu opératoire</option>
                                    <option value="Compte-rendu d'hospitalisation">Compte-rendu d'hospitalisation
                                    </option>
                                    <option value="Résultat d'examen">Résultat d'examen</option>
                                    <option value="Résultat de laboratoire">Résultat de laboratoire</option>
                                    <option value="Résultat d'imagerie">Résultat d'imagerie</option>
                                    <option value="Rapport médical">Rapport médical</option>
                                    <option value="Lettre de liaison">Lettre de liaison</option>
                                    <option value="Courrier médical">Courrier médical</option>
                                    <option value="Fiche de suivi">Fiche de suivi</option>
                                    <option value="Carnet de vaccination">Carnet de vaccination</option>
                                    <option value="Document administratif">Document administratif</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du fichier</label>
                                <input type="text" name="nom" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                                <input type="file" name="fichier" required
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.txt,.rtf,.xls,.xlsx,.ppt,.pptx"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, JPG, JPEG, PNG,
                                    GIF, BMP, TIFF, TXT, RTF, XLS, XLSX, PPT, PPTX (Max: 10MB)</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                <textarea name="commentaire" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-upload mr-2"></i>Télécharger Fichier
                        </button>
                    </form>

                    <!-- Liste des fichiers -->
                    @if ($fichiers->count() > 0)
                        <div class="space-y-3">
                            @foreach ($fichiers as $fichier)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-2">
                                                <span class="font-semibold">{{ $fichier->nom }}</span>
                                                <span
                                                    class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $fichier->type }}</span>
                                                <span
                                                    class="text-gray-600">{{ \Carbon\Carbon::parse($fichier->created_at)->format('d/m/Y') }}</span>
                                                <span class="text-gray-600">Dr. {{ $fichier->medecin->nom }}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm mb-1">Taille:
                                                {{ number_format($fichier->taille / 1024, 2) }} KB</p>
                                            @if ($fichier->commentaire)
                                                <p class="text-gray-700"><strong>Commentaire:</strong>
                                                    {{ $fichier->commentaire }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <a href="{{ Storage::url($fichier->chemin) }}" target="_blank"
                                                class="text-cordes-blue hover:text-cordes-dark">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button
                                                onclick="editFichier({{ $fichier->id }}, '{{ addslashes($fichier->nom) }}', '{{ $fichier->type }}', '{{ addslashes($fichier->commentaire) }}', {{ $fichier->medecin_id }})"
                                                class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('secretaire.dossier-medical.fichier.destroy') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $fichier->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun fichier médical téléchargé.</p>
                    @endif
                </div>

                <!-- Modal de modification fichier -->
                <div id="editFichierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifier le Fichier Médical</h3>
                                <form action="{{ route('secretaire.dossier-medical.fichier.update') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" id="edit_fichier_id">
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                            @if ($isCurrentUserMedecin)
                                                <input type="text" value="{{ $currentUser->nom }}" readonly
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                                <input type="hidden" name="medecin_id"
                                                    value="{{ $currentUser->id }}">
                                            @else
                                                <select name="medecin_id" id="edit_fichier_medecin_id" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                    <option value="">-- Sélectionner un médecin --</option>
                                                    @foreach ($medecins as $medecin)
                                                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de
                                                fichier</label>
                                            <select name="type" id="edit_fichier_type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Ordonnance">Ordonnance</option>
                                                <option value="Prescription médicale">Prescription médicale</option>
                                                <option value="Certificat">Certificat</option>
                                                <option value="Certificat médical">Certificat médical</option>
                                                <option value="Certificat d'aptitude">Certificat d'aptitude</option>
                                                <option value="Certificat de décès">Certificat de décès</option>
                                                <option value="Compte-rendu">Compte-rendu</option>
                                                <option value="Compte-rendu opératoire">Compte-rendu opératoire
                                                </option>
                                                <option value="Compte-rendu d'hospitalisation">Compte-rendu
                                                    d'hospitalisation</option>
                                                <option value="Résultat d'examen">Résultat d'examen</option>
                                                <option value="Résultat de laboratoire">Résultat de laboratoire
                                                </option>
                                                <option value="Résultat d'imagerie">Résultat d'imagerie</option>
                                                <option value="Rapport médical">Rapport médical</option>
                                                <option value="Lettre de liaison">Lettre de liaison</option>
                                                <option value="Courrier médical">Courrier médical</option>
                                                <option value="Fiche de suivi">Fiche de suivi</option>
                                                <option value="Autre">Autre</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du
                                                fichier</label>
                                            <input type="text" name="nom" id="edit_fichier_nom" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fichier
                                                (laisser vide pour conserver l'actuel)</label>
                                            <input type="file" name="fichier"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.txt,.rtf,.xls,.xlsx,.ppt,.pptx"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue">
                                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX,
                                                JPG, JPEG, PNG, GIF, BMP, TIFF, TXT, RTF, XLS, XLSX, PPT, PPTX (Max:
                                                10MB)</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                            <textarea name="commentaire" id="edit_fichier_commentaire" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cordes-blue"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditFichierModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                            <i class="fas fa-save mr-2"></i>Modifier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <script>
        // Auto-hide des messages après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.bg-green-100');
            const errorMessage = document.querySelector('.bg-red-100');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.transition = 'opacity 0.5s ease-out';
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.remove();
                    }, 500);
                }, 5000);
            }
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.transition = 'opacity 0.5s ease-out';
                    errorMessage.style.opacity = '0';
                    setTimeout(() => {
                        errorMessage.remove();
                    }, 500);
                }, 5000);
            }
        });

        // Auto-submit du formulaire de sélection patient
        document.addEventListener('DOMContentLoaded', function() {
            const patientSelect = document.querySelector('select[name="patient_id"]');
            if (patientSelect) {
                patientSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });

        // Fonctions pour les modals de modification
        function editConsultation(id, date_consultation, motif, symptomes, diagnostic, traitement, medecin_id) {
            document.getElementById('editConsultationModal').classList.remove('hidden');
            document.getElementById('edit_consultation_id').value = id;
            document.getElementById('edit_consultation_date').value = date_consultation;
            document.getElementById('edit_consultation_motif').value = motif;
            document.getElementById('edit_consultation_symptomes').value = symptomes || '';
            document.getElementById('edit_consultation_diagnostic').value = diagnostic || '';
            document.getElementById('edit_consultation_traitement').value = traitement || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_consultation_medecin_id').value = medecin_id;
            @endif
        }

        function closeEditConsultationModal() {
            document.getElementById('editConsultationModal').classList.add('hidden');
            document.getElementById('editConsultationModal').querySelector('form').reset();
        }

        function editHabitude(id, type, description, frequence, quantite, date_debut, date_fin, commentaire, medecin_id) {
            document.getElementById('editHabitudeModal').classList.remove('hidden');
            document.getElementById('edit_habitude_id').value = id;
            document.getElementById('edit_habitude_type').value = type;
            document.getElementById('edit_habitude_description').value = description;
            document.getElementById('edit_habitude_frequence').value = frequence || '';
            document.getElementById('edit_habitude_quantite').value = quantite || '';
            document.getElementById('edit_habitude_date_debut').value = date_debut || '';
            document.getElementById('edit_habitude_date_fin').value = date_fin || '';
            document.getElementById('edit_habitude_commentaire').value = commentaire || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_habitude_medecin_id').value = medecin_id;
            @endif

            // Mettre à jour la date minimum pour la date de fin
            const dateDebut = document.getElementById('edit_habitude_date_debut');
            if (dateDebut) {
                updateDateFinMin('edit_habitude_date_debut', 'edit_habitude_date_fin');
            }
        }

        function closeEditHabitudeModal() {
            document.getElementById('editHabitudeModal').classList.add('hidden');
            document.getElementById('editHabitudeModal').querySelector('form').reset();
        }

        function editExamen(id, type, resultat, unite, valeurs_reference, date_examen, commentaire, medecin_id) {
            document.getElementById('editExamenModal').classList.remove('hidden');
            document.getElementById('edit_examen_id').value = id;
            document.getElementById('edit_examen_type').value = type;
            document.getElementById('edit_examen_resultat').value = resultat;
            document.getElementById('edit_examen_unite').value = unite || '';
            document.getElementById('edit_examen_valeurs_reference').value = valeurs_reference || '';
            document.getElementById('edit_examen_date').value = date_examen;
            document.getElementById('edit_examen_commentaire').value = commentaire || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_examen_medecin_id').value = medecin_id;
            @endif
        }

        function closeEditExamenModal() {
            document.getElementById('editExamenModal').classList.add('hidden');
            document.getElementById('editExamenModal').querySelector('form').reset();
        }

        function editImagerie(id, type, zone_examinee, resultat, date_examen, commentaire, medecin_id) {
            document.getElementById('editImagerieModal').classList.remove('hidden');
            document.getElementById('edit_imagerie_id').value = id;
            document.getElementById('edit_imagerie_type').value = type;
            document.getElementById('edit_imagerie_zone').value = zone_examinee;
            document.getElementById('edit_imagerie_resultat').value = resultat;
            document.getElementById('edit_imagerie_date').value = date_examen;
            document.getElementById('edit_imagerie_commentaire').value = commentaire || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_imagerie_medecin_id').value = medecin_id;
            @endif
        }

        function closeEditImagerieModal() {
            document.getElementById('editImagerieModal').classList.add('hidden');
            document.getElementById('editImagerieModal').querySelector('form').reset();
        }

        function editVaccination(id, nom, date_vaccination, date_rappel, commentaire, medecin_id) {
            document.getElementById('editVaccinationModal').classList.remove('hidden');
            document.getElementById('edit_vaccination_id').value = id;
            document.getElementById('edit_vaccination_nom').value = nom;
            document.getElementById('edit_vaccination_date').value = date_vaccination;
            document.getElementById('edit_vaccination_date_rappel').value = date_rappel || '';
            document.getElementById('edit_vaccination_commentaire').value = commentaire || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_vaccination_medecin_id').value = medecin_id;
            @endif
        }

        function closeEditVaccinationModal() {
            document.getElementById('editVaccinationModal').classList.add('hidden');
            document.getElementById('editVaccinationModal').querySelector('form').reset();
        }

        function editFichier(id, nom, type, commentaire, medecin_id) {
            document.getElementById('editFichierModal').classList.remove('hidden');
            document.getElementById('edit_fichier_id').value = id;
            document.getElementById('edit_fichier_nom').value = nom;
            document.getElementById('edit_fichier_type').value = type;
            document.getElementById('edit_fichier_commentaire').value = commentaire || '';
            @if (!$isCurrentUserMedecin)
                document.getElementById('edit_fichier_medecin_id').value = medecin_id;
            @endif
        }

        function closeEditFichierModal() {
            document.getElementById('editFichierModal').classList.add('hidden');
            document.getElementById('editFichierModal').querySelector('form').reset();
        }

        // Fonction pour mettre à jour la date minimum de fin en fonction de la date de début
        function updateDateFinMin(dateDebutId, dateFinId) {
            const dateDebut = document.getElementById(dateDebutId);
            const dateFin = document.getElementById(dateFinId);

            if (dateDebut.value) {
                dateFin.min = dateDebut.value;
                // Si la date de fin est antérieure à la nouvelle date de début, la réinitialiser
                if (dateFin.value && dateFin.value < dateDebut.value) {
                    dateFin.value = '';
                }
            }
        }

        // Fermer les modals en cliquant à l'extérieur et réinitialiser le formulaire
        document.addEventListener('click', function(event) {
            const modals = [{
                    id: 'editConsultationModal',
                    closeFunc: closeEditConsultationModal
                },
                {
                    id: 'editHabitudeModal',
                    closeFunc: closeEditHabitudeModal
                },
                {
                    id: 'editExamenModal',
                    closeFunc: closeEditExamenModal
                },
                {
                    id: 'editImagerieModal',
                    closeFunc: closeEditImagerieModal
                },
                {
                    id: 'editVaccinationModal',
                    closeFunc: closeEditVaccinationModal
                },
                {
                    id: 'editFichierModal',
                    closeFunc: closeEditFichierModal
                }
            ];
            modals.forEach(modalInfo => {
                const modal = document.getElementById(modalInfo.id);
                if (modal && event.target === modal) {
                    modalInfo.closeFunc();
                }
            });
        });
    </script>
</body>

</html>
