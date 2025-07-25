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
                    <p class="text-gray-600 text-sm mt-1">Gestion des rendez-vous médicaux</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nouveau RDV
                </button>
            </div>
        </header>

        <main class="p-6">
            <!-- ZONE DES MESSAGES -->
            <div id="messages-container" class="space-y-4 mb-6">
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
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="statusFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="confirmed">Confirmé</option>
                            <option value="completed">Terminé</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fas fa-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="typeFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none">
                            <option value="">Tous les types</option>
                            <option value="consultation">Consultation</option>
                            <option value="follow_up">Suivi</option>
                            <option value="emergency">Urgence</option>
                            <option value="routine">Routine</option>
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

            <!-- Tableau des rendez-vous -->
            <div class="overflow-x-auto bg-white shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durée</th>
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
                                data-status="{{ $rdv->status }}"
                                data-type="{{ $rdv->appointment_type }}"
                                data-date="{{ $rdv->appointment_date->format('Y-m-d') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $rdv->appointment_date->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @php
                                            $timeString = $rdv->appointment_time;
                                            if (strlen($timeString) > 5) {
                                                $timeString = substr($timeString, 0, 5);
                                            }
                                        @endphp
                                        {{ $timeString }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $rdv->patient->nom ?? 'Inconnu' }} {{ $rdv->patient->prenom ?? '' }}
                                    </div>
                                    @if($rdv->patient && $rdv->patient->telephone)
                                        <div class="text-xs text-gray-500">{{ $rdv->patient->telephone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'consultation' => 'bg-blue-100 text-blue-800',
                                            'follow_up' => 'bg-purple-100 text-purple-800',
                                            'emergency' => 'bg-red-100 text-red-800',
                                            'routine' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $typeLabels = [
                                            'consultation' => 'Consultation',
                                            'follow_up' => 'Suivi',
                                            'emergency' => 'Urgence',
                                            'routine' => 'Routine'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$rdv->appointment_type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$rdv->appointment_type] ?? ucfirst($rdv->appointment_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->duration }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-green-100 text-green-800', 
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'confirmed' => 'Confirmé',
                                            'completed' => 'Terminé', 
                                            'cancelled' => 'Annulé'
                                        ];
                                        $statusIcons = [
                                            'pending' => 'fas fa-clock',
                                            'confirmed' => 'fas fa-check-circle',
                                            'completed' => 'fas fa-check-double',
                                            'cancelled' => 'fas fa-times-circle'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$rdv->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i class="{{ $statusIcons[$rdv->status] ?? 'fas fa-question' }} mr-1"></i>
                                        {{ $statusLabels[$rdv->status] ?? ucfirst($rdv->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $rdv->reason ?? 'Aucun motif' }}">
                                        {{ $rdv->reason ?? 'Aucun motif' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='openEditModal(@json($rdv))'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRendezVous('{{ $rdv->id }}')"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
                                            title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($rdv->status === 'cancelled' && $rdv->cancelled_at)
                                            <button onclick="showCancellationInfo('{{ $rdv->cancelled_at->format('d/m/Y H:i') }}', '{{ $rdv->cancellation_reason ?? 'Aucune raison spécifiée' }}')"
                                                class="text-gray-600 hover:text-gray-800 transition-colors p-1 rounded hover:bg-gray-50"
                                                title="Info annulation">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
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
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Nouveau rendez-vous</h2>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="{{ route('secretaire.rendezvous.store') }}" method="POST" class="space-y-4" id="addForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                        <select name="patient_id" id="patient_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="">Sélectionnez un patient</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom }} {{ $patient->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                        @if(Auth::user()->role === 'medecin')
                            <input type="text" value="Dr. {{ Auth::user()->nom ?? 'Médecin' }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                        @elseif(Auth::user()->role === 'secretaire' && Auth::user()->medecin_id)
                            <input type="text" value="Dr. {{ Auth::user()->medecin->nom ?? 'Médecin' }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" name="appointment_date" id="appointment_date" required 
                            min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Heure *</label>
                        <input type="time" name="appointment_time" id="appointment_time" required 
                            value="{{ old('appointment_time') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durée (min)</label>
                        <input type="number" name="duration" id="duration" min="15" max="180" step="15" 
                            value="{{ old('duration', 30) }}" placeholder="30"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div>
                        <label for="appointment_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="appointment_type" id="appointment_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="consultation" {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                            <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                            <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                            <option value="routine" {{ old('appointment_type') == 'routine' ? 'selected' : '' }}>Routine</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif de consultation</label>
                    <textarea name="reason" id="reason" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Décrivez le motif de la consultation...">{{ old('reason') }}</textarea>
                </div>

                <div>
                    <label for="patient_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes du patient</label>
                    <textarea name="patient_notes" id="patient_notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Notes ou remarques du patient...">{{ old('patient_notes') }}</textarea>
                </div>

                @if(Auth::user()->role === 'medecin')
                <div>
                    <label for="doctor_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes du médecin</label>
                    <textarea name="doctor_notes" id="doctor_notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Notes médicales...">{{ old('doctor_notes') }}</textarea>
                </div>
                @endif

                <div id="addCancellationFields" class="hidden">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-1">Raison de l'annulation</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Expliquez la raison de l'annulation...">{{ old('cancellation_reason') }}</textarea>
                </div>

                <div>
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                    <textarea name="feedback" id="feedback" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Commentaires ou feedback...">{{ old('feedback') }}</textarea>
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
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                        <select name="patient_id" id="edit_patient_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="">Sélectionnez un patient</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                        @if(Auth::user()->role === 'medecin')
                            <input type="text" value="Dr. {{ Auth::user()->nom ?? 'Médecin' }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                        @elseif(Auth::user()->role === 'secretaire' && Auth::user()->medecin_id)
                            <input type="text" value="Dr. {{ Auth::user()->medecin->nom ?? 'Médecin' }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="edit_appointment_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" name="appointment_date" id="edit_appointment_date" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="edit_appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Heure *</label>
                        <input type="time" name="appointment_time" id="edit_appointment_time" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                    <div>
                        <label for="edit_duration" class="block text-sm font-medium text-gray-700 mb-1">Durée (min)</label>
                        <input type="number" name="duration" id="edit_duration" min="15" max="180" step="15"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                        <select name="status" id="edit_status" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="pending">En attente</option>
                            <option value="confirmed">Confirmé</option>
                            <option value="completed">Terminé</option>
                            <option value="cancelled">Annulé</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_appointment_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="appointment_type" id="edit_appointment_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            <option value="consultation">Consultation</option>
                            <option value="follow_up">Suivi</option>
                            <option value="emergency">Urgence</option>
                            <option value="routine">Routine</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit_reason" class="block text-sm font-medium text-gray-700 mb-1">Motif de consultation</label>
                    <textarea name="reason" id="edit_reason" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Décrivez le motif de la consultation..."></textarea>
                </div>

                <div>
                    <label for="edit_patient_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes du patient</label>
                    <textarea name="patient_notes" id="edit_patient_notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Notes ou remarques du patient..."></textarea>
                </div>

                @if(Auth::user()->role === 'medecin')
                <div>
                    <label for="edit_doctor_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes du médecin</label>
                    <textarea name="doctor_notes" id="edit_doctor_notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Notes médicales..."></textarea>
                </div>
                @endif

                <div id="cancellationFields" class="hidden">
                    <label for="edit_cancellation_reason" class="block text-sm font-medium text-gray-700 mb-1">Raison de l'annulation</label>
                    <textarea name="cancellation_reason" id="edit_cancellation_reason" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Expliquez la raison de l'annulation..."></textarea>
                </div>

                <div>
                    <label for="edit_feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                    <textarea name="feedback" id="edit_feedback" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                        placeholder="Commentaires ou feedback..."></textarea>
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

            if (!selectedDate || !selectedTime) {
                return {
                    valid: false,
                    message: 'Veuillez sélectionner une date et une heure.'
                };
            }

            const selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
            const currentDateTimeObj = new Date();

            if (selectedDateTime <= currentDateTimeObj) {
                return {
                    valid: false,
                    message: 'La date et l\'heure du rendez-vous doivent être postérieures à maintenant.'
                };
            }

            return { valid: true };
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

            requestAnimationFrame(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
            });

            messageDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

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
            const statusFilter = document.getElementById('statusFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;

            const rows = document.querySelectorAll('.rdv-row');
            const noRdvRow = document.getElementById('noRdvRow');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const statusData = row.getAttribute('data-status');
                const typeData = row.getAttribute('data-type');
                const dateData = row.getAttribute('data-date');

                const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                const matchesStatus = !statusFilter || statusData === statusFilter;
                const matchesType = !typeFilter || typeData === typeFilter;
                const matchesDate = !dateFilter || dateData === dateFilter;

                if (matchesSearch && matchesStatus && matchesType && matchesDate) {
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
                `${visibleCount} rendez-vous trouvé${visibleCount !== 1 ? 's' : ''}`;
        }

        // Fonctions pour les modales
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(rdv) {
            console.log('Opening edit modal with data:', rdv);
            
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('editForm').action = `/secretaire/rendezvous/${rdv.id}`;
            document.getElementById('edit_id').value = rdv.id;
            document.getElementById('edit_patient_id').value = rdv.patient_id;

            // Gestion correcte de la date
            let dateValue = '';
            if (rdv.appointment_date_formatted) {
                dateValue = rdv.appointment_date_formatted;
            } else if (rdv.appointment_date) {
                // Si c'est un objet Carbon de Laravel sérialisé
                if (typeof rdv.appointment_date === 'object' && rdv.appointment_date.date) {
                    dateValue = rdv.appointment_date.date.split(' ')[0];
                } else if (typeof rdv.appointment_date === 'string') {
                    // Si c'est une chaîne de date
                    dateValue = rdv.appointment_date.split(' ')[0];
                }
            }
            
            console.log('Setting date to:', dateValue);
            document.getElementById('edit_appointment_date').value = dateValue;
            
            // Gestion correcte de l'heure
            let timeValue = '';
            if (rdv.appointment_time_formatted) {
                timeValue = rdv.appointment_time_formatted;
            } else if (rdv.appointment_time) {
                timeValue = rdv.appointment_time;
                if (typeof timeValue === 'string' && timeValue.length > 5) {
                    timeValue = timeValue.substring(0, 5); // Get HH:MM format
                }
            }
            
            console.log('Setting time to:', timeValue);
            document.getElementById('edit_appointment_time').value = timeValue;
            
            // Remplir les autres champs
            document.getElementById('edit_duration').value = parseInt(rdv.duration) || 30;
            document.getElementById('edit_status').value = rdv.status;
            document.getElementById('edit_appointment_type').value = rdv.appointment_type;
            document.getElementById('edit_reason').value = rdv.reason || '';
            document.getElementById('edit_patient_notes').value = rdv.patient_notes || '';
            
            // Notes du médecin seulement si l'élément existe
            const doctorNotesField = document.getElementById('edit_doctor_notes');
            if (doctorNotesField) {
                doctorNotesField.value = rdv.doctor_notes || '';
            }
            
            document.getElementById('edit_cancellation_reason').value = rdv.cancellation_reason || '';
            document.getElementById('edit_feedback').value = rdv.feedback || '';

            // Show/hide cancellation fields
            toggleCancellationFields();
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function toggleCancellationFields() {
            const statusSelect = document.getElementById('edit_status');
            const cancellationFields = document.getElementById('cancellationFields');
            
            if (statusSelect.value === 'cancelled') {
                cancellationFields.classList.remove('hidden');
            } else {
                cancellationFields.classList.add('hidden');
            }
        }

        function toggleAddCancellationFields() {
            const statusSelect = document.getElementById('status');
            const cancellationFields = document.getElementById('addCancellationFields');
            
            if (statusSelect.value === 'cancelled') {
                cancellationFields.classList.remove('hidden');
            } else {
                cancellationFields.classList.add('hidden');
            }
        }

        function showCancellationInfo(cancelledAt, reason) {
            alert(`Annulé le: ${cancelledAt}\nRaison: ${reason}`);
        }

        function deleteRendezVous(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
                fetch(`/secretaire/rendezvous/${id}`, {
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

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé');

            setupAutoCloseMessages();

            // Événements de filtrage
            document.getElementById('searchInput').addEventListener('input', filterRendezVous);
            document.getElementById('statusFilter').addEventListener('change', filterRendezVous);
            document.getElementById('typeFilter').addEventListener('change', filterRendezVous);
            document.getElementById('dateFilter').addEventListener('change', filterRendezVous);

            // Toggle cancellation fields when status changes
            document.getElementById('edit_status').addEventListener('change', toggleCancellationFields);
            document.getElementById('status').addEventListener('change', toggleAddCancellationFields);

            // Validation du formulaire d'ajout
            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    const dateInput = document.getElementById('appointment_date');
                    const timeInput = document.getElementById('appointment_time');
                    
                    // Seulement valider pour les statuts non-annulés
                    const statusSelect = document.getElementById('status');
                    if (statusSelect.value !== 'cancelled') {
                        const validation = validateDateTime(dateInput, timeInput);
                        if (!validation.valid) {
                            e.preventDefault();
                            showMessage(validation.message, 'error');
                            return false;
                        }
                    }
                });
            }

            // Vérifier s'il y a des erreurs pour rouvrir le modal
            @if ($errors->any() && old('_token')) 
                setTimeout(() => {
                    openAddModal();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            @endif

            @if (session('success'))
                window.scrollTo({ top: 0, behavior: 'smooth' });
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