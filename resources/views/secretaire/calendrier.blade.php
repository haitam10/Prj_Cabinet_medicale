<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Médecin - Calendrier</title>
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
        .modal {
            display: none;
        }

        .modal.show {
            display: flex;
        }

        /* Styles personnalisés pour la grille du calendrier */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .day-card {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

        .day-header {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .appointment-item {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 0.75rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .appointment-item:last-child {
            margin-bottom: 0;
        }

        .appointment-time {
            font-weight: 600;
            color: #1e40af;
        }

        .appointment-patient {
            font-weight: 500;
            color: #1e293b;
        }

        .appointment-motif {
            color: #475569;
            font-size: 0.875rem;
        }

        .appointment-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-confirmed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
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
                        class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Calendrier des Rendez-vous</h1>
                    <p class="text-gray-600 text-sm mt-1">Vos rendez-vous de la semaine</p>
                </div>
            </div>
        </header>

        <main class="p-6">
            @if (session('error'))
                <div id="errorMessage"
                    class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 transition-opacity duration-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <a href="{{ route('secretaire.calendrier', ['date' => $currentDate->copy()->subWeek()->format('Y-m-d')]) }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>Semaine précédente
                    </a>
                    <h3 class="text-xl font-semibold text-gray-800">
                        Semaine du {{ $startOfWeek->isoFormat('D MMMM YYYY') }} au
                        {{ $endOfWeek->isoFormat('D MMMM YYYY') }}
                    </h3>
                    <a href="{{ route('secretaire.calendrier', ['date' => $currentDate->copy()->addWeek()->format('Y-m-d')]) }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Semaine suivante<i class="fas fa-chevron-right ml-2"></i>
                    </a>
                </div>

                <div class="calendar-grid">
                    @foreach ($daysOfWeek as $dayData)
                        <div class="day-card">
                            <div class="day-header">
                                {{ $dayData['date']->isoFormat('dddd D MMMM') }}
                            </div>
                            <div class="space-y-3">
                                @forelse ($dayData['appointments'] as $appointment)
                                    <div class="appointment-item">
                                        <span class="appointment-time">
                                            <i
                                                class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($appointment->date)->format('H:i') }}
                                        </span>
                                        <span class="appointment-patient">
                                            <i class="fas fa-user mr-1"></i>{{ $appointment->patient->nom ?? 'N/A' }}
                                            {{ $appointment->patient->prenom ?? '' }}
                                        </span>
                                        <span class="appointment-motif">
                                            <i class="fas fa-info-circle mr-1"></i>{{ $appointment->motif }}
                                        </span>
                                        <span
                                            class="appointment-status
                                            @if ($appointment->statut === 'confirmé') status-confirmed
                                            @elseif($appointment->statut === 'en_attente') status-pending
                                            @elseif($appointment->statut === 'annulé') status-cancelled @endif">
                                            @switch($appointment->statut)
                                                @case('confirmé')
                                                    <i class="fas fa-check-circle"></i>Confirmé
                                                @break

                                                @case('en_attente')
                                                    <i class="fas fa-hourglass-half"></i>En attente
                                                @break

                                                @case('annulé')
                                                    <i class="fas fa-times-circle"></i>Annulé
                                                @break

                                                @default
                                                    {{ ucfirst($appointment->statut) }}
                                            @endswitch
                                        </span>
                                    </div>
                                    @empty
                                        <p class="text-gray-500 text-sm text-center py-4">Aucun rendez-vous pour ce jour.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </main>
        </div>

        <script>
            // Auto-dismiss messages after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const messages = document.querySelectorAll('#successMessage, #errorMessage');
                    messages.forEach(function(message) {
                        if (message) {
                            message.style.display = 'none';
                        }
                    });
                }, 5000);
            });
        </script>
    </body>

    </html>
