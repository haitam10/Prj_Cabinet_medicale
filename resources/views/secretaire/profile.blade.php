<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mon Profil - Cabinet Médical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgc-1iIqCKAz2yAM6chdi7bnX68fUWZ2k&libraries=places&callback=initMap" async defer></script>
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
                    <i class="fas fa-calendar-check mr-3 text-gray-400 group-hover:text-white"></i>
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
                        <i class="fas fa-file-medical mr-3 text-gray-400 group-hover:text-white"></i>
                        Dossier Médical
                    </a>
                    <a href="{{ route('secretaire.calendrier') }}"
                        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                        <i class="fas fa-calendar-alt mr-3 text-gray-400 group-hover:text-white"></i>
                        Calendrier
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
                                    placeholder="Commencez à taper votre adresse...">
                                @error('adresse')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $user->latitude ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $user->longitude ?? '') }}">

                                <div id="map" style="height: 300px; margin-top: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db;"></div>
                            </div>
                                                        <div>
                                <!-- BOUTON POUR LOCALISER -->
                                <button type="button" id="btnLocateMe" class="mb-2 px-4 py-2 bg-cordes-blue text-white rounded hover:bg-cordes-blue-dark transition">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Localiser ma position
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

        // Gestion du bouton Localiser ma position
        document.addEventListener('DOMContentLoaded', function () {
            const btnLocate = document.getElementById('btnLocateMe');
            if (btnLocate) {
                btnLocate.addEventListener('click', () => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Mettre à jour les inputs cachés
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;

                            // Mettre à jour la carte et le marqueur
                            const pos = new google.maps.LatLng(lat, lng);
                            map.setCenter(pos);
                            map.setZoom(15);
                            marker.setPosition(pos);

                            // Récupérer l'adresse inverse
                            const geocoder = new google.maps.Geocoder();
                            geocoder.geocode({ location: pos }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    document.getElementById('adresse').value = results[0].formatted_address;
                                }
                            });
                        }, error => {
                            alert('Impossible de récupérer votre position : ' + error.message);
                        });
                    } else {
                        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
                    }
                });
            }
        });
    </script>

    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgc-1iIqCKAz2yAM6chdi7bnX68fUWZ2k&libraries=places&callback=initMap"
      async
      defer>
    </script>

    <script>
      let map;
      let marker;
      let autocomplete;

      function initMap() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const adresseInput = document.getElementById('adresse');

        const lat = parseFloat(latInput.value) || 33.5731;      // Coordonnée par défaut (Casablanca)
        const lng = parseFloat(lngInput.value) || -7.5898;

        const initialPosition = { lat, lng };

        // Initialiser la carte
        map = new google.maps.Map(document.getElementById('map'), {
          center: initialPosition,
          zoom: 13,
        });

        // Marqueur draggable
        marker = new google.maps.Marker({
          position: initialPosition,
          map: map,
          draggable: true,
        });

        // Autocomplete Google Places sur le champ adresse
        autocomplete = new google.maps.places.Autocomplete(adresseInput, {
          types: ['geocode'],
        });

        // Quand l'utilisateur sélectionne une adresse
        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace();
          if (!place.geometry) {
            return;
          }
          const location = place.geometry.location;

          // Mettre à jour la carte et le marqueur
          map.setCenter(location);
          map.setZoom(15);
          marker.setPosition(location);

          // Mettre à jour les inputs cachés
          latInput.value = location.lat();
          lngInput.value = location.lng();
        });

        // Quand l'utilisateur déplace le marqueur manuellement
        marker.addListener('dragend', () => {
          const pos = marker.getPosition();
          latInput.value = pos.lat();
          lngInput.value = pos.lng();

          // Optionnel : récupérer adresse inverse
          const geocoder = new google.maps.Geocoder();
          geocoder.geocode({ location: pos }, (results, status) => {
            if (status === 'OK' && results[0]) {
              adresseInput.value = results[0].formatted_address;
            }
          });
        });
      }
    </script>
</body>
</html>