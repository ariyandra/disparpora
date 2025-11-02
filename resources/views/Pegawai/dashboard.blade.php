<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard_pelatih.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pelatih.css') }}">
    <link href="{{ asset('css/pelatih-custom.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header dengan glass effect -->
    <header class="glass-effect sticky top-0 z-50 border-b border-white/20">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>DISPARPORA</h1>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard.pegawai') }}" class="nav-link active">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('data.pelatih') }}" class="nav-link">Pelatih</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('data.atlet') }}" class="nav-link">Atlet</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cabor') }}" class="nav-link">Cabor</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('lapangan') }}" class="nav-link">Lapangan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('asesmen') }}" class="nav-link">Asesmen</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi') }}" class="nav-link">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                @if(auth()->user()->role == 0 || auth()->user()->role == 1)
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link">User</a>
                </li>
                @endif
                @include('partials.notifications')
            </ul>
            
            <a href="{{ route('logout.admin') }}" class="logout-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
            </div>
    </header>

    <!-- Main Dashboard Content -->
    <main class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Dashboard Title -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-4 floating-animation">
                    Dashboard
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kelola jadwal, dan monitoring performa dengan mudah
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 mx-auto mt-4 rounded-full"></div>
            </div>
            
            <!-- Dashboard Statistics - 2x2 Grid -->
            <div class="mb-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Statistik Hari Ini</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1 - Atlet Aktif -->
                    <div class="card-hover bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden group">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">
                                    {{ $jml_atlit ?? 0}}
                                </div>
                                <h3 class="text-2xl font-semibold text-gray-700 mb-2">Atlet</h3>
                                <p class="text-gray-500">Tersedia</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 2 - Jadwal Latihan -->
                    <div class="card-hover bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden group">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">{{ $jml_cabor ?? 5 }}</div>
                                <h3 class="text-2xl font-semibold text-gray-700 mb-2">Cabang Olahraga</h3>
                                <p class="text-gray-500">Tersedia Sampai Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 3 - Asesmen Pending -->
                    <div class="card-hover bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden group">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">{{ $jml_pelatih ?? 0 }}</div>
                                <h3 class="text-2xl font-semibold text-gray-700 mb-2">Pelatih</h3>
                                <p class="text-gray-500">Tersedia Hingga Saat Ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card 4 - Lapangan Tersedia -->
                    <div class="card-hover bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden group">
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-4 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">{{ $jml_lapangan ?? 0 }}</div>
                                <h3 class="text-2xl font-semibold text-gray-700 mb-2">Lapangan</h3>
                                <p class="text-gray-500">Siap digunakan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Floating elements -->
    <div class="fixed top-20 left-10 w-20 h-20 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full opacity-10 floating-animation"></div>
    <div class="fixed bottom-20 right-10 w-16 h-16 bg-gradient-to-r from-pink-400 to-red-500 rounded-full opacity-10 floating-animation" style="animation-delay: 2s;"></div>

    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById("notificationDropdown");
            dropdown.style.display = dropdown.style.display === "none" || dropdown.style.display === "" ? "block" : "none";
        }

        // Tutup dropdown kalau klik di luar area notifikasi
        document.addEventListener("click", function(event) {
            const container = document.querySelector(".notification-container");
            if (container && !container.contains(event.target)) {
                document.getElementById("notificationDropdown").style.display = "none";
            }
        });
    </script>
</body>
</html>