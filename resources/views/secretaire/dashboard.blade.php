<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C-M Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-home mr-3 text-cordes-accent group-hover:text-white"></i>
                    Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-users mr-3 text-gray-400 group-hover:text-white"></i>
                    Rendez-vous
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-chart-bar mr-3 text-gray-400 group-hover:text-white"></i>
                    Patients
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-shopping-cart mr-3 text-gray-400 group-hover:text-white"></i>
                    Factures
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors group">
                    <i class="fas fa-box mr-3 text-gray-400 group-hover:text-white"></i>
                    Paiements
                </a>
            </div>
        </nav>
        
        <div class="absolute bottom-4 left-4 right-4">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/17003/17003310.png" alt="Admin" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="text-white text-sm font-medium">John Admin</p>
                        <p class="text-gray-400 text-xs">Administrator</p>
                    </div>
                </div>
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
                            <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard Content -->
        <main class="p-6">
            <div class="px-0 py-6">
                <!-- Action Buttons Row -->
                <div class="flex items-center justify-center space-x-6">
                    <!-- Ajouter RDV Button -->
                    <button onclick="openModal('rdvModal')" class="flex-1 max-w-xl bg-cordes-blue hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-calendar-plus text-xl mb-2"></i>
                        <div>Ajouter RDV</div>
                    </button>
                    
                    <!-- Ajouter Patient Button -->
                    <button onclick="openModal('patientModal')" class="flex-1 max-w-xl bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-user-plus text-xl mb-2"></i>
                        <div>Ajouter Patient</div>
                    </button>
                    
                    <!-- Generer Facture Button -->
                    <button onclick="openModal('factureModal')" class="flex-1 max-w-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-file-invoice text-xl mb-2"></i>
                        <div>Generer Facture</div>
                    </button>
                </div>
            </div>
            <div id="modals">
                @include('secretaire.partials.dashPopus')
            </div>
                    <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Revenue Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Factures</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_facts }}</p>
                            <div class="flex items-center mt-2">
                                {{-- <span class="text-green-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    12%
                                </span>
                                <span class="text-gray-500 text-sm ml-2">vs last month</span> --}}
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-cordes-blue bg-opacity-10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-cordes-blue text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Users Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Patients</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_pats }}</p>
                            <div class="flex items-center mt-2">
                                {{-- <span class="text-green-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    8%
                                </span>
                                <span class="text-gray-500 text-sm ml-2">vs last month</span> --}}
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Orders Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Paiements</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_pais }}</p>
                            <div class="flex items-center mt-2">
                                {{-- <span class="text-green-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    15%
                                </span>
                                <span class="text-gray-500 text-sm ml-2">vs last month</span> --}}
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Products Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rendez-vous</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count_rvs }}</p>
                            <div class="flex items-center mt-2">
                                {{-- <span class="text-green-600 text-sm font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    5%
                                </span>
                                <span class="text-gray-500 text-sm ml-2">vs last month</span> --}}
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Factures Table (replacing Charts Row) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Latest Factures Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Latest Factures</h3>
                            <p class="text-gray-600 text-sm">Dernières factures émises</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-cordes-blue hover:text-cordes-dark text-sm font-medium">View All</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médecin</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($latest_facs as $facture)
                                <tr class="hover:bg-blue-50 transition-colors duration-200 cursor-pointer">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#{{ $facture->id }}</span>
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
                                        @if($facture->statut == 'payée')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Payée
                                            </span>
                                        @elseif($facture->statut == 'en attente')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                En attente
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
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
                        @foreach($latest_rvs->take(4) as $rdv)
                        <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $rdv->patient->nom ?? 'Patient' }}</p>
                                <p class="text-sm text-gray-600">{{ $rdv->medecin->nom ?? 'Dr. Inconnu' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($rdv->heure)->format('H:i') }}</p>
                                @if($rdv->statut == 'confirmé')
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Confirmé</span>
                                @elseif($rdv->statut == 'en attente')
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">En attente</span>
                                @else
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">{{ ucfirst($rdv->statut) }}</span>
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
        // Add some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar navigation active state
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    navLinks.forEach(l => l.classList.remove('bg-gray-700', 'text-white'));
                    navLinks.forEach(l => l.classList.add('text-gray-300'));
                    this.classList.add('bg-gray-700', 'text-white');
                    this.classList.remove('text-gray-300');
                });
            });

            // Set dashboard as active by default
            navLinks[0].classList.add('bg-gray-700', 'text-white');
            navLinks[0].classList.remove('text-gray-300');

            // Notification bell animation
            const bellIcon = document.querySelector('.fa-bell');
            if (bellIcon) {
                setInterval(() => {
                    bellIcon.classList.add('animate-pulse');
                    setTimeout(() => {
                        bellIcon.classList.remove('animate-pulse');
                    }, 1000);
                }, 5000);
            }

            // Stats cards hover effects
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