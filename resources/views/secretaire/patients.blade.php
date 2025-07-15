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
                                CIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom complet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sexe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Âge</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Groupe Sanguin</th>
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
                                data-sexe="{{ $patient->sexe }}">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $patient->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick='openEditModal({{ $patient->id }})'
                                            class="text-blue-600 hover:text-blue-800 transition-colors p-1 rounded hover:bg-blue-50">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePatient({{ $patient->id }})"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noPatientRow">
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
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

            <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
                @csrf

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
            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

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

    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Stocker les données des patients dans une variable globale pour un accès facile et sécurisé
        // Cela évite d'injecter de gros objets JSON directement dans les attributs onclick et résout l'erreur 'eval'.
        window.allPatients = @json($patients->keyBy('id')->toArray());

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
            document.getElementById('editForm').action = '/patients/' + patient.id;

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
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deletePatient(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
                fetch('/patients/' + id, {
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

        // Fermer les modales avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
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
