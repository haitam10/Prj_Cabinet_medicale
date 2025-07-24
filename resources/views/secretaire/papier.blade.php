<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Secrétaire - Configuration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <span class="text-white text-xl font-bold">Espace Médecin</span>
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
                        class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
            <div class="bg-gray-800 rounded-lg p-4 group cursor-pointer hover:bg-red-600 transition-colors duration-200">
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
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-900">Configuration</h1>
                <p class="text-gray-600 text-sm mt-1">Configuration des Documents du cabinet</p>
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

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form action="{{ route('secretaire.papier.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <!-- Title -->
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Configuration du Cabinet</h2>
                        <div class="w-24 h-1 bg-cordes-blue mx-auto rounded"></div>
                    </div>

                    <!-- Cabinet Info Section -->
                    <div class="space-y-6">
                        <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">
                            <i class="fas fa-hospital mr-2 text-cordes-blue"></i>Informations Cabinet
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Cabinet</label>
                                <input type="text" name="nom_cabinet" value="{{ $cabinetInfo->nom_cabinet ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Nom du cabinet...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse du Cabinet</label>
                                <input type="text" name="addr_cabinet" value="{{ $cabinetInfo->addr_cabinet ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Adresse du cabinet...">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone du Cabinet</label>
                                <input type="tel" name="tel_cabinet" value="{{ $cabinetInfo->tel_cabinet ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="+212 6XX XXX XXX">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description du Cabinet</label>
                                <textarea name="desc_cabinet" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none" placeholder="Une brève description du cabinet...">{{ $cabinetInfo->descr_cabinet ?? '' }}</textarea>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Certificats Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">
                                <i class="fas fa-file-medical mr-2 text-green-600"></i>Certificats
                            </h3>
                            <button type="button" onclick="openCreateModal('certificat')" class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                <i class="fas fa-plus mr-2"></i>Ajouter Certificat
                            </button>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $certifIndex = 1; @endphp
                                        
                                        @forelse($certifModels as $certif)
                                        <tr class="{{ $certif->is_default ? 'bg-blue-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $certifIndex++ }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $certif->model_nom }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button type="button" onclick="openViewModal('certificat', {{ $certif->id }})" 
                                                        class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-eye"></i> 
                                                </button>
                                                <button type="button" onclick="openEditModal('certificat', {{ $certif->id }})" 
                                                        class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-edit"></i> 
                                                </button>
                                                <button type="button" onclick="deleteTemplate('certificat', {{ $certif->id }})" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i> 
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                                Aucun certificat trouvé
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Ordonnances Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">
                                <i class="fas fa-prescription-bottle-medical mr-2 text-blue-600"></i>Ordonnances
                            </h3>
                            <button type="button" onclick="openCreateModal('ordonnance')" class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                <i class="fas fa-plus mr-2"></i>Ajouter Ordonnance
                            </button>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $ordonnIndex = 1; @endphp
                                        
                                        @forelse($ordonnModels as $ordonn)
                                        <tr class="{{ $ordonn->is_default ? 'bg-blue-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordonnIndex++ }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $ordonn->model_nom }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button type="button" onclick="openViewModal('ordonnance', {{ $ordonn->id }})" 
                                                        class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-eye"></i> 
                                                </button>
                                                <button type="button" onclick="openEditModal('ordonnance', {{ $ordonn->id }})" 
                                                        class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-edit"></i> 
                                                </button>
                                                <button type="button" onclick="deleteTemplate('ordonnance', {{ $ordonn->id }})" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i> 
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                                Aucune ordonnance trouvée
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Secrétaires Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">
                                <i class="fas fa-user-friends mr-2 text-purple-600"></i>Secrétaires
                            </h3>
                            <button type="button" onclick="openAddSecretaireModal()" class="px-4 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                                <i class="fas fa-plus mr-2"></i>Ajouter Secrétaire
                            </button>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="secretairesTableBody">
                                        @php $secretaireIndex = 1; @endphp
                                        
                                        @forelse($secretaires as $secretaire)
                                        <tr id="secretaire-row-{{ $secretaire->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $secretaireIndex++ }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $secretaire->nom }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $secretaire->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $secretaire->telephone ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button type="button" onclick="dissociateSecretaire({{ $secretaire->id }})" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-unlink"></i> Dissocier
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="no-secretaires-row">
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                Aucune secrétaire associée
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons for Cabinet Info -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="resetForm()" class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="submit" class="px-6 py-2 bg-cordes-blue text-white rounded-lg hover:bg-cordes-dark transition-colors">
                            <i class="fas fa-save mr-2"></i>Enregistrer les informations du cabinet
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Create Template Modal -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="createModalTitle" class="text-xl font-semibold">Ajouter Nouveau Template</h3>
                        <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="createTemplateForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="createTemplateType" name="template_type">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Template</label>
                                <input type="text" name="name" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                <input type="file" name="logo_path" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description d'en-tête</label>
                                <textarea name="header_description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description du Corps</label>
                                <textarea name="body_description" rows="5"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description de Pied</label>
                                <textarea name="footer_description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-cordes-blue text-white rounded hover:bg-cordes-dark">
                                <i class="fas fa-plus mr-2"></i>Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="editModalTitle" class="text-xl font-semibold">Modifier Template</h3>
                        <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="editTemplateForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="editTemplateId" name="id">
                        <input type="hidden" id="editTemplateType" name="template_type">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Template</label>
                                <input type="text" id="editName" name="name" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                <input type="file" id="editLogoInput" name="logo_path" accept="image/*" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none">
                                <div id="currentLogo" class="mt-2"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description d'en-tête</label>
                                <textarea id="editHeaderDesc" name="header_description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description du Corps</label>
                                <textarea id="editBodyDesc" name="body_description" rows="5"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description de Pied</label>
                                <textarea id="editFooterDesc" name="footer_description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cordes-accent focus:border-transparent outline-none"></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Template Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="viewModalTitle" class="text-xl font-semibold">Détails du Template</h3>
                        <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Template</label>
                            <p id="viewName" class="p-2 border border-gray-200 rounded-lg bg-gray-50"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <div id="viewLogo" class="p-2 border border-gray-200 rounded-lg bg-gray-50"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description d'en-tête</label>
                            <p id="viewHeaderDesc" class="p-2 border border-gray-200 rounded-lg bg-gray-50 whitespace-pre-wrap"></p>
                        </div>
                        <div id="viewPatientSection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2"></label>
                            <p id="viewPatientName" class="p-2 border border-gray-200 rounded-lg bg-gray-50 whitespace-pre-wrap"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description du Corps</label>
                            <p id="viewBodyDesc" class="p-2 border border-gray-200 rounded-lg bg-gray-50 whitespace-pre-wrap"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description de Pied</label>
                            <p id="viewFooterDesc" class="p-2 border border-gray-200 rounded-lg bg-gray-50 whitespace-pre-wrap"></p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Secretaire Modal -->
    <div id="addSecretaireModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-lg w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Associer une Secrétaire</h3>
                        <button onclick="closeAddSecretaireModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secrétaires disponibles</label>
                            <div id="availableSecretairesList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg">
                                <!-- Secrétaires disponibles seront chargées ici -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6 space-x-2">
                        <button type="button" onclick="closeAddSecretaireModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto-hide success/error messages
        function autoHideMessages() {
            const messages = [
                document.getElementById('successMessage'),
                document.getElementById('errorMessage')
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

        function resetForm() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser les informations du cabinet ?')) {
                document.querySelector('form[action="{{ route('secretaire.papier.store') }}"]').reset();
            }
        }

        // --- Secrétaire Management Functions ---
        function openAddSecretaireModal() {
            const modal = document.getElementById('addSecretaireModal');
            const secretairesList = document.getElementById('availableSecretairesList');
            
            // Clear previous content
            secretairesList.innerHTML = '<div class="p-4 text-center text-gray-500">Chargement...</div>';
            
            // Load available secretaires
            $.ajax({
                url: "{{ route('secretaire.papier.getAvailableSecretaires') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success && response.secretaires.length > 0) {
                        let html = '';
                        response.secretaires.forEach(secretaire => {
                            html += `
                                <div class="p-3 border-b border-gray-200 hover:bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">${secretaire.nom}</div>
                                        <div class="text-sm text-gray-500">${secretaire.email}</div>
                                        <div class="text-sm text-gray-500">${secretaire.telephone || 'N/A'}</div>
                                    </div>
                                    <button onclick="associateSecretaire(${secretaire.id})" 
                                            class="px-3 py-1 bg-cordes-blue text-white rounded hover:bg-cordes-dark text-sm">
                                        <i class="fas fa-plus mr-1"></i>Associer
                                    </button>
                                </div>
                            `;
                        });
                        secretairesList.innerHTML = html;
                    } else {
                        secretairesList.innerHTML = '<div class="p-4 text-center text-gray-500">Aucune secrétaire disponible</div>';
                    }
                },
                error: function() {
                    secretairesList.innerHTML = '<div class="p-4 text-center text-red-500">Erreur lors du chargement</div>';
                }
            });
            
            modal.classList.remove('hidden');
        }

        function closeAddSecretaireModal() {
            document.getElementById('addSecretaireModal').classList.add('hidden');
        }

        function associateSecretaire(secretaireId) {
            $.ajax({
                url: "{{ route('secretaire.papier.associateSecretaire') }}",
                method: 'POST',
                data: {
                    secretaire_id: secretaireId
                },
                success: function(response) {
                    if (response.success) {
                        closeAddSecretaireModal();
                        showMessage('Secrétaire associée avec succès', 'success');
                        
                        // Add the new secretaire to the table
                        addSecretaireToTable(response.secretaire);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Erreur lors de l\'association';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showMessage(errorMessage, 'error');
                }
            });
        }

        function dissociateSecretaire(secretaireId) {
            if (confirm('Êtes-vous sûr de vouloir dissocier cette secrétaire ?')) {
                $.ajax({
                    url: `/secretaire/papier/dissociate-secretaire/${secretaireId}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showMessage('Secrétaire dissociée avec succès', 'success');
                            
                            // Remove the secretaire from the table
                            removeSecretaireFromTable(secretaireId);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Erreur lors de la dissociation';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showMessage(errorMessage, 'error');
                    }
                });
            }
        }

        function addSecretaireToTable(secretaire) {
            const tableBody = document.getElementById('secretairesTableBody');
            const noSecretairesRow = document.getElementById('no-secretaires-row');
            
            // Remove "no secretaires" row if it exists
            if (noSecretairesRow) {
                noSecretairesRow.remove();
            }
            
            // Calculate next index
            const existingRows = tableBody.querySelectorAll('tr:not(#no-secretaires-row)');
            const nextIndex = existingRows.length + 1;
            
            // Create new row
            const newRow = document.createElement('tr');
            newRow.id = `secretaire-row-${secretaire.id}`;
            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${nextIndex}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${secretaire.nom}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${secretaire.email}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${secretaire.telephone || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button type="button" onclick="dissociateSecretaire(${secretaire.id})" 
                            class="text-red-600 hover:text-red-900">
                        <i class="fas fa-unlink"></i> Dissocier
                    </button>
                </td>
            `;
            
            tableBody.appendChild(newRow);
        }

        function removeSecretaireFromTable(secretaireId) {
            const row = document.getElementById(`secretaire-row-${secretaireId}`);
            if (row) {
                row.remove();
                
                // Check if table is empty and add "no secretaires" row
                const tableBody = document.getElementById('secretairesTableBody');
                const remainingRows = tableBody.querySelectorAll('tr');
                
                if (remainingRows.length === 0) {
                    const noSecretairesRow = document.createElement('tr');
                    noSecretairesRow.id = 'no-secretaires-row';
                    noSecretairesRow.innerHTML = `
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Aucune secrétaire associée
                        </td>
                    `;
                    tableBody.appendChild(noSecretairesRow);
                } else {
                    // Update row numbers
                    remainingRows.forEach((row, index) => {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            firstCell.textContent = index + 1;
                        }
                    });
                }
            }
        }

        // --- Delete Template Function ---
        function deleteTemplate(type, id) {
            if (confirm(`Êtes-vous sûr de vouloir surprimer ce template de ${type} ?`)) {
                $.ajax({
                    url: `{{ url('/secretaire/papier/delete-template') }}/${type}/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                             showMessage(response.message || 'Erreur lors de la suppression', 'error');
                        }
                    },
                    error: function(xhr) {
                         let errorMessage = 'Erreur lors de la suppression';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showMessage(errorMessage, 'error');
                    }
                });
            }
        }

        // --- Create Template Modal Functions ---
        function openCreateModal(type) {
            const modal = document.getElementById('createModal');
            const title = document.getElementById('createModalTitle');
            const templateType = document.getElementById('createTemplateType');
            const form = document.getElementById('createTemplateForm');
            
            form.reset();
            title.textContent = `Ajouter Nouveau Template ${type === 'certificat' ? 'Certificat' : 'Ordonnance'}`;
            templateType.value = type;
            modal.classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        document.getElementById('createTemplateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('secretaire.papier.createTemplate') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        closeCreateModal();
                        showMessage('Template ajouté avec succès', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Erreur lors de l\'ajout du template';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).map(err => err.join(', ')).join('<br>');
                    }
                    showMessage(errorMessage, 'error');
                }
            });
        });

        // --- Edit Template Modal Functions ---
        function openEditModal(type, id) {
            const modal = document.getElementById('editModal');
            const title = document.getElementById('editModalTitle');
            const templateId = document.getElementById('editTemplateId');
            const templateType = document.getElementById('editTemplateType');
            const currentLogoDiv = document.getElementById('currentLogo');
            
            title.textContent = `Modifier Template ${type === 'certificat' ? 'Certificat' : 'Ordonnance'}`;
            templateId.value = id;
            templateType.value = type;
            
            currentLogoDiv.innerHTML = '';

            $.ajax({
                url: `{{ url('/secretaire/papier/get-template') }}/${type}/${id}`,
                method: 'GET',
                success: function(template) {
                    document.getElementById('editName').value = template.model_nom || ''; 
                    document.getElementById('editHeaderDesc').value = template.descr_head || '';
                    document.getElementById('editBodyDesc').value = template.descr_body || '';
                    document.getElementById('editFooterDesc').value = template.descr_footer || '';
                    
                    if (template.logo_file_path) { 
                        currentLogoDiv.innerHTML = `<p class="text-sm text-gray-500">Logo actuel:</p><img src="{{ asset('storage') }}/${template.logo_file_path}" alt="Logo actuel" class="h-16 w-16 object-contain border rounded">`;
                    } else {
                        currentLogoDiv.innerHTML = '<p class="text-sm text-gray-500">Aucun logo actuel</p>';
                    }
                    
                    modal.classList.remove('hidden');
                },
                error: function(xhr) {
                    let errorMessage = 'Erreur lors du chargement du template';
                    if (xhr.status === 404) {
                        errorMessage = 'Template non trouvé ou non autorisé.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showMessage(errorMessage, 'error');
                }
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('editTemplateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('secretaire.papier.updateTemplate') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        closeEditModal();
                        showMessage('Template mis à jour avec succès', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Erreur lors de la mise à jour du template';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).map(err => err.join(', ')).join('<br>');
                    }
                    showMessage(errorMessage, 'error');
                }
            });
        });

        // --- View Template Modal Functions ---
        function openViewModal(type, id) {
            const modal = document.getElementById('viewModal');
            const title = document.getElementById('viewModalTitle');
            const viewLogoDiv = document.getElementById('viewLogo');
            const viewPatientSection = document.getElementById('viewPatientSection');

            title.textContent = `Détails du Template ${type === 'certificat' ? 'Certificat' : 'Ordonnance'}`;
            viewLogoDiv.innerHTML = '';

            if (type === 'certificat') {
                viewPatientSection.classList.remove('hidden');
            } else {
                viewPatientSection.classList.add('hidden');
            }

            $.ajax({
                url: `{{ url('/secretaire/papier/get-template') }}/${type}/${id}`,
                method: 'GET',
                success: function(template) {
                    document.getElementById('viewName').textContent = template.model_nom || 'N/A';
                    document.getElementById('viewHeaderDesc').textContent = template.descr_head || 'N/A';
                    document.getElementById('viewBodyDesc').textContent = template.descr_body || 'N/A';
                    document.getElementById('viewFooterDesc').textContent = template.descr_footer || 'N/A';

                    document.getElementById('viewPatientName').textContent = 'Mme/Mr Nom Patient';

                    if (template.logo_file_path) {
                        viewLogoDiv.innerHTML = `<img src="{{ asset('storage') }}/${template.logo_file_path}" alt="Logo actuel" class="h-16 w-16 object-contain border rounded">`;
                    } else {
                        viewLogoDiv.innerHTML = '<p class="text-sm text-gray-500">Aucun logo</p>';
                    }

                    modal.classList.remove('hidden');
                },
                error: function() {
                    showMessage('Erreur lors du chargement des détails du template', 'error');
                }
            });
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }

        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `mb-4 p-4 ${alertClass} rounded-lg border transition-opacity duration-500`;
            messageDiv.innerHTML = `<i class=\"fas ${icon} mr-2\"></i>${message}` + 
                                   `<button type="button" class="float-right text-current opacity-70 hover:opacity-100 transition-opacity ml-2" onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(),500);">` +
                                   `<i class="fas fa-times"></i></button>`;
            
            const main = document.querySelector('main');
            main.insertBefore(messageDiv, main.firstChild);
            
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                setTimeout(() => {
                    messageDiv.remove();
                }, 500);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            autoHideMessages();
        });
    </script>
</body>
</html>