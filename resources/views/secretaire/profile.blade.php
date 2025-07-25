<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mon Profil - Cabinet Médical</title>
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
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .form-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        .btn-locate {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .btn-locate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        .btn-locate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .readonly-input {
            background-color: #f8fafc;
            color: #6b7280;
            cursor: not-allowed;
        }
        .info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border-left: 4px solid #3b82f6;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-actif {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-inactif {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .status-suspendu {
            background-color: #fef3c7;
            color: #d97706;
        }
        #map {
            height: 300px;
            margin-top: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }
    </style>
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
                        class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                            class="w-10 h-10 rounded-full object-cover">
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
    <div class="ml-64 min-h-screen">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Mon Profil</h1>
                    <p class="text-gray-600 text-sm mt-1">Gérez vos informations personnelles</p>
                </div>
            </div>
        </header>

        <main class="p-6 max-w-7xl mx-auto">
            @if (session('success'))
                <div id="successMessage"
                    class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 transition-opacity duration-500 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div id="errorMessage"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- En-tête du profil -->
            <div class="profile-header mb-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-white">{{ $user->nom }}</h2>
                        <p class="text-xl text-white/80 mb-2">{{ ucfirst($user->role) }}</p>
                        <span class="status-badge status-{{ $user->statut }}">
                            {{ ucfirst($user->statut) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="info-card bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-cordes-blue text-xl"></i>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900 break-words">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="info-card bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-phone text-cordes-blue text-xl"></i>
                        <div>
                            <p class="text-sm text-gray-600">Téléphone</p>
                            <p class="font-semibold text-gray-900 break-words">{{ $user->telephone ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
                <div class="info-card bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-id-card text-cordes-blue text-xl"></i>
                        <div>
                            <p class="text-sm text-gray-600">CIN</p>
                            <p class="font-semibold text-gray-900 break-words">{{ $user->cin }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <form method="POST" action="{{ route('secretaire.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Informations personnelles -->
                    <div class="form-section bg-white rounded-lg p-6 shadow">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-cordes-blue"></i>
                            Informations personnelles
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="form-label block mb-1 font-medium" for="cin">CIN *</label>
                                <input type="text" id="cin" name="cin" value="{{ old('cin', $user->cin) }}" 
                                       class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue" required>
                                @error('cin')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="nom">Nom complet *</label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" 
                                       class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue" required>
                                @error('nom')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="email">Email *</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="telephone">Téléphone</label>
                                <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" 
                                       class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                                @error('telephone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="role">Rôle</label>
                                <input type="text" id="role" name="role" value="{{ ucfirst($user->role) }}" 
                                       class="form-input w-full rounded border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                                <p class="text-sm text-gray-500 mt-1">Le rôle ne peut pas être modifié</p>
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="statut">Statut *</label>
                                <select id="statut" name="statut" class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue" required>
                                    <option value="actif" {{ old('statut', $user->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ old('statut', $user->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    <option value="suspendu" {{ old('statut', $user->statut) == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                                @error('statut')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informations complémentaires -->
                    <div class="form-section bg-white rounded-lg p-6 shadow">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-cordes-blue"></i>
                            Informations complémentaires
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="form-label block mb-1 font-medium" for="date_naissance">Date de naissance</label>
                                <input type="date" id="date_naissance" name="date_naissance" 
                                       value="{{ old('date_naissance', $user->date_naissance ? $user->date_naissance->format('Y-m-d') : '') }}" 
                                       class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                                @error('date_naissance')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label block mb-1 font-medium" for="sexe">Sexe</label>
                                <select id="sexe" name="sexe" class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                                    <option value="">Sélectionnez</option>
                                    <option value="homme" {{ old('sexe', $user->sexe) == 'homme' ? 'selected' : '' }}>Homme</option>
                                    <option value="femme" {{ old('sexe', $user->sexe) == 'femme' ? 'selected' : '' }}>Femme</option>
                                </select>
                                @error('sexe')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
 
                                
                                <label class="form-label block mb-1 font-medium" for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" 
                                    value="{{ old('adresse', $user->adresse) }}" 
                                    class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue"
                                    placeholder="Commencez à taper votre adresse ou cliquez sur 'Localiser ma position'">
                                @error('adresse')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $user->latitude ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $user->longitude ?? '') }}">

                                <div id="map"></div>

                                                               <!-- BOUTON POUR LOCALISER -->
                                <button type="button" id="btnLocateMe" class="btn-locate">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span id="locateText">Localiser ma position</span>
                                </button>
                            </div>

                            @if($user->role === 'medecin')
                                <div>
                                    <label class="form-label block mb-1 font-medium" for="specialite">Spécialité</label>
                                    <input type="text" id="specialite" name="specialite" value="{{ old('specialite', $user->specialite) }}" 
                                           class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                                    @error('specialite')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label block mb-1 font-medium" for="numero_adeli">Numéro ADELI</label>
                                    <input type="text" id="numero_adeli" name="numero_adeli" value="{{ old('numero_adeli', $user->numero_adeli) }}" 
                                           class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                                    @error('numero_adeli')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Changement de mot de passe -->
                <div class="form-section bg-white rounded-lg p-6 shadow mt-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-lock mr-2 text-cordes-blue"></i>
                        Changer le mot de passe
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label block mb-1 font-medium" for="password">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="form-label block mb-1 font-medium" for="password_confirmation">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input w-full rounded border-gray-300 focus:ring focus:ring-cordes-blue">
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Laissez vide si vous ne souhaitez pas changer le mot de passe
                    </p>
                </div>

                <!-- Bouton de sauvegarde -->
                <div class="flex justify-end mt-6">
                    <button type="submit" class="btn-primary inline-flex items-center px-4 py-2 bg-cordes-blue text-white rounded hover:bg-cordes-blue-dark transition">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Variables globales
        let map;
        let marker;
        let autocomplete;
        let geocoder;

        // Auto-dismiss messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const messages = document.querySelectorAll('#successMessage, #errorMessage');
                messages.forEach(function(message) {
                    if (message) {
                        message.style.opacity = '0';
                        setTimeout(function() {
                            message.style.display = 'none';
                        }, 500);
                    }
                });
            }, 5000);
        });

        // Initialisation de la carte Google Maps
        function initMap() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const adresseInput = document.getElementById('adresse');

            // Récupérer les coordonnées existantes ou utiliser les coordonnées par défaut (Casablanca)
            const lat = parseFloat(latInput.value) || 33.5731;
            const lng = parseFloat(lngInput.value) || -7.5898;
            const initialPosition = { lat, lng };

            // Initialiser la carte
            map = new google.maps.Map(document.getElementById('map'), {
                center: initialPosition,
                zoom: latInput.value && lngInput.value ? 15 : 13,
                streetViewControl: false,
                mapTypeControl: false,
                fullscreenControl: false,
            });

            // Initialiser le geocoder
            geocoder = new google.maps.Geocoder();

            // Créer le marqueur draggable
            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                draggable: true,
                title: 'Votre position'
            });

            // Initialiser l'autocomplétion Google Places
            autocomplete = new google.maps.places.Autocomplete(adresseInput, {
                types: ['geocode'],
                componentRestrictions: { country: 'ma' } // Restreindre au Maroc
            });

            // Événement : sélection d'une adresse via l'autocomplétion
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                
                if (!place.geometry || !place.geometry.location) {
                    console.log('Aucune géométrie trouvée pour cette adresse');
                    return;
                }

                const location = place.geometry.location;
                updateMapAndInputs(location.lat(), location.lng(), place.formatted_address);
            });

            // Événement : déplacement manuel du marqueur
            marker.addListener('dragend', function() {
                const position = marker.getPosition();
                const lat = position.lat();
                const lng = position.lng();
                
                // Mettre à jour les inputs cachés
                latInput.value = lat;
                lngInput.value = lng;

                // Géocodage inverse pour obtenir l'adresse
                geocoder.geocode({ location: { lat, lng } }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        adresseInput.value = results[0].formatted_address;
                    } else {
                        console.log('Géocodage inverse échoué : ' + status);
                    }
                });
            });

            // Configurer le bouton de géolocalisation
            setupGeolocationButton();
        }

        // Configuration du bouton de géolocalisation
        function setupGeolocationButton() {
            const btnLocate = document.getElementById('btnLocateMe');
            const locateText = document.getElementById('locateText');

            if (btnLocate) {
                btnLocate.addEventListener('click', function() {
                    if (!navigator.geolocation) {
                        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
                        return;
                    }

                    // Désactiver le bouton et changer le texte
                    btnLocate.disabled = true;
                    locateText.textContent = 'Localisation en cours...';

                    const options = {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    };

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            console.log('Position obtenue:', lat, lng);
                            
                            // Mettre à jour la carte d'abord
                            updateMapAndInputs(lat, lng, '');
                            
                            // Essayer plusieurs méthodes pour obtenir l'adresse
                            getAddressFromCoordinates(lat, lng)
                                .then(address => {
                                    if (address) {
                                        document.getElementById('adresse').value = address;
                                        showNotification('Position et adresse localisées avec succès!', 'success');
                                    } else {
                                        showNotification('Position localisée, mais adresse non trouvée.', 'warning');
                                    }
                                })
                                .catch(error => {
                                    console.error('Erreur géocodage:', error);
                                    showNotification('Position localisée, mais erreur lors de la récupération de l\'adresse.', 'warning');
                                })
                                .finally(() => {
                                    // Réactiver le bouton
                                    btnLocate.disabled = false;
                                    locateText.textContent = 'Localiser ma position';
                                });
                        },
                        function(error) {
                            let errorMessage = 'Erreur de géolocalisation : ';
                            
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += 'Permission refusée. Veuillez autoriser la géolocalisation.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += 'Position indisponible.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += 'Délai d\'attente dépassé.';
                                    break;
                                default:
                                    errorMessage += 'Erreur inconnue.';
                                    break;
                            }
                            
                            console.error('Erreur de géolocalisation:', error);
                            alert(errorMessage);
                            
                            // Réactiver le bouton
                            btnLocate.disabled = false;
                            locateText.textContent = 'Localiser ma position';
                        },
                        options
                    );
                });
            }
        }

        // Fonction pour obtenir l'adresse à partir des coordonnées avec plusieurs tentatives
        async function getAddressFromCoordinates(lat, lng) {
            const methods = [
                // Méthode 1: Google Geocoding API
                () => new Promise((resolve, reject) => {
                    geocoder.geocode({ 
                        location: { lat, lng },
                        language: 'fr',
                        region: 'MA'
                    }, function(results, status) {
                        if (status === 'OK' && results && results.length > 0) {
                            resolve(results[0].formatted_address);
                        } else {
                            reject(new Error('Google Geocoding failed: ' + status));
                        }
                    });
                }),
                
                // Méthode 2: Nominatim (OpenStreetMap)
                () => fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            return data.display_name;
                        }
                        throw new Error('Nominatim failed');
                    }),
                
                // Méthode 3: Places API Nearby Search comme fallback
                () => new Promise((resolve, reject) => {
                    const service = new google.maps.places.PlacesService(map);
                    service.nearbySearch({
                        location: { lat, lng },
                        radius: 50,
                        type: ['establishment']
                    }, function(results, status) {
                        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
                            resolve(results[0].vicinity || results[0].name);
                        } else {
                            reject(new Error('Places API failed: ' + status));
                        }
                    });
                })
            ];

            // Essayer chaque méthode jusqu'à ce qu'une fonctionne
            for (const method of methods) {
                try {
                    const address = await method();
                    if (address && address.trim()) {
                        console.log('Adresse trouvée:', address);
                        return address;
                    }
                } catch (error) {
                    console.log('Méthode échouée:', error.message);
                    continue;
                }
            }
            
            // Si toutes les méthodes échouent, créer une adresse basique
            return `Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}`;
        }
        // Fonction pour mettre à jour la carte et les inputs
        function updateMapAndInputs(lat, lng, address) {
            const position = { lat, lng };
            
            // Mettre à jour la carte
            map.setCenter(position);
            map.setZoom(15);
            
            // Mettre à jour le marqueur
            marker.setPosition(position);
            
            // Mettre à jour les inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            if (address) {
                document.getElementById('adresse').value = address;
            }
        }

        // Fonction pour afficher des notifications
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
                type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                'bg-red-100 text-red-800 border border-red-200'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Supprimer la notification après 3 secondes
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgc-1iIqCKAz2yAM6chdi7bnX68fUWZ2k&libraries=places&callback=initMap" async defer></script>
</body>
</html>