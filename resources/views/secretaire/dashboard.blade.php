<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C-M Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cordes-blue': '#1e40af',
                        'cordes-dark': '#1e293b',
                        'cordes-light': '#f8fafc',
                        'cordes-accent': '#3b82f6'
                    }
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Sidebar -->
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
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-file-medical mr-3 text-white"></i>
                        Dossier Médical
                    </a>
                @endif
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

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Overview</h1>
                        <p class="text-gray-600 text-sm mt-1">Welcome back!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        {{-- <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        </div> --}}
                        <div class="relative">
                            <button
                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard Content -->
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

            <div class="px-0 py-6">
                <!-- Action Buttons Row -->
                <div class="flex items-center justify-center space-x-6">
                    <!-- Ajouter RDV Button -->
                    <button onclick="openAddModal()"
                        class="flex-1 max-w-xl bg-cordes-blue hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-calendar-plus text-xl mb-2"></i>
                        <div>Ajouter RDV</div>
                    </button>

                    <!-- Ajouter Patient Button -->
                    <button onclick="openPatientModal()"
                        class="flex-1 max-w-xl bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-user-plus text-xl mb-2"></i>
                        <div>Ajouter Patient</div>
                    </button>

                    <!-- Generer Facture Button -->
                    <button onclick="openFactureModal()"
                        class="flex-1 max-w-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-file-invoice text-xl mb-2"></i>
                        <div>Generer Facture</div>
                    </button>
                </div>
            </div>

            <!-- MODAL AJOUTER RDV -->
            <div id="addModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
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
                                @isset($patients)
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}
                                        </option>
                                    @endforeach
                                @endisset

                            </select>
                        </div>
                        <div>
                            <label for="medecin_id"
                                class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                            <select name="medecin_id" id="medecin_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="">Sélectionnez un médecin</option>
                                @isset($patients)
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}
                                        </option>
                                    @endforeach
                                @endisset

                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" id="date" required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent" />
                            </div>
                            <div>
                                <label for="heure"
                                    class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
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

            <!-- MODAL AJOUTER PATIENT -->
            <div id="patientModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-lg rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau patient</h2>
                        <button onclick="closePatientModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <form action="{{ route('patients.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="patient_cin" class="block text-sm font-medium text-gray-700 mb-1">CIN
                                *</label>
                            <input type="text" name="cin" id="patient_cin" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: AB123456">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="patient_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom
                                    *</label>
                                <input type="text" name="nom" id="patient_nom" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                            </div>

                            <div>
                                <label for="patient_sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe
                                    *</label>
                                <select name="sexe" id="patient_sexe" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionner</option>
                                    <option value="homme">Homme</option>
                                    <option value="femme">Femme</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="patient_date_naissance"
                                class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                            <input type="date" name="date_naissance" id="patient_date_naissance" required
                                max="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                        </div>

                        <div>
                            <label for="patient_contact"
                                class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                            <input type="tel" name="contact" id="patient_contact"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 0612345678">
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closePatientModal()"
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

            <!-- MODAL GENERER FACTURE -->
            <div id="factureModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-md rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Générer Facture</h2>
                        <button onclick="closeFactureModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    {{-- <form action="{{ route('secretaire.factureStore') }}" method="POST" class="space-y-4"> --}}
                    <form action={{ route('factures.create') }} method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Patient (CNI)</label>
                            <div class="relative">
                                <select id="patientSelect" name="patient_id" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-accent focus:border-transparent">
                                    <option value="">Tapez CNI ou nom...</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">
                                            {{ $patient->cin }} | {{ $patient->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                            <select name="medecin_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                                <option value="">Sélectionner un médecin</option>
                                @foreach ($medecins as $medecin)
                                    <option value="{{ $medecin->id }}">Dr.{{ $medecin->nom }} | N°
                                        {{ $medecin->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secrétaire</label>
                            <select name="secretaire_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                                <option value="">Sélectionner un secrétaire</option>
                                @foreach ($secretaires as $secretaire)
                                    <option value="{{ $secretaire->id }}">Sec.{{ $secretaire->nom }} | N°
                                        {{ $secretaire->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" name="date" id="currentDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                            <input type="number" name="montant" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                        </div>
                        <div>
                            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="statut" id="statut" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="en attente">En attente</option>
                                <option value="payée">Payée</option>
                            </select>
                        </div>
                        <div class="flex space-x-3 pt-4">
                            <button type="button" onclick="closeFactureModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                <i class="fas fa-save mr-2"></i>Générer Facture
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Revenue Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Factures</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_facts }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-cordes-blue bg-opacity-10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-cordes-blue text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Users Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Patients</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_pats }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Orders Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Paiements</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_pais }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Products Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rendez-vous</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_rvs }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Factures Table -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Latest Factures Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Latest Factures</h3>
                            <p class="text-gray-600 text-sm">Dernières factures émises</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-cordes-blue hover:text-cordes-dark text-sm font-medium">View
                                All</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        N°</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Médecin</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Patient</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montant</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($latest_facs as $facture)
                                    <tr class="hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span
                                                class="text-sm font-medium text-gray-900">#{{ $facture->id }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $facture->medecin->nom ?? 'Dr. Inconnu' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $facture->patient->nom ?? 'Patient Inconnu' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if ($facture->statut == 'payée')
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Payée
                                                </span>
                                            @elseif($facture->statut == 'en attente')
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($facture->statut) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            ${{ number_format($facture->montant, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Rendez-vous du Jour -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Rendez-vous du Jour</h3>
                        <button class="text-cordes-blue hover:text-cordes-dark text-sm font-medium">View All</button>
                    </div>
                    <div class="space-y-4">
                        @foreach ($latest_rvs->take(4) as $rdv)
                            <div
                                class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $rdv->patient->nom ?? 'Patient' }}</p>
                                    <p class="text-sm text-gray-600">{{ $rdv->medecin->nom ?? 'Dr. Inconnu' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($rdv->heure)->format('H:i') }}</p>
                                    @if ($rdv->statut == 'confirmé')
                                        <span
                                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Confirmé</span>
                                    @elseif($rdv->statut == 'en attente')
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">En
                                            attente</span>
                                    @else
                                        <span
                                            class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">{{ ucfirst($rdv->statut) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </main>
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
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '<i class="fas fa-times"></i>';
                    closeButton.className =
                        'float-right text-current opacity-70 hover:opacity-100 transition-opacity ml-2';
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

        function validateDateTime(dateValue, timeValue) {
            const now = new Date();
            const selectedDateTime = new Date(dateValue + 'T' + timeValue);

            if (selectedDateTime <= now) {
                alert('La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.');
                return false;
            }
            return true;
        }

        function updateMinTime(dateInput, timeInput) {
            const selectedDate = dateInput.value;
            const today = new Date().toISOString().split('T')[0];

            if (selectedDate === today) {
                const now = new Date();
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' +
                    now.getMinutes().toString().padStart(2, '0');
                timeInput.min = currentTime;
            } else {
                timeInput.removeAttribute('min');
            }
        }

        // RDV Modal Functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.querySelector('#addModal form').reset();

            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('heure');

            dateInput.addEventListener('change', function() {
                updateMinTime(dateInput, timeInput);
            });

            document.querySelector('#addModal form').addEventListener('submit', function(e) {
                const dateValue = dateInput.value;
                const timeValue = timeInput.value;
                const medecinId = document.getElementById('medecin_id').value;

                if (!validateDateTime(dateValue, timeValue)) {
                    e.preventDefault();
                    return;
                }
            });
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // PATIENT Modal Functions
        function openPatientModal() {
            document.getElementById('patientModal').classList.remove('hidden');
            document.querySelector('#patientModal form').reset();
        }

        function closePatientModal() {
            document.getElementById('patientModal').classList.add('hidden');
        }

        // FACTURE Modal Functions - PROPERLY INTEGRATED
        function openFactureModal() {
            document.getElementById('factureModal').classList.remove('hidden');
            document.querySelector('#factureModal form').reset();
            // Set current date
            // document.getElementById('currentDate').value = new Date().toISOString().split('T')[0];
        }

        function closeFactureModal() {
            document.getElementById('factureModal').classList.add('hidden');
        }

        // Event Listeners for clicking outside modals
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        document.getElementById('patientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePatientModal();
            }
        });

        // FACTURE MODAL EVENT LISTENER
        document.getElementById('factureModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFactureModal();
            }
        });

        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closePatientModal();
                closeFactureModal(); // ADDED THIS
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();

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

            navLinks[0].classList.add('bg-gray-700', 'text-white');
            navLinks[0].classList.remove('text-gray-300');

            const bellIcon = document.querySelector('.fa-bell');
            if (bellIcon) {
                setInterval(() => {
                    bellIcon.classList.add('animate-pulse');
                    setTimeout(() => {
                        bellIcon.classList.remove('animate-pulse');
                    }, 1000);
                }, 5000);
            }

            const statsCards = document.querySelectorAll('.hover\\:shadow-md');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>

</html>
