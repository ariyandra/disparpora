<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Disparpora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header dengan glass effect -->
    <header class="glass-effect sticky top-0 z-50 border-b border-white/20">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>DISPARPORA</h1>
            </div>
            
            <a href="{{ route('auntentikasi') }}" class="logout-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Login
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
                    Kelola data dan monitoring sistem dinas olahraga dengan mudah
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
                                <span class="text-sm font-medium text-green-600 bg-green-100 px-3 py-1 rounded-full">Hadir</span>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">{{ $jml_atlit ?? 0 }}</div>
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
                                <span class="text-sm font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">Tersedia</span>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800 mb-2 number-animation">{{ $jml_cabor ?? 0 }}</div>
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
                                <span class="text-sm font-medium text-orange-600 bg-orange-100 px-3 py-1 rounded-full">Hadir</span>
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
                                <span class="text-sm font-medium text-green-600 bg-green-100 px-3 py-1 rounded-full">Tersedia</span>
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

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .nav-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link.active,
        .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .number-animation {
            animation: countUp 2s ease-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .nav-menu {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</body>
</html>