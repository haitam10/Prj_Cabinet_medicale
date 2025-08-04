<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Patients</title>
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
        /* Style pour les nouveaux patients */
        .nouveau-patient {
            background-color: #dcfce7 !important;
            border-left: 4px solid #16a34a !important;
            animation: pulseGreen 2s infinite;
        }
        .nouveau-patient:hover {
            background-color: #bbf7d0 !important;
        }
        @keyframes pulseGreen {
            0%, 100% {
                background-color: #dcfce7;
            }
            50% {
                background-color: #bbf7d0;
            }
        }
        /* Style pour l'indicateur "Nouveau" */
        .badge-nouveau {
            background-color: #16a34a;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 8px;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-64 bg-cordes-dark shadow-xl z-50 flex flex-col">
        <div class="flex items-center justify-center h-16 bg-cordes-light flex-shrink-0">
            <div class="flex items-center space-x-3">
                <img style="width: 180px; height:160px" src="{{ url('storage/uploads/logo_miacex.png') }}" />
            </div>
        </div>
        <!-- Nav scrollable -->
        <nav class="mt-8 px-4 flex-1 overflow-y-auto pb-28"> <!-- pb-28 = padding bottom important -->
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
                    class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                    <i class="fas fa-user mr-3 text-cordes-accent group-hover:text-white"></i>
                    Mon Profil
                </a>
            </div>
        </nav>
        <!-- User Profile / Logout fixed bottom -->
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
                                    class="text-red-400">Se déconnecter</span></p>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Patients</h1>
                    <p class="text-gray-600 text-sm mt-1">Liste des patients enregistrés</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter Patient
                </button>
            </div>
        </header>

        <main class="p-6">
            @if (session('success'))
                <div id="successMessage"
                    class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-opacity duration-500 shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-600 text-xl"></i>
                        <div>
                            <div class="mt-1">{{ session('success') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div id="errorMessage"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500 shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-600 text-xl"></i>
                        <div>
                            <div class="mt-1">{{ session('error') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div id="validationErrors"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500 shadow-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-3 mt-1 text-red-600 text-xl"></i>
                        <div class="flex-1">
                            @php
                                $cinError = $errors->first('cin');
                                $hasUniqueError = $cinError && str_contains($cinError, 'existe déjà');
                            @endphp
                            @if ($hasUniqueError)
                                <div class="font-bold text-xl text-red-800">🚫 PATIENT DÉJÀ EXISTANT !</div>
                                <div class="mt-2 text-red-700 text-lg">{{ $cinError }}</div>
                                <div class="mt-2 text-red-600 text-sm">Veuillez vérifier le CIN saisi ou rechercher le
                                    patient existant.</div>
                            @else
                                <div class="font-bold text-lg text-red-800">❌ Erreurs de validation :</div>
                                <div class="mt-2 space-y-2">
                                    @foreach ($errors->all() as $error)
                                        <div class="flex items-start">
                                            <i class="fas fa-exclamation-triangle mr-2 mt-0.5 text-red-600 text-sm"></i>
                                            <span class="text-red-700">{{ $error }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un patient..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                            autocomplete="off">
                    </div>
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select id="sexeFilter"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent appearance-none"
                            autocomplete="sex">
                            <option value="">Tous les sexes</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-users mr-2"></i>
                        <span id="patientCount">{{ $patients->total() }}
                            patient{{ $patients->total() > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom complet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sexe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Âge</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Groupe Sanguin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ajouté le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="patientsTableBody">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-gray-50 transition-colors patient-row"
                                data-search="{{ strtolower($patient->nom . ' ' . $patient->cin) }}"
                                data-sexe="{{ $patient->sexe }}"
                                data-patient-id="{{ $patient->id }}"
                                onclick="marquerPatientCommeVu({{ $patient->id }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                        @if($patient->profile_image && Storage::disk('public')->exists($patient->profile_image))
                                            <img src="{{ asset('storage/' . $patient->profile_image) }}" alt="Photo de {{ $patient->nom }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user text-gray-400"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $patient->cin }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $patient->sexe === 'homme' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $patient->nom }}
                                                <span class="badge-nouveau" id="badge-{{ $patient->id }}" style="display: none;">NOUVEAU</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $patient->sexe === 'homme' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        {{ ucfirst($patient->sexe) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-coins text-green-600 mr-2"></i>
                                        <span class="font-medium text-green-600">
                                            {{ number_format($patient->montant_paye ?? 0, 2, ',', ' ') }} DH
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($patient->contact)
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-400 mr-1 text-xs"></i>
                                            {{ $patient->contact }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Non renseigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($patient->email)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-1 text-xs"></i>
                                            {{ $patient->email }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Non renseigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($patient->groupe_sanguin)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $patient->groupe_sanguin }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Non renseigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $patient->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $patient->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $patient->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if (Auth::check() && Auth::user()->role === 'medecin')
                                            <button onclick="viewPatientDetails({{ $patient->id }}); event.stopPropagation();"
                                                class="text-yellow-600 hover:text-yellow-800 transition-colors p-1 rounded hover:bg-yellow-50"
                                                title="Voir les détails du patient">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                        <button onclick='openEditModal({{ $patient->id }}); event.stopPropagation();'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePatient({{ $patient->id }}); event.stopPropagation();"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noPatientRow">
                                <td colspan="12" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-user-times text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-lg">Aucun patient trouvé</p>
                                    <p class="text-sm mt-1">Commencez par ajouter un nouveau patient.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($patients->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $patients->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- MODAL AJOUTER PATIENT -->
    <div id="addModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau patient</h2>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="{{ route('secretaire.patients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <!-- Photo de profil -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Photo de profil</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden" id="profilePreview">
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        </div>
                        <div>
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Image de profil</label>
                            <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('profile_image') ? 'border-red-500 bg-red-50' : '' }}"
                                onchange="previewImage(this, 'profilePreview')">
                            @if ($errors->has('profile_image'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('profile_image') }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)</p>
                        </div>
                    </div>
                </div>
                <!-- Informations de base -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="cin" class="block text-sm font-medium text-gray-700 mb-1">CIN *</label>
                            <input type="text" name="cin" id="cin" required
                                value="{{ old('cin') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('cin') ? 'border-red-500 bg-red-50' : '' }}"
                                placeholder="Ex: AB123456" autocomplete="off">
                            @if ($errors->has('cin'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('cin') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom complet
                                *</label>
                            <input type="text" name="nom" id="nom" required
                                value="{{ old('nom') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('nom') ? 'border-red-500 bg-red-50' : '' }}"
                                autocomplete="name">
                            @if ($errors->has('nom'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('nom') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe *</label>
                            <select name="sexe" id="sexe" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('sexe') ? 'border-red-500 bg-red-50' : '' }}"
                                autocomplete="sex">
                                <option value="">Sélectionner</option>
                                <option value="homme" {{ old('sexe') == 'homme' ? 'selected' : '' }}>Homme</option>
                                <option value="femme" {{ old('sexe') == 'femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                            @if ($errors->has('sexe'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('sexe') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-1">Date de
                                naissance *</label>
                            <input type="date" name="date_naissance" id="date_naissance" required
                                value="{{ old('date_naissance') }}" max="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('date_naissance') ? 'border-red-500 bg-red-50' : '' }}"
                                autocomplete="bday">
                            @if ($errors->has('date_naissance'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('date_naissance') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="profession"
                                class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                            <input type="text" name="profession" id="profession" value="{{ old('profession') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="organization-title">
                        </div>
                        <div>
                            <label for="situation_familiale"
                                class="block text-sm font-medium text-gray-700 mb-1">Situation familiale</label>
                            <select name="situation_familiale" id="situation_familiale"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="off">
                                <option value="">Sélectionner</option>
                                <option value="celibataire"
                                    {{ old('situation_familiale') == 'celibataire' ? 'selected' : '' }}>Célibataire
                                </option>
                                <option value="marie" {{ old('situation_familiale') == 'marie' ? 'selected' : '' }}>
                                    Marié(e)</option>
                                <option value="divorce"
                                    {{ old('situation_familiale') == 'divorce' ? 'selected' : '' }}>Divorcé(e)</option>
                                <option value="veuf" {{ old('situation_familiale') == 'veuf' ? 'selected' : '' }}>
                                    Veuf/Veuve</option>
                            </select>
                        </div>
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="is_active" id="is_active"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Informations de contact -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">Téléphone
                                principal *</label>
                            <input type="tel" name="contact" id="contact" required
                                value="{{ old('contact') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('contact') ? 'border-red-500 bg-red-50' : '' }}"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                            @if ($errors->has('contact'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('contact') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="telephone_secondaire"
                                class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
                            <input type="tel" name="telephone_secondaire" id="telephone_secondaire"
                                value="{{ old('telephone_secondaire') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('email') ? 'border-red-500 bg-red-50' : '' }}"
                                placeholder="exemple@email.com" autocomplete="email">
                            @if ($errors->has('email'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <textarea name="adresse" id="adresse" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Adresse complète" autocomplete="street-address">{{ old('adresse') }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- Contact d'urgence -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Contact d'urgence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact d'urgence</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                 value="{{ old('emergency_contact_name') }}" maxlength="100"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('emergency_contact_name') ? 'border-red-500 bg-red-50' : '' }}"
                                placeholder="Nom complet du contact" autocomplete="name">
                            @if ($errors->has('emergency_contact_name'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('emergency_contact_name') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone du contact d'urgence</label>
                            <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone"
                                 value="{{ old('emergency_contact_phone') }}" maxlength="20"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent {{ $errors->has('emergency_contact_phone') ? 'border-red-500 bg-red-50' : '' }}"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                            @if ($errors->has('emergency_contact_phone'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('emergency_contact_phone') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Informations médicales -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations médicales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label for="groupe_sanguin" class="block text-sm font-medium text-gray-700 mb-1">Groupe
                                sanguin</label>
                            <select name="groupe_sanguin" id="groupe_sanguin"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="off">
                                <option value="">Sélectionner</option>
                                <option value="A+" {{ old('groupe_sanguin') == 'A+' ? 'selected' : '' }}>A+
                                </option>
                                <option value="A-" {{ old('groupe_sanguin') == 'A-' ? 'selected' : '' }}>A-
                                </option>
                                <option value="B+" {{ old('groupe_sanguin') == 'B+' ? 'selected' : '' }}>B+
                                </option>
                                <option value="B-" {{ old('groupe_sanguin') == 'B-' ? 'selected' : '' }}>B-
                                </option>
                                <option value="AB+" {{ old('groupe_sanguin') == 'AB+' ? 'selected' : '' }}>AB+
                                </option>
                                <option value="AB-" {{ old('groupe_sanguin') == 'AB-' ? 'selected' : '' }}>AB-
                                </option>
                                <option value="O+" {{ old('groupe_sanguin') == 'O+' ? 'selected' : '' }}>O+
                                </option>
                                <option value="O-" {{ old('groupe_sanguin') == 'O-' ? 'selected' : '' }}>O-
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="poids" class="block text-sm font-medium text-gray-700 mb-1">Poids
                                (kg)</label>
                            <input type="number" name="poids" id="poids" step="0.1" min="0"
                                max="999.99" value="{{ old('poids') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 70.5" autocomplete="off">
                        </div>
                        <div>
                            <label for="taille" class="block text-sm font-medium text-gray-700 mb-1">Taille
                                (cm)</label>
                            <input type="number" name="taille" id="taille" step="0.1" min="0"
                                max="999.99" value="{{ old('taille') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 175" autocomplete="off">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="allergies"
                                class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                            <textarea name="allergies" id="allergies" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Allergies connues..." autocomplete="off">{{ old('allergies') }}</textarea>
                        </div>
                        <div>
                            <label for="antecedents"
                                class="block text-sm font-medium text-gray-700 mb-1">Antécédents</label>
                            <textarea name="antecedents" id="antecedents" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Antécédents médicaux..." autocomplete="off">{{ old('antecedents') }}</textarea>
                        </div>
                        <div>
                            <label for="medicaments"
                                class="block text-sm font-medium text-gray-700 mb-1">Médicaments</label>
                            <textarea name="medicaments" id="medicaments" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Médicaments actuels..." autocomplete="off">{{ old('medicaments') }}</textarea>
                        </div>
                    </div>
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

    <!-- MODAL MODIFIER PATIENT -->
    <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Modifier le patient</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <!-- Photo de profil -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Photo de profil</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden" id="editProfilePreview">
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        </div>
                        <div>
                            <label for="edit_profile_image" class="block text-sm font-medium text-gray-700 mb-1">Nouvelle image de profil</label>
                            <input type="file" name="profile_image" id="edit_profile_image" accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                onchange="previewImage(this, 'editProfilePreview')">
                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)</p>
                        </div>
                    </div>
                </div>
                <!-- Informations de base -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="edit_cin" class="block text-sm font-medium text-gray-700 mb-1">CIN *</label>
                            <input type="text" name="cin" id="edit_cin" required readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                                placeholder="Ex: AB123456" autocomplete="off">
                        </div>
                        <div>
                            <label for="edit_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom complet
                                *</label>
                            <input type="text" name="nom" id="edit_nom" required readonly
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                                autocomplete="name">
                        </div>
                        <div>
                            <label for="edit_sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe *</label>
                            <select name="sexe" id="edit_sexe" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="sex">
                                <option value="">Sélectionner</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_date_naissance" class="block text-sm font-medium text-gray-700 mb-1">Date
                                de naissance *</label>
                            <input type="date" name="date_naissance" id="edit_date_naissance" required
                                max="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="bday">
                        </div>
                        <div>
                            <label for="edit_profession"
                                class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                            <input type="text" name="profession" id="edit_profession"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="organization-title">
                        </div>
                        <div>
                            <label for="edit_situation_familiale"
                                class="block text-sm font-medium text-gray-700 mb-1">Situation familiale</label>
                            <select name="situation_familiale" id="edit_situation_familiale"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="off">
                                <option value="">Sélectionner</option>
                                <option value="celibataire">Célibataire</option>
                                <option value="marie">Marié(e)</option>
                                <option value="divorce">Divorcé(e)</option>
                                <option value="veuf">Veuf/Veuve</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_is_active" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="is_active" id="edit_is_active"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Informations de contact -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_contact" class="block text-sm font-medium text-gray-700 mb-1">Téléphone
                                principal *</label>
                            <input type="tel" name="contact" id="edit_contact" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                        </div>
                        <div>
                            <label for="edit_telephone_secondaire"
                                class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
                            <input type="tel" name="telephone_secondaire" id="edit_telephone_secondaire"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="exemple@email.com" autocomplete="email">
                        </div>
                        <div>
                            <label for="edit_adresse"
                                class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <textarea name="adresse" id="edit_adresse" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Adresse complète" autocomplete="street-address"></textarea>
                        </div>
                    </div>
                </div>
                <!-- Contact d'urgence -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Contact d'urgence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du contact d'urgence</label>
                            <input type="text" name="emergency_contact_name" id="edit_emergency_contact_name"
                                 maxlength="100"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Nom complet du contact" autocomplete="name">
                        </div>
                        <div>
                            <label for="edit_emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone du contact d'urgence</label>
                            <input type="tel" name="emergency_contact_phone" id="edit_emergency_contact_phone"
                                 maxlength="20"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 0612345678" autocomplete="tel">
                        </div>
                    </div>
                </div>
                <!-- Informations médicales -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Informations médicales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label for="edit_groupe_sanguin"
                                class="block text-sm font-medium text-gray-700 mb-1">Groupe sanguin</label>
                            <select name="groupe_sanguin" id="edit_groupe_sanguin"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                autocomplete="off">
                                <option value="">Sélectionner</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_poids" class="block text-sm font-medium text-gray-700 mb-1">Poids
                                (kg)</label>
                            <input type="number" name="poids" id="edit_poids" step="0.1" min="0"
                                max="999.99"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 70.5" autocomplete="off">
                        </div>
                        <div>
                            <label for="edit_taille" class="block text-sm font-medium text-gray-700 mb-1">Taille
                                (cm)</label>
                            <input type="number" name="taille" id="edit_taille" step="0.1" min="0"
                                max="999.99"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Ex: 175" autocomplete="off">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="edit_allergies"
                                class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                            <textarea name="allergies" id="edit_allergies" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Allergies connues..." autocomplete="off"></textarea>
                        </div>
                        <div>
                            <label for="edit_antecedents"
                                class="block text-sm font-medium text-gray-700 mb-1">Antécédents</label>
                            <textarea name="antecedents" id="edit_antecedents" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Antécédents médicaux..." autocomplete="off"></textarea>
                        </div>
                        <div>
                            <label for="edit_medicaments"
                                class="block text-sm font-medium text-gray-700 mb-1">Médicaments</label>
                            <textarea name="medicaments" id="edit_medicaments" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Médicaments actuels..." autocomplete="off"></textarea>
                        </div>
                    </div>
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

    <!-- MODAL VOIR DÉTAILS PATIENT -->
    <div id="viewDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-7xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Détails du Patient</h2>
                <button onclick="closeViewDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <!-- Informations globales du patient -->
            <div class="bg-blue-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-3"></i>Informations Globales
                </h3>
                <div id="patientGlobalInfo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les informations seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Consultations -->
            <div class="bg-green-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                    <i class="fas fa-stethoscope mr-3"></i>Consultations
                </h3>
                <div id="consultationsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les consultations seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Ordonnances -->
            <div class="bg-purple-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-purple-800 mb-4 flex items-center">
                    <i class="fas fa-prescription-bottle-medical mr-3"></i>Ordonnances
                </h3>
                <div id="ordonnancesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les ordonnances seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Certificats -->
            <div class="bg-orange-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-orange-800 mb-4 flex items-center">
                    <i class="fas fa-certificate mr-3"></i>Certificats
                </h3>
                <div id="certificatsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les certificats seront injectés ici via JavaScript -->
                </div>
            </div>
            <!-- Remarques -->
            <div class="bg-yellow-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-yellow-800 mb-4 flex items-center">
                    <i class="fas fa-sticky-note mr-3"></i>Remarques
                </h3>
                <div id="remarquesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les remarques seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Habitudes de Vie -->
            <div class="bg-indigo-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center">
                    <i class="fas fa-heart mr-3"></i>Habitudes de Vie
                </h3>
                <div id="habitudesVieList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les habitudes de vie seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Examens Biologiques -->
            <div class="bg-red-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-red-800 mb-4 flex items-center">
                    <i class="fas fa-vial mr-3"></i>Examens Biologiques
                </h3>
                <div id="examensBiologiquesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les examens biologiques seront injectés ici via JavaScript -->
                </div>
            </div>
            <!-- Imagerie Médicale -->
            <div class="bg-teal-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-teal-800 mb-4 flex items-center">
                    <i class="fas fa-x-ray mr-3"></i>Imagerie Médicale
                </h3>
                <div id="imagerieMedicaleList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- L'imagerie médicale sera injectée ici via JavaScript -->
                </div>
            </div>
            <!-- Vaccinations -->
            <div class="bg-emerald-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-emerald-800 mb-4 flex items-center">
                    <i class="fas fa-syringe mr-3"></i>Vaccinations
                </h3>
                <div id="vaccinationsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les vaccinations seront injectées ici via JavaScript -->
                </div>
            </div>
            <!-- Fichiers Médicaux -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-medical-alt mr-3"></i>Fichiers Médicaux
                </h3>
                <div id="fichiersMedicauxList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les fichiers médicaux seront injectés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Stocker les données des patients dans une variable globale pour un accès facile et sécurisé
        // Cela évite d'injecter de gros objets JSON directement dans les attributs onclick et résout l'erreur 'eval'.
        window.allPatients = @json($patients->keyBy('id')->toArray());

        // Clé pour le localStorage - système des nouveaux patients
        const STORAGE_KEY = 'patients_vus';

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
                        
            // Retirer la classe "nouveau-patient" et masquer le badge
            const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
            const badge = document.getElementById(`badge-${patientId}`);
                        
            if (row) {
                row.classList.remove('nouveau-patient');
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
                if (!patientsVus.includes(patientId)) {
                    // Marquer comme nouveau patient
                    row.classList.add('nouveau-patient');
                    const badge = document.getElementById(`badge-${patientId}`);
                    if (badge) {
                        badge.style.display = 'inline';
                    }
                }
            });
        }

        // Fonction pour prévisualiser l'image
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
                        
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu" class="w-full h-full object-cover">`;
                }
                                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<i class="fas fa-user text-gray-400 text-2xl"></i>';
            }
        }

        // FONCTION CORRIGÉE POUR VOIR LES DÉTAILS DU PATIENT
        function viewPatientDetails(patientId) {
            // Marquer le patient comme vu quand on voit ses détails
            marquerPatientCommeVu(patientId);
                        
            // Correction: utiliser l'URL correcte avec le préfixe 'secretaire'
            fetch(`/secretaire/patients/${patientId}/details`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.patient) {
                    displayPatientDetails(data);
                    document.getElementById('viewDetailsModal').classList.remove('hidden');
                } else if (data.error) {
                    showErrorMessage('Erreur: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des détails:', error);
                showErrorMessage('Erreur lors de la récupération des détails du patient: ' + error.message);
            });
        }

        function displayPatientDetails(data) {
            const patient = data.patient;
            const consultations = data.consultations || [];
            const ordonnances = data.ordonnances || [];
            const certificats = data.certificats || [];
            const remarques = data.remarques || [];
            const habitudesVie = data.habitudesVie || [];
            const examensBiologiques = data.examensBiologiques || [];
            const imagerieMedicale = data.imagerieMedicale || [];
            const vaccinations = data.vaccinations || [];
            const fichiersMedicaux = data.fichiersMedicaux || [];

            // Afficher les informations globales
            const globalInfoHtml = `
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h4 class="font-semibold text-gray-800 mb-2">Informations personnelles</h4>
                    <p><strong>CIN:</strong> ${patient.cin}</p>
                    <p><strong>Nom:</strong> ${patient.nom}</p>
                    <p><strong>Sexe:</strong> ${patient.sexe.charAt(0).toUpperCase() + patient.sexe.slice(1)}</p>
                    <p><strong>Date de naissance:</strong> ${new Date(patient.date_naissance).toLocaleDateString('fr-FR')}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h4 class="font-semibold text-gray-800 mb-2">Contact</h4>
                    <p><strong>Téléphone:</strong> ${patient.contact || 'Non renseigné'}</p>
                    <p><strong>Email:</strong> ${patient.email || 'Non renseigné'}</p>
                    <p><strong>Adresse:</strong> ${patient.adresse || 'Non renseignée'}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h4 class="font-semibold text-gray-800 mb-2">Informations médicales</h4>
                    <p><strong>Groupe sanguin:</strong> ${patient.groupe_sanguin || 'Non renseigné'}</p>
                    <p><strong>Poids:</strong> ${patient.poids ? patient.poids + ' kg' : 'Non renseigné'}</p>
                    <p><strong>Taille:</strong> ${patient.taille ? patient.taille + ' cm' : 'Non renseigné'}</p>
                    <p><strong>Allergies:</strong> ${patient.allergies || 'Aucune'}</p>
                </div>
            `;
            document.getElementById('patientGlobalInfo').innerHTML = globalInfoHtml;

            // Afficher les consultations
            let consultationsHtml = '';
            if (consultations.length > 0) {
                consultations.forEach(consultation => {
                    const dateConsultation = consultation.date_consultation;
                    const medecinNom = consultation.medecin ? consultation.medecin.nom : 'Non renseigné';
                    const motif = consultation.motif || 'Non renseigné';
                    const diagnostic = consultation.diagnostic || 'Pas encore de diagnostic';
                    const traitement = consultation.traitement || 'Pas encore de traitement';
                    const status = consultation.status || 'En cours';
                    const symptomes = consultation.symptomes || 'Non renseignés';
                    const rendezvousInfo = consultation.rendezvous ?
                         `<p><strong>Rendez-vous initial:</strong> ${new Date(consultation.rendezvous.appointment_date).toLocaleDateString('fr-FR')} à ${consultation.rendezvous.appointment_time}</p>` :
                         '';
                                        
                    consultationsHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Consultation du ${new Date(dateConsultation).toLocaleDateString('fr-FR')}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Motif:</strong> ${motif}</p>
                            <p><strong>Symptômes:</strong> ${symptomes}</p>
                            <p><strong>Diagnostic:</strong> ${diagnostic}</p>
                            <p><strong>Traitement:</strong> ${traitement}</p>
                            <p><strong>Statut:</strong> <span class="px-2 py-1 rounded text-xs ${status === 'completed' ? 'bg-green-100 text-green-800' : status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}">${status}</span></p>
                            ${rendezvousInfo}
                        </div>
                    `;
                });
            } else {
                consultationsHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-stethoscope text-4xl mb-2"></i><p>Aucune consultation trouvée</p></div>';
            }
            document.getElementById('consultationsList').innerHTML = consultationsHtml;

            // Afficher les ordonnances
            let ordonnancesHtml = '';
            if (ordonnances.length > 0) {
                ordonnances.forEach(ordonnance => {
                    const date = ordonnance.date_ordonance || ordonnance.created_at;
                    const medecinNom = ordonnance.medecin ? ordonnance.medecin.nom : 'Non renseigné';
                                        
                    ordonnancesHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-purple-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Ordonnance du ${new Date(date).toLocaleDateString('fr-FR')}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Médicaments:</strong> ${ordonnance.medicaments || 'Non renseigné'}</p>
                            <p><strong>Durée:</strong> ${ordonnance.duree_traitement || 'Non renseignée'}</p>
                            <p><strong>Instructions:</strong> ${ordonnance.instructions || 'Aucune'}</p>
                        </div>
                    `;
                });
            } else {
                ordonnancesHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-prescription-bottle text-4xl mb-2"></i><p>Aucune ordonnance trouvée</p></div>';
            }
            document.getElementById('ordonnancesList').innerHTML = ordonnancesHtml;

            // Afficher les certificats
            let certificatsHtml = '';
            if (certificats.length > 0) {
                certificats.forEach(certificat => {
                    const date = certificat.date_certificat || certificat.created_at;
                    const medecinNom = certificat.medecin ? certificat.medecin.nom : 'Non renseigné';
                                        
                    certificatsHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-orange-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Certificat du ${new Date(date).toLocaleDateString('fr-FR')}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Type:</strong> ${certificat.type || 'Non renseigné'}</p>
                            <p><strong>Contenu:</strong> ${certificat.contenu || 'Non renseigné'}</p>
                        </div>
                    `;
                });
            } else {
                certificatsHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-certificate text-4xl mb-2"></i><p>Aucun certificat trouvé</p></div>';
            }
            document.getElementById('certificatsList').innerHTML = certificatsHtml;

            // Afficher les remarques
            let remarquesHtml = '';
            if (remarques.length > 0) {
                remarques.forEach(remarque => {
                    const date = remarque.date_remarque || remarque.created_at;
                    const medecinNom = remarque.medecin ? remarque.medecin.nom : 'Non renseigné';
                                        
                    remarquesHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Remarque du ${new Date(date).toLocaleDateString('fr-FR')}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Remarque:</strong> ${remarque.remarque || 'Non renseigné'}</p>
                        </div>
                    `;
                });
            } else {
                remarquesHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-sticky-note text-4xl mb-2"></i><p>Aucune remarque trouvée</p></div>';
            }
            document.getElementById('remarquesList').innerHTML = remarquesHtml;

            // Afficher les habitudes de vie
            let habitudesVieHtml = '';
            if (habitudesVie.length > 0) {
                habitudesVie.forEach(habitude => {
                    const dateDebut = habitude.date_debut ? new Date(habitude.date_debut).toLocaleDateString('fr-FR') : 'Non renseignée';
                    const dateFin = habitude.date_fin ? new Date(habitude.date_fin).toLocaleDateString('fr-FR') : 'En cours';
                    const medecinNom = habitude.medecin ? habitude.medecin.nom : 'Non renseigné';
                                        
                    habitudesVieHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-indigo-500">
                            <h4 class="font-semibold text-gray-800 mb-2">${habitude.type || 'Habitude de vie'}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Description:</strong> ${habitude.description || 'Non renseignée'}</p>
                            <p><strong>Fréquence:</strong> ${habitude.frequence || 'Non renseignée'}</p>
                            <p><strong>Quantité:</strong> ${habitude.quantite || 'Non renseignée'}</p>
                            <p><strong>Période:</strong> ${dateDebut} - ${dateFin}</p>
                            ${habitude.commentaire ? `<p><strong>Commentaire:</strong> ${habitude.commentaire}</p>` : ''}
                        </div>
                    `;
                });
            } else {
                habitudesVieHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-heart text-4xl mb-2"></i><p>Aucune habitude de vie trouvée</p></div>';
            }
            document.getElementById('habitudesVieList').innerHTML = habitudesVieHtml;

            // Afficher les examens biologiques
            let examensBiologiquesHtml = '';
            if (examensBiologiques.length > 0) {
                examensBiologiques.forEach(examen => {
                    const dateExamen = examen.date_examen ? new Date(examen.date_examen).toLocaleDateString('fr-FR') : 'Non renseignée';
                    const medecinNom = examen.medecin ? examen.medecin.nom : 'Non renseigné';
                                        
                    examensBiologiquesHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Examen du ${dateExamen}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Type:</strong> ${examen.type || 'Non renseigné'}</p>
                            <p><strong>Résultat:</strong> ${examen.resultat || 'Non renseigné'}</p>
                            <p><strong>Unité:</strong> ${examen.unite || 'Non renseignée'}</p>
                            <p><strong>Valeurs de référence:</strong> ${examen.valeurs_reference || 'Non renseignées'}</p>
                            ${examen.commentaire ? `<p><strong>Commentaire:</strong> ${examen.commentaire}</p>` : ''}
                        </div>
                    `;
                });
            } else {
                examensBiologiquesHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-vial text-4xl mb-2"></i><p>Aucun examen biologique trouvé</p></div>';
            }
            document.getElementById('examensBiologiquesList').innerHTML = examensBiologiquesHtml;

            // Afficher l'imagerie médicale
            let imagerieMedicaleHtml = '';
            if (imagerieMedicale.length > 0) {
                imagerieMedicale.forEach(imagerie => {
                    const dateExamen = imagerie.date_examen ? new Date(imagerie.date_examen).toLocaleDateString('fr-FR') : 'Non renseignée';
                    const medecinNom = imagerie.medecin ? imagerie.medecin.nom : 'Non renseigné';
                                        
                    imagerieMedicaleHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-teal-500">
                            <h4 class="font-semibold text-gray-800 mb-2">Imagerie du ${dateExamen}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Type:</strong> ${imagerie.type || 'Non renseigné'}</p>
                            <p><strong>Zone examinée:</strong> ${imagerie.zone_examinee || 'Non renseignée'}</p>
                            <p><strong>Résultat:</strong> ${imagerie.resultat || 'Non renseigné'}</p>
                            ${imagerie.commentaire ? `<p><strong>Commentaire:</strong> ${imagerie.commentaire}</p>` : ''}
                        </div>
                    `;
                });
            } else {
                imagerieMedicaleHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-x-ray text-4xl mb-2"></i><p>Aucune imagerie médicale trouvée</p></div>';
            }
            document.getElementById('imagerieMedicaleList').innerHTML = imagerieMedicaleHtml;

            // Afficher les vaccinations
            let vaccinationsHtml = '';
            if (vaccinations.length > 0) {
                vaccinations.forEach(vaccination => {
                    const dateVaccination = vaccination.date_vaccination ? new Date(vaccination.date_vaccination).toLocaleDateString('fr-FR') : 'Non renseignée';
                    const dateRappel = vaccination.date_rappel ? new Date(vaccination.date_rappel).toLocaleDateString('fr-FR') : 'Non prévue';
                    const medecinNom = vaccination.medecin ? vaccination.medecin.nom : 'Non renseigné';
                                        
                    vaccinationsHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-emerald-500">
                            <h4 class="font-semibold text-gray-800 mb-2">${vaccination.nom || 'Vaccination'}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Date de vaccination:</strong> ${dateVaccination}</p>
                            <p><strong>Date de rappel:</strong> ${dateRappel}</p>
                            ${vaccination.commentaire ? `<p><strong>Commentaire:</strong> ${vaccination.commentaire}</p>` : ''}
                        </div>
                    `;
                });
            } else {
                vaccinationsHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-syringe text-4xl mb-2"></i><p>Aucune vaccination trouvée</p></div>';
            }
            document.getElementById('vaccinationsList').innerHTML = vaccinationsHtml;

            // Afficher les fichiers médicaux
            let fichiersMedicauxHtml = '';
            if (fichiersMedicaux.length > 0) {
                fichiersMedicaux.forEach(fichier => {
                    const medecinNom = fichier.medecin ? fichier.medecin.nom : 'Non renseigné';
                    const tailleFichier = fichier.taille ? (fichier.taille / 1024).toFixed(2) + ' KB' : 'Inconnue';
                                        
                    fichiersMedicauxHtml += `
                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-gray-500">
                            <h4 class="font-semibold text-gray-800 mb-2">${fichier.nom || 'Fichier médical'}</h4>
                            <p><strong>Médecin:</strong> ${medecinNom}</p>
                            <p><strong>Type:</strong> ${fichier.type || 'Non renseigné'}</p>
                            <p><strong>Taille:</strong> ${tailleFichier}</p>
                            ${fichier.commentaire ? `<p><strong>Commentaire:</strong> ${fichier.commentaire}</p>` : ''}
                            <p><strong>Ajouté le:</strong> ${new Date(fichier.created_at).toLocaleDateString('fr-FR')}</p>
                        </div>
                    `;
                });
            } else {
                fichiersMedicauxHtml = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-file-medical-alt text-4xl mb-2"></i><p>Aucun fichier médical trouvé</p></div>';
            }
            document.getElementById('fichiersMedicauxList').innerHTML = fichiersMedicauxHtml;
        }

        function closeViewDetailsModal() {
            document.getElementById('viewDetailsModal').classList.add('hidden');
        }

        // Fonction pour masquer automatiquement les messages après 5 secondes
        function autoHideMessages() {
            const messages = [
                document.getElementById('successMessage'),
                document.getElementById('errorMessage'),
                document.getElementById('validationErrors')
            ];

            messages.forEach(message => {
                if (message) {
                    // S'assurer que le message est visible
                    message.style.display = 'block';
                    message.style.opacity = '1';

                    // Ajouter un bouton de fermeture
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '<i class="fas fa-times"></i>';
                    closeButton.className =
                        'absolute top-2 right-2 text-current opacity-70 hover:opacity-100 transition-opacity p-1 rounded';
                    closeButton.onclick = () => hideMessage(message);
                    message.style.position = 'relative'; // Pour positionner le bouton de fermeture
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
                messageElement.style.transition = 'opacity 0.5s ease-out';
                messageElement.style.opacity = '0';
                setTimeout(() => {
                    messageElement.style.display = 'none';
                }, 500);
            }
        }

        // Fonction pour afficher un message de succès
        function showSuccessMessage(message) {
            // Créer ou mettre à jour le message de succès
            let successDiv = document.getElementById('successMessage');
            if (!successDiv) {
                successDiv = document.createElement('div');
                successDiv.id = 'successMessage';
                successDiv.className =
                    'mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-opacity duration-500 shadow-lg';
                // Insérer le message au début du main
                const mainElement = document.querySelector('main');
                mainElement.insertBefore(successDiv, mainElement.firstChild);
            }

            successDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600 text-xl"></i>
                <div>
                    <div class="mt-1">${message}</div>
                </div>
            </div>
        `;
            successDiv.style.display = 'block';
            successDiv.style.opacity = '1';

            // Ajouter bouton de fermeture et masquer après 5 secondes
            const closeButton = document.createElement('button');
            closeButton.innerHTML = '<i class="fas fa-times"></i>';
            closeButton.className =
                'absolute top-2 right-2 text-current opacity-70 hover:opacity-100 transition-opacity p-1 rounded';
            closeButton.onclick = () => hideMessage(successDiv);
            successDiv.style.position = 'relative';
            successDiv.appendChild(closeButton);

            setTimeout(() => {
                hideMessage(successDiv);
            }, 5000);
        }

        // Fonction pour afficher un message d'erreur
        function showErrorMessage(message) {
            // Créer ou mettre à jour le message d'erreur
            let errorDiv = document.getElementById('errorMessage');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'errorMessage';
                errorDiv.className =
                    'mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500 shadow-lg';
                // Insérer le message au début du main
                const mainElement = document.querySelector('main');
                mainElement.insertBefore(errorDiv, mainElement.firstChild);
            }

            errorDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-600 text-xl"></i>
                <div>
                    <div class="mt-1">${message}</div>
                </div>
            </div>
        `;
            errorDiv.style.display = 'block';
            errorDiv.style.opacity = '1';

            // Ajouter bouton de fermeture et masquer après 5 secondes
            const closeButton = document.createElement('button');
            closeButton.innerHTML = '<i class="fas fa-times"></i>';
            closeButton.className =
                'absolute top-2 right-2 text-current opacity-70 hover:opacity-100 transition-opacity p-1 rounded';
            closeButton.onclick = () => hideMessage(errorDiv);
            errorDiv.style.position = 'relative';
            errorDiv.appendChild(closeButton);

            setTimeout(() => {
                hideMessage(errorDiv);
            }, 5000);
        }

        // Initialiser au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Masquer automatiquement les messages
            autoHideMessages();

            // Marquer les nouveaux patients
            marquerNouveauxPatients();

            // Ouvrir automatiquement le modal si il y a des erreurs de validation
            @if ($errors->any())
                openAddModal();
            @endif

            // Ajouter les événements de filtrage
            document.getElementById('searchInput').addEventListener('input', filterPatients);
            document.getElementById('sexeFilter').addEventListener('change', filterPatients);

            // Vérifier s'il y a des messages flash et les afficher
            @if (session('success'))
                showSuccessMessage("{{ session('success') }}");
            @endif

            @if (session('error'))
                showErrorMessage("{{ session('error') }}");
            @endif
        });

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.querySelector('#addModal form').reset(); // Réinitialise le formulaire à l'ouverture
            // Réinitialiser l'aperçu de l'image
            document.getElementById('profilePreview').innerHTML = '<i class="fas fa-user text-gray-400 text-2xl"></i>';
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Modifié pour prendre l'ID du patient et le récupérer depuis window.allPatients
        function openEditModal(patientId) {
            const patient = window.allPatients[patientId];
            if (!patient) {
                console.error('Patient non trouvé pour l\'ID:', patientId);
                return;
            }

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editForm').action = '/secretaire/patients/' + patient.id;

            // Remplir tous les champs
            document.getElementById('edit_id').value = patient.id;
            document.getElementById('edit_cin').value = patient.cin;
            document.getElementById('edit_nom').value = patient.nom;
            document.getElementById('edit_sexe').value = patient.sexe;
            document.getElementById('edit_date_naissance').value = patient.date_naissance;
            document.getElementById('edit_contact').value = patient.contact || '';
            document.getElementById('edit_adresse').value = patient.adresse || '';
            document.getElementById('edit_email').value = patient.email || '';
            document.getElementById('edit_telephone_secondaire').value = patient.telephone_secondaire || '';
            document.getElementById('edit_groupe_sanguin').value = patient.groupe_sanguin || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            document.getElementById('edit_antecedents').value = patient.antecedents || '';
            document.getElementById('edit_medicaments').value = patient.medicaments || '';
            document.getElementById('edit_poids').value = patient.poids || '';
            document.getElementById('edit_taille').value = patient.taille || '';
            document.getElementById('edit_profession').value = patient.profession || '';
            document.getElementById('edit_situation_familiale').value = patient.situation_familiale || '';
            document.getElementById('edit_emergency_contact_name').value = patient.emergency_contact_name || '';
            document.getElementById('edit_emergency_contact_phone').value = patient.emergency_contact_phone || '';
            document.getElementById('edit_is_active').value = patient.is_active ? '1' : '0';

            // Afficher l'image de profil actuelle
            const editPreview = document.getElementById('editProfilePreview');
            if (patient.profile_image) {
                editPreview.innerHTML = `<img src="/storage/${patient.profile_image}" alt="Photo actuelle" class="w-full h-full object-cover">`;
            } else {
                editPreview.innerHTML = '<i class="fas fa-user text-gray-400 text-2xl"></i>';
            }
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deletePatient(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
                fetch('/secretaire/patients/' + id, {
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
                            window.location.reload();
                        } else if (data.error) {
                            alert('Erreur: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suppression.');
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

        document.getElementById('viewDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewDetailsModal();
            }
        });

        // Fermer les modales avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeViewDetailsModal();
            }
        });

        // Fonction de recherche et filtrage
        function filterPatients() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const sexeFilter = document.getElementById('sexeFilter').value;
            const rows = document.querySelectorAll('.patient-row');
            const noPatientRow = document.getElementById('noPatientRow');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const sexeData = row.getAttribute('data-sexe');

                const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                const matchesSexe = !sexeFilter || sexeData === sexeFilter;

                if (matchesSearch && matchesSexe) {
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
    </script>
</body>
</html>