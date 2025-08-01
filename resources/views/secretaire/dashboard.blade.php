<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medexa - Medical Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Original colors (kept in case they are used elsewhere in the dashboard)
                        'primary': '#6366f1',
                        'primary-dark': '#4f46e5',
                        'secondary': '#8b5cf6',
                        'accent': '#06b6d4',
                        'success': '#10b981',
                        'warning': '#f59e0b',
                        'danger': '#ef4444',
                        'dark': '#1e293b',
                        'light': '#f8fafc',
                        // Cordes colors from the second page, for consistent sidebar styling
                        "cordes-blue": "#1e40af",
                        "cordes-dark": "#1e293b",
                        "cordes-light": "#f8fafc",
                        "cordes-accent": "#3b82f6",
                    }
                }
            }
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
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
          class="flex items-center px-4 py-3 text-white bg-gray-700 rounded-lg transition-colors group">
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

    <!-- Main Content -->
    <div class="ml-64" id="main-content">
        <!-- Top Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->nom ?? 'Mohammad' }} 👋
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <select id="date-filter"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Day</option>
                            <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Month</option>
                            <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Year</option>
                        </select>
                    </div>
                    <button onclick="exportToPDF()"
                        class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                        <i class="fas fa-download mr-2"></i>Export PDF
                    </button>
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


            <!-- Stats Cards - Updated to show filtered data and be clickable -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Factures Card -->
                <div onclick="openFactureModal()"
                    class="bg-gradient-to-br from-primary to-primary-dark rounded-2xl p-6 text-white cursor-pointer hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                        </div>
                        <span class="text-white/80 text-sm"
                            id="facts-percent">{{ number_format($facts_diff_percent, 1) }}%</span>
                    </div>
                    <h3 class="text-white/80 text-sm font-medium mb-1">Factures</h3>
                    <p class="text-3xl font-bold" id="facts-count">{{ number_format($count_facts_filtered) }}</p>

                    <div class="mt-4 bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2"
                            style="width: {{ abs($facts_diff_percent) > 100 ? 100 : abs($facts_diff_percent) }}%">
                        </div>
                    </div>
                    <p class="text-white/80 text-xs mt-2" id="facts-change">
                        {{ $facts_diff_percent >= 0 ? 'Increase' : 'Decrease' }} by
                        {{ number_format($count_facts_filtered) }} Factures</p>
                </div>

                <!-- Patients Card -->
                <div onclick="openPatientModal()"
                    class="bg-white rounded-2xl p-6 border border-gray-100 cursor-pointer hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                        <span class="text-green-600 text-sm"
                            id="pats-percent">{{ number_format($pats_diff_percent, 1) }}%</span>
                    </div>
                    <h3 class="text-gray-600 text-sm font-medium mb-1">Patients</h3>
                    <p class="text-3xl font-bold text-gray-900" id="pats-count">
                        {{ number_format($count_pats_filtered) }}</p>
                    <div class="mt-4 bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 rounded-full h-2"
                            style="width: {{ abs($pats_diff_percent) > 100 ? 100 : abs($pats_diff_percent) }}%"></div>
                    </div>
                    <p class="text-gray-500 text-xs mt-2" id="pats-change">
                        {{ $pats_diff_percent >= 0 ? 'Increase' : 'Decrease' }} by
                        {{ number_format($count_pats_filtered) }} Patients</p>
                </div>

                <!-- Paiements Card -->
                <div onclick="openPaiementModal()"
                    class="bg-white rounded-2xl p-6 border border-gray-100 cursor-pointer hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-credit-card text-success text-xl"></i>
                        </div>
                        <span class="text-green-600 text-sm"
                            id="pais-percent">{{ number_format($pais_diff_percent, 1) }}%</span>
                    </div>
                    <h3 class="text-gray-600 text-sm font-medium mb-1">Paiements</h3>
                    <p class="text-3xl font-bold text-gray-900" id="pais-count">{{ $count_pais_filtered }}</p>
                    <div class="mt-4 bg-gray-200 rounded-full h-2">
                        <div class="bg-success rounded-full h-2"
                            style="width: {{ abs($pais_diff_percent) > 100 ? 100 : abs($pais_diff_percent) }}%"></div>
                    </div>
                    <p class="text-gray-500 text-xs mt-2" id="pais-change">
                        {{ $pais_diff_percent >= 0 ? 'Increase' : 'Decrease' }} by
                        {{ number_format($count_pais_filtered) }} Paiements</p>
                </div>

                <!-- Rendez-vous Card -->
                <div onclick="openAddModal()"
                    class="bg-white rounded-2xl p-6 border border-gray-100 cursor-pointer hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-info text-xl"></i>
                        </div>
                        <span class="text-green-600 text-sm"
                            id="rvs-percent">{{ number_format($rvs_diff_percent, 1) }}%</span>
                    </div>
                    <h3 class="text-gray-600 text-sm font-medium mb-1">Rendez-vous</h3>
                    <p class="text-3xl font-bold text-gray-900" id="rvs-count">{{ $count_rvs_filtered }}</p>
                    <div class="mt-4 bg-gray-200 rounded-full h-2">
                        <div class="bg-info rounded-full h-2"
                            style="width: {{ abs($rvs_diff_percent) > 100 ? 100 : abs($rvs_diff_percent) }}%"></div>
                    </div>
                    <p class="text-gray-500 text-xs mt-2" id="rvs-change">
                        {{ $rvs_diff_percent >= 0 ? 'Increase' : 'Decrease' }} by
                        {{ number_format($count_rvs_filtered) }} Rendez vous</p>
                </div>
            </div>


            <!-- Charts and Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rendez-vous</h3>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-primary rounded-full"></span>
                                <span class="text-sm text-gray-600">Confirmé</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
                                <span class="text-sm text-gray-600">En attente</span>
                            </div>

                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="rdvChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Factures</h3>
                        <button id="period_butt"
                            class="bg-primary text-white px-3 py-1 rounded-lg text-sm">{{ ucfirst($period) }}</button>
                    </div>
                    <div class="h-80">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>

           <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {{-- Main chart for payments statistics (now a line chart) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Paiements</h3>
                        </div>
                        <div class="flex items-center space-x-4">
                            {{-- Legend for the line chart --}}
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">Payé</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
                                <span class="text-sm text-gray-600">En attente</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">Échoué</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-80">
                        {{-- Canvas for the Paiements Line Chart --}}
                        <canvas id="paiementsLineChart"></canvas>
                    </div>
                </div>

                {{-- New chart for charges statistics (a doughnut chart) --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Charges</h3>
                        <button id="period_butt"
                            class="bg-primary text-white px-3 py-1 rounded-lg text-sm">{{ ucfirst($period) }}</button>
                    </div>
                    <div class="h-80 flex items-center justify-center">
                        {{-- Canvas for the Charges Doughnut Chart --}}
                        <canvas id="chargesDoughnutChart"></canvas>
                    </div>
                </div>
            </div>


            <!-- Latest Factures Table -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Latest Factures Table -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Latest Factures</h3>
                            <p class="text-gray-600 text-sm">Dernières factures émises</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('secretaire.factures') }}"">
                                <button class="text-primary hover:text-primary-dark text-sm font-medium">View
                                    All</button>
                            </a>
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
                                                class="text-sm font-medium text-gray-900">#{{ $loop->iteration }}</span>
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
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Payée</span>
                                            @elseif($facture->statut == 'en_attente')
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">En
                                                    attente</span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($facture->statut) }}</span>
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
                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Rendez-vous du Jour</h3>
                        <a href="{{ route('secretaire.rendezvous') }}"">
                            <button class="text-primary hover:text-primary-dark text-sm font-medium">View All</button>
                        </a>
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
                                        {{ \Carbon\Carbon::parse($rdv->appointment_time)->format('H:i') }}</p>
                                    @if ($rdv->status == 'confirmed')
                                        <span
                                            class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Confirmé</span>
                                    @elseif($rdv->status == 'pending')
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

            <!-- MODAL AJOUTER RDV - Fixed Version -->
            <div id="addModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-2xl rounded-lg shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Nouveau rendez-vous</h2>
                        <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <form action="{{ route('secretaire.rendezvous.store') }}" method="POST" class="space-y-4"
                        id="addForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient
                                    *</label>
                                <select name="patient_id" id="patient_id" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionnez un patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                            {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->nom }} {{ $patient->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                @if (Auth::user()->role === 'medecin')
                                    <input type="text" value="Dr. {{ Auth::user()->nom ?? 'Médecin' }}" readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                                @elseif(Auth::user()->role === 'secretaire' && Auth::user()->medecin_id)
                                    <input type="text" value="Dr. {{ Auth::user()->medecin->nom ?? 'Médecin' }}"
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed outline-none">
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="appointment_date"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                                <select name="appointment_date" id="appointment_date" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionnez une date</option>
                                    @foreach ($disponibilites as $disp)
                                        <option value="{{ $disp->date }}"
                                            {{ old('appointment_date') == $disp->date ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($disp->date)->format('d/m/Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($disponibilites->isEmpty())
                                    <p class="text-sm text-red-600 mt-1">Aucune disponibilité définie. Veuillez d'abord
                                        créer des disponibilités.</p>
                                @endif
                            </div>
                            <div>
                                <label for="appointment_time"
                                    class="block text-sm font-medium text-gray-700 mb-1">Créneau horaire *</label>
                                <select name="appointment_time" id="appointment_time" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="">Sélectionnez d'abord une date</option>
                                </select>
                            </div>
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durée
                                    (min)</label>
                                <input type="number" name="duration" id="duration" value="30" readonly
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-600 cursor-not-allowed outline-none" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut
                                    *</label>
                                <select name="status" id="status" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En
                                        attente</option>
                                    <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>
                                        Confirmé</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                        Terminé</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                        Annulé</option>
                                </select>
                            </div>
                            <div>
                                <label for="appointment_type"
                                    class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select name="appointment_type" id="appointment_type" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent">
                                    <option value="consultation"
                                        {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>Consultation
                                    </option>
                                    <option value="follow_up"
                                        {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                                    <option value="emergency"
                                        {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                                    <option value="routine"
                                        {{ old('appointment_type') == 'routine' ? 'selected' : '' }}>Routine</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif de
                                consultation</label>
                            <textarea name="reason" id="reason" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Décrivez le motif de la consultation...">{{ old('reason') }}</textarea>
                        </div>

                        <div>
                            <label for="patient_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes du
                                patient</label>
                            <textarea name="patient_notes" id="patient_notes" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Notes ou remarques du patient...">{{ old('patient_notes') }}</textarea>
                        </div>

                        @if (Auth::user()->role === 'medecin')
                            <div>
                                <label for="doctor_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes
                                    du médecin</label>
                                <textarea name="doctor_notes" id="doctor_notes" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                    placeholder="Notes médicales...">{{ old('doctor_notes') }}</textarea>
                            </div>
                        @endif

                        <div id="addCancellationFields" class="hidden">
                            <label for="cancellation_reason"
                                class="block text-sm font-medium text-gray-700 mb-1">Raison de l'annulation</label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Expliquez la raison de l'annulation...">{{ old('cancellation_reason') }}</textarea>
                        </div>

                        <div>
                            <label for="feedback"
                                class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                            <textarea name="feedback" id="feedback" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cordes-blue focus:border-transparent"
                                placeholder="Commentaires ou feedback...">{{ old('feedback') }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeAddModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL AJOUTER PATIENT -->
            <div id="patientModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau patient</h2>
                        <button onclick="closePatientModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
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
                                    <label for="cin" class="block text-sm font-medium text-gray-700 mb-1">CIN
                                        *</label>
                                    <input type="text" name="cin" id="cin" required
                                        value="{{ old('cin') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('cin') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="Ex: AB123456" autocomplete="off">
                                    @if ($errors->has('cin'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('cin') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom
                                        complet *</label>
                                    <input type="text" name="nom" id="nom" required
                                        value="{{ old('nom') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('nom') ? 'border-red-500 bg-red-50' : '' }}"
                                        autocomplete="name">
                                    @if ($errors->has('nom'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('nom') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe
                                        *</label>
                                    <select name="sexe" id="sexe" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('sexe') ? 'border-red-500 bg-red-50' : '' }}"
                                        autocomplete="sex">
                                        <option value="">Sélectionner</option>
                                        <option value="homme" {{ old('sexe') == 'homme' ? 'selected' : '' }}>Homme
                                        </option>
                                        <option value="femme" {{ old('sexe') == 'femme' ? 'selected' : '' }}>Femme
                                        </option>
                                    </select>
                                    @if ($errors->has('sexe'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('sexe') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="date_naissance"
                                        class="block text-sm font-medium text-gray-700 mb-1">Date de naissance
                                        *</label>
                                    <input type="date" name="date_naissance" id="date_naissance" required
                                        value="{{ old('date_naissance') }}" max="{{ date('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('date_naissance') ? 'border-red-500 bg-red-50' : '' }}"
                                        autocomplete="bday">
                                    @if ($errors->has('date_naissance'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('date_naissance') }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <label for="profession"
                                        class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                                    <input type="text" name="profession" id="profession"
                                        value="{{ old('profession') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        autocomplete="organization-title">
                                </div>
                                <div>
                                    <label for="situation_familiale"
                                        class="block text-sm font-medium text-gray-700 mb-1">Situation
                                        familiale</label>
                                    <select name="situation_familiale" id="situation_familiale"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        autocomplete="off">
                                        <option value="">Sélectionner</option>
                                        <option value="celibataire"
                                            {{ old('situation_familiale') == 'celibataire' ? 'selected' : '' }}>
                                            Célibataire</option>
                                        <option value="marie"
                                            {{ old('situation_familiale') == 'marie' ? 'selected' : '' }}>Marié(e)
                                        </option>
                                        <option value="divorce"
                                            {{ old('situation_familiale') == 'divorce' ? 'selected' : '' }}>Divorcé(e)
                                        </option>
                                        <option value="veuf"
                                            {{ old('situation_familiale') == 'veuf' ? 'selected' : '' }}>Veuf/Veuve
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de contact -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="contact"
                                        class="block text-sm font-medium text-gray-700 mb-1">Téléphone principal
                                        *</label>
                                    <input type="tel" name="contact" id="contact" required
                                        value="{{ old('contact') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('contact') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="Ex: 0612345678" autocomplete="tel">
                                    @if ($errors->has('contact'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('contact') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="telephone_secondaire"
                                        class="block text-sm font-medium text-gray-700 mb-1">Téléphone
                                        secondaire</label>
                                    <input type="tel" name="telephone_secondaire" id="telephone_secondaire"
                                        value="{{ old('telephone_secondaire') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Ex: 0612345678" autocomplete="tel">
                                </div>
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent {{ $errors->has('email') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="exemple@email.com" autocomplete="email">
                                    @if ($errors->has('email'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label for="adresse"
                                        class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                                    <textarea name="adresse" id="adresse" rows="2"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Adresse complète" autocomplete="street-address">{{ old('adresse') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Informations médicales -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Informations médicales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label for="groupe_sanguin"
                                        class="block text-sm font-medium text-gray-700 mb-1">Groupe sanguin</label>
                                    <select name="groupe_sanguin" id="groupe_sanguin"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        autocomplete="off">
                                        <option value="">Sélectionner</option>
                                        <option value="A+" {{ old('groupe_sanguin') == 'A+' ? 'selected' : '' }}>
                                            A+</option>
                                        <option value="A-" {{ old('groupe_sanguin') == 'A-' ? 'selected' : '' }}>
                                            A-</option>
                                        <option value="B+" {{ old('groupe_sanguin') == 'B+' ? 'selected' : '' }}>
                                            B+</option>
                                        <option value="B-" {{ old('groupe_sanguin') == 'B-' ? 'selected' : '' }}>
                                            B-</option>
                                        <option value="AB+"
                                            {{ old('groupe_sanguin') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-"
                                            {{ old('groupe_sanguin') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('groupe_sanguin') == 'O+' ? 'selected' : '' }}>
                                            O+</option>
                                        <option value="O-" {{ old('groupe_sanguin') == 'O-' ? 'selected' : '' }}>
                                            O-</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="poids" class="block text-sm font-medium text-gray-700 mb-1">Poids
                                        (kg)</label>
                                    <input type="number" name="poids" id="poids" step="0.1"
                                        min="0" max="999.99" value="{{ old('poids') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Ex: 70.5" autocomplete="off">
                                </div>
                                <div>
                                    <label for="taille" class="block text-sm font-medium text-gray-700 mb-1">Taille
                                        (cm)</label>
                                    <input type="number" name="taille" id="taille" step="0.1"
                                        min="0" max="999.99" value="{{ old('taille') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Ex: 175" autocomplete="off">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="allergies"
                                        class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                                    <textarea name="allergies" id="allergies" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Allergies connues..." autocomplete="off">{{ old('allergies') }}</textarea>
                                </div>
                                <div>
                                    <label for="antecedents"
                                        class="block text-sm font-medium text-gray-700 mb-1">Antécédents</label>
                                    <textarea name="antecedents" id="antecedents" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Antécédents médicaux..." autocomplete="off">{{ old('antecedents') }}</textarea>
                                </div>
                                <div>
                                    <label for="medicaments"
                                        class="block text-sm font-medium text-gray-700 mb-1">Médicaments</label>
                                    <textarea name="medicaments" id="medicaments" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Médicaments actuels..." autocomplete="off">{{ old('medicaments') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closePatientModal()"
                                class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL GENERER FACTURE -->
            <div id="factureModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Générer Facture</h2>
                        <button onclick="closeFactureModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <form action="{{ route('factures.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="patient_id_facture"
                                class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                            <select name="patient_id" id="patient_id_facture" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Sélectionner un patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->nom }} ({{ $patient->cin }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="medecin_id_facture"
                                class="block text-sm font-medium text-gray-700 mb-1">Médecin *</label>
                            <select name="medecin_id" id="medecin_id_facture" required
                                @if (Auth::user()->role === 'medecin') readonly @endif
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Sélectionner un médecin</option>
                                @foreach ($medecins as $medecin)
                                    <option value="{{ $medecin->id }}"
                                        @if (Auth::user()->role === 'medecin' && Auth::id() === $medecin->id) selected @endif>
                                        {{ $medecin->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="secretaire_id_facture"
                                class="block text-sm font-medium text-gray-700 mb-1">Secrétaire</label>
                            <select name="secretaire_id" id="secretaire_id_facture"
                                @if (Auth::user()->role === 'secretaire') readonly @endif
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Sélectionner un secrétaire</option>
                                @foreach ($secretaires as $secretaire)
                                    <option value="{{ $secretaire->id }}"
                                        @if (Auth::user()->role === 'secretaire' && Auth::id() === $secretaire->id) selected @endif>
                                        {{ $secretaire->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="montant_facture"
                                    class="block text-sm font-medium text-gray-700 mb-1">Montant (DH) *</label>
                                <input type="number" name="montant" id="montant_facture" step="0.01"
                                    min="0" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="statut_facture"
                                    class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                                <select name="statut" id="statut_facture" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Sélectionner</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="payée">Payée</option>
                                    <option value="annulée">Annulée</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="date_facture" class="block text-sm font-medium text-gray-700 mb-1">Date
                                *</label>
                            <input type="date" name="date" id="date_facture" required readonly
                                value="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50">
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeFactureModal()"
                                class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Créer Paiement -->
            <div id="createPaiementModal"
                class="modal fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau paiement</h2>
                        <button onclick="closePaiementModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('paiements.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="facture_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Facture <span class="text-red-500">*</span>
                            </label>
                            <select id="facture_id" name="facture_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Sélectionner une facture</option>
                                @foreach ($factures ?? [] as $facture)
                                    <option value="{{ $facture->id }}" data-montant="{{ $facture->montant }}">
                                        Facture - {{ $facture->patient->nom ?? 'N/A' }}
                                        {{ $facture->patient->prenom ?? '' }}
                                        ({{ number_format($facture->montant, 2) }} DH)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="montant_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Montant (DH) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="montant_paiement" name="montant" step="0.01"
                                    min="0" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="date_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date de Paiement <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date_paiement" name="date_paiement"
                                    value="{{ date('Y-m-d') }}" required readonly
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mode de Paiement <span class="text-red-500">*</span>
                                </label>
                                <select id="mode_paiement" name="mode_paiement" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Sélectionner un mode</option>
                                    <option value="especes">💵 Espèces</option>
                                    <option value="carte_bancaire">💳 Carte Bancaire</option>
                                    <option value="cheque">📝 Chèque</option>
                                    <option value="virement">🏦 Virement</option>
                                    <option value="paypal">💻 PayPal</option>
                                </select>
                            </div>
                            <div>
                                <label for="statut_paiement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select id="statut_paiement" name="statut" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Sélectionner un statut</option>
                                    <option value="paye">✅ Payé</option>
                                    <option value="en_attente">⏳ En attente</option>
                                    <option value="echoue">❌ Échoué</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closePaiementModal()"
                                class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Configuration CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Global chart instances
        let rdvChartInstance = null;
        let financialChartInstance = null;

        // Données des disponibilités et rendez-vous existants (similaire à la page rendez-vous)
        const disponibilites = @json($disponibilitesJS ?? []);
        const existingAppointments = @json($existingAppointmentsJS ?? []);

        // PDF Export Function
        async function exportToPDF() {
            const exportBtn = document.querySelector('button[onclick="exportToPDF()"]');
            const originalText = exportBtn.innerHTML;

            try {
                // Show loading state
                exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
                exportBtn.disabled = true;

                const element = document.getElementById('main-content');

                // Render the element to canvas
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: element.scrollWidth,
                    height: element.scrollHeight
                });

                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');

                // Convert canvas to image
                const imgData = canvas.toDataURL('image/png');

                // Calculate dimensions
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 295; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                let heightLeft = imgHeight;
                let position = 0;

                // Add the first page
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                // Add more pages if needed
                while (heightLeft > 0) {
                    position -= pageHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Generate filename with date
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const filename = `Medexa_Dashboard_${dateStr}.pdf`;

                pdf.save(filename);

                // Show success message
                showTemporaryMessage('Dashboard exported successfully as PDF!', 'success');

            } catch (error) {
                console.error('Error generating PDF:', error);
                showTemporaryMessage('Failed to export PDF. Please try again.', 'error');
            } finally {
                // Restore button
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
            }
        }


        // Function to hide messages automatically after 5 seconds
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
            if (main) {
                main.insertBefore(messageDiv, main.firstChild);
            } else {
                document.body.insertBefore(messageDiv, document.body.firstChild);
            }

            setTimeout(() => {
                hideMessage(messageDiv);
            }, 5000);
        }

        function validateDateTime(dateValue, timeValue) {
            const now = new Date();
            const selectedDateTime = new Date(dateValue + 'T' + timeValue);

            if (selectedDateTime <= now) {
                showTemporaryMessage('La date et l\'heure du rendez-vous ne peuvent pas être dans le passé.', 'error');
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

        // Time slot functions (copied from rendezvous page)
        function generateTimeSlots(startTime, endTime) {
            const slots = [];
            const start = new Date(`2000-01-01T${startTime}:00`);
            const end = new Date(`2000-01-01T${endTime}:00`);

            let current = new Date(start);

            while (current < end) {
                const timeString = current.toTimeString().substring(0, 5);
                const nextSlot = new Date(current.getTime() + 30 * 60000);
                const nextTimeString = nextSlot.toTimeString().substring(0, 5);

                if (nextSlot <= end) {
                    slots.push({
                        value: timeString,
                        label: `${timeString} - ${nextTimeString}`
                    });
                }

                current = nextSlot;
            }

            return slots;
        }

        function isTimeSlotOccupied(date, time, excludeId = null) {
            return existingAppointments.some(appointment => {
                return appointment.date === date &&
                    appointment.time === time &&
                    appointment.status !== 'cancelled' &&
                    appointment.id !== excludeId;
            });
        }

        function updateTimeSlots(dateSelect, timeSelect, excludeId = null) {
            const selectedDate = dateSelect.value;
            timeSelect.innerHTML = '<option value="">Sélectionnez un créneau</option>';

            if (!selectedDate) {
                return;
            }

            const disponibilite = disponibilites.find(disp => disp.date === selectedDate);

            if (!disponibilite) {
                timeSelect.innerHTML = '<option value="">Aucune disponibilité pour cette date</option>';
                return;
            }

            const slots = generateTimeSlots(disponibilite.heure_entree, disponibilite.heure_sortie);

            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.value;
                option.textContent = slot.label;

                if (isTimeSlotOccupied(selectedDate, slot.value, excludeId)) {
                    option.disabled = true;
                    option.textContent += ' (Occupé)';
                    option.style.color = '#ef4444';
                }

                timeSelect.appendChild(option);
            });
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

        // RDV Modal Functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.querySelector('#addModal form').reset();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // PATIENT Modal Functions
        function openPatientModal() {
            document.getElementById('patientModal').classList.remove('hidden');
            document.querySelector('#patientModal form').reset();
        }

        function closePatientModal() {
            document.getElementById('patientModal').classList.add('hidden');
        }

        // FACTURE Modal Functions
        function openFactureModal() {
            document.getElementById('factureModal').classList.remove('hidden');
            document.querySelector('#factureModal form').reset();
        }

        function closeFactureModal() {
            document.getElementById('factureModal').classList.add('hidden');
        }

        function openPaiementModal() {
            document.getElementById('createPaiementModal').classList.remove('hidden');
            document.querySelector('#createPaiementModal form').reset();
        }

        function closePaiementModal() {
            document.getElementById('createPaiementModal').classList.add('hidden');
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
                closeFactureModal();
            }
        });

        function updateRdvChart(labels, confirmedData, pendingData, maxYValue) {
            if (rdvChartInstance) {
                rdvChartInstance.destroy();
            }
            const rdvCtx = document.getElementById('rdvChart').getContext('2d');
            rdvChartInstance = new Chart(rdvCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'confirmed',
                        data: confirmedData,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 40
                    }, {
                        label: 'En attente',
                        data: pendingData,
                        backgroundColor: '#fbbf24',
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: '#f3f4f6'
                            },
                            ...(maxYValue && {
                                max: maxYValue + (maxYValue * 0.1)
                            })
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function updateFinancialChart(labels, data, maxYValue) {
            if (financialChartInstance) {
                financialChartInstance.destroy();
            }
            const financialCtx = document.getElementById('financialChart').getContext('2d');
            financialChartInstance = new Chart(financialCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: '#f3f4f6'
                            },
                            ...(maxYValue && {
                                max: maxYValue + (maxYValue * 0.1)
                            })
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }



        // Function to update dashboard card values
        function updateDashboardCards(data) {
            // Update counts
            document.getElementById('facts-count').textContent = new Intl.NumberFormat().format(data.count_facts_filtered);
            document.getElementById('pats-count').textContent = new Intl.NumberFormat().format(data.count_pats_filtered);
            document.getElementById('pais-count').textContent = data.count_pais_filtered;
            document.getElementById('rvs-count').textContent = data.count_rvs_filtered;

            // Update percentages
            document.getElementById('facts-percent').textContent = data.facts_diff_percent + '%';
            document.getElementById('pats-percent').textContent = data.pats_diff_percent + '%';
            document.getElementById('pais-percent').textContent = data.pais_diff_percent + '%';
            document.getElementById('rvs-percent').textContent = data.rvs_diff_percent + '%';

            // Update change descriptions
            document.getElementById('facts-change').textContent =
                `${data.facts_diff_percent >= 0 ? 'Increase' : 'Decrease'} by ${new Intl.NumberFormat().format(data.count_facts_filtered)} Factures`;
            document.getElementById('pats-change').textContent =
                `${data.pats_diff_percent >= 0 ? 'Increase' : 'Decrease'} by ${new Intl.NumberFormat().format(data.count_pats_filtered)} Patients`;
            document.getElementById('pais-change').textContent =
                `${data.pais_diff_percent >= 0 ? 'Increase' : 'Decrease'} by ${new Intl.NumberFormat().format(data.count_pais_filtered)} Paiements`;
            document.getElementById('rvs-change').textContent =
                `${data.rvs_diff_percent >= 0 ? 'Increase' : 'Decrease'} by ${new Intl.NumberFormat().format(data.count_rvs_filtered)} Rendez vous`;

            // Update progress bars
            const factsBar = document.querySelector('#facts-count').closest('.bg-gradient-to-br').querySelector(
                '.bg-white');
            const patsBar = document.querySelector('#pats-count').closest('.bg-white').querySelector('.bg-green-600');
            const paisBar = document.querySelector('#pais-count').closest('.bg-white').querySelector('.bg-success');
            const rvsBar = document.querySelector('#rvs-count').closest('.bg-white').querySelector('.bg-info');

            if (factsBar) factsBar.style.width = `${Math.min(Math.abs(data.facts_diff_percent), 100)}%`;
            if (patsBar) patsBar.style.width = `${Math.min(Math.abs(data.pats_diff_percent), 100)}%`;
            if (paisBar) paisBar.style.width = `${Math.min(Math.abs(data.pais_diff_percent), 100)}%`;
            if (rvsBar) rvsBar.style.width = `${Math.min(Math.abs(data.rvs_diff_percent), 100)}%`;
        }

        let paiementsLineChartInstance = null;
        let chargesDoughnutChartInstance = null;

        // Function to update the payments line chart
        function updatePaiementsLineChart(labels, payeData, attenteData, echoueData, maxYValue) {
            if (paiementsLineChartInstance) {
                paiementsLineChartInstance.destroy();
            }
            
            const ctx = document.getElementById('paiementsLineChart').getContext('2d');
            paiementsLineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Payé',
                            data: payeData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        },
                        {
                            label: 'En attente',
                            data: attenteData,
                            borderColor: '#fbbf24',
                            backgroundColor: 'rgba(251, 191, 36, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#fbbf24',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        },
                        {
                            label: 'Échoué',
                            data: echoueData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Legend is handled by the HTML
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#e5e7eb',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: '#f3f4f6'
                            },
                            ticks: {
                                color: '#6b7280'
                            },
                            ...(maxYValue && {
                                max: maxYValue
                            })
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        // Function to update the charges doughnut chart
        function updateChargesDoughnutChart(labels, data, colors, hoverData) {
            if (chargesDoughnutChartInstance) {
                chargesDoughnutChartInstance.destroy();
            }
            
            const ctx = document.getElementById('chargesDoughnutChart').getContext('2d');
            chargesDoughnutChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverBorderWidth: 4,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // We'll create custom legend
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const hoverValue = hoverData[context.dataIndex] || '';
                                    return `${label}: ${value}% (${hoverValue})`;
                                }
                            }
                        }
                    },
                    cutout: '60%',
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        }

        // Update the fetchDashboardData function to handle new charts
        function fetchDashboardData(period) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/secretaire/dashboard?period=${period}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log("Response Status:", response.status);
                    console.log("Response Content-Type:", response.headers.get("Content-Type"));

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error("Non-OK response text:", text);
                            throw new Error(
                                `HTTP error! status: ${response.status}. Response: ${text.substring(0, 200)}...`
                            );
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Fetched dashboard data:", data);

                    // Update dashboard cards with new data
                    updateDashboardCards(data);

                    // Update existing charts
                    updateRdvChart(data.rdv_chart_labels, data.rdv_chart_confirmed_data, data.rdv_chart_pending_data, data.rdv_chart_max_y);
                    updateFinancialChart(data.financial_chart_labels, data.financial_chart_data, data.financial_chart_max_y);

                    // Update new charts
                    updatePaiementsLineChart(
                        data.payment_line_labels, 
                        data.payment_line_paye_data, 
                        data.payment_line_attente_data, 
                        data.payment_line_echoue_data, 
                        data.payment_line_chart_max_y
                    );
                    
                    updateChargesDoughnutChart(
                        data.charges_labels, 
                        data.charges_data, 
                        data.charges_colors, 
                        data.charges_hover_data
                    );

                })
                .catch(error => {
                    console.error('Error fetching dashboard data:', error);
                    showTemporaryMessage('Failed to update dashboard data. Check console for details.', 'error');
                });
        }

        // Initialize the new charts on page load
        document.addEventListener('DOMContentLoaded', function() {
            // ... existing DOMContentLoaded code ...
            
            // Initialize new charts with initial data
            const initialPaymentLineLabels = @json($payment_line_labels ?? []);
            const initialPaymentLinePayeData = @json($payment_line_paye_data ?? []);
            const initialPaymentLineAttenteData = @json($payment_line_attente_data ?? []);
            const initialPaymentLineEchoueData = @json($payment_line_echoue_data ?? []);
            const initialPaymentLineMaxY = @json($payment_line_chart_max_y ?? 10);
            
            const initialChargesLabels = @json($charges_labels ?? []);
            const initialChargesData = @json($charges_data ?? []);
            const initialChargesColors = @json($charges_colors ?? []);
            const initialChargesHoverData = @json($charges_hover_data ?? []);
            
            // Initialize the new charts
            updatePaiementsLineChart(
                initialPaymentLineLabels,
                initialPaymentLinePayeData,
                initialPaymentLineAttenteData,
                initialPaymentLineEchoueData,
                initialPaymentLineMaxY
            );
            
            updateChargesDoughnutChart(
                initialChargesLabels,
                initialChargesData,
                initialChargesColors,
                initialChargesHoverData
            );
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

            // Event listener for the timeframe select dropdown
            const dateFilterSelect = document.getElementById('date-filter');
            const periodButton = document.getElementById('period_butt');

            if (dateFilterSelect) {
                const updatePeriodButton = (value) => {
                    periodButton.textContent = value.charAt(0).toUpperCase() + value.slice(1);
                };

                // Set initial value on load
                updatePeriodButton(dateFilterSelect.value);

                // Update when select changes
                dateFilterSelect.addEventListener('change', function() {
                    updatePeriodButton(this.value);
                    fetchDashboardData(this.value);
                });

                // Initial data load
                fetchDashboardData(dateFilterSelect.value);
            }

            // Setup appointment form validation and event listeners
            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    const dateSelect = document.getElementById('appointment_date');
                    const timeSelect = document.getElementById('appointment_time');
                    const statusSelect = document.getElementById('status');

                    if (!dateSelect.value) {
                        e.preventDefault();
                        showTemporaryMessage('Veuillez sélectionner une date.', 'error');
                        return false;
                    }

                    if (!timeSelect.value && statusSelect.value !== 'cancelled') {
                        e.preventDefault();
                        showTemporaryMessage('Veuillez sélectionner un créneau horaire.', 'error');
                        return false;
                    }
                });
            }

            // Event listeners for time slot updates
            const appointmentDateSelect = document.getElementById('appointment_date');
            const appointmentTimeSelect = document.getElementById('appointment_time');

            if (appointmentDateSelect && appointmentTimeSelect) {
                appointmentDateSelect.addEventListener('change', function() {
                    updateTimeSlots(this, appointmentTimeSelect);
                });
            }

            // Event listener for status change to show/hide cancellation fields
            const statusSelect = document.getElementById('status');
            if (statusSelect) {
                statusSelect.addEventListener('change', toggleAddCancellationFields);
            }

            // Sidebar navigation active state
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    navLinks.forEach(l => l.classList.remove('text-primary', 'bg-primary/10'));
                    navLinks.forEach(l => l.classList.add('text-gray-600'));
                    this.classList.add('text-primary', 'bg-primary/10');
                    this.classList.remove('text-gray-600');
                });
            });

            // Set initial active state for the first nav link
            if (navLinks.length > 0) {
                navLinks[0].classList.add('text-primary', 'bg-primary/10');
                navLinks[0].classList.remove('text-gray-600');
            }

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

            // Show modal if there are validation errors
            @if ($errors->any() && old('_token'))
                setTimeout(() => {
                    openAddModal();
                }, 100);
            @endif
        });
    </script>
</body>

</html>