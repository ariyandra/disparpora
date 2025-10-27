<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/pelatih.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/profil_diri.css') }}" rel="stylesheet">
    <style>
        /* Print only the #printable content */
        @media print {
            /* hide everything */
            body * { visibility: hidden; }
            /* show only the printable container */
            #printable, #printable * { visibility: visible; }
            /* position printable at top-left */
            #printable { position: absolute; left: 0; top: 0; width: 100%; }
            /* hide elements explicitly marked no-print */
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>DISPARPORA</h1>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard.atlet') }}" class="nav-link active">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.cabor') }}" class="nav-link">Cabang Olahraga</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.lapangan') }}" class="nav-link">Lapangan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.asesmen') }}" class="nav-link">Asesmen</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.absensi') }}" class="nav-link">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                <li class="nav-item">
                    <div class="notification-container" style="position: relative; cursor: pointer;" onclick="toggleNotifications()">
                        <span class="notification-icon" id="notificationBell">🔔</span>
                        <span class="notification-badge" id="notificationBadge" style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold;">{{ $notifikasi->count() }}</span>
                        
                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="notificationDropdown" style="position: absolute; top: 100%; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 1000; display: none; margin-top: 10px; border: 1px solid #e0e0e0;">
                            <div style="padding: 16px; border-bottom: 1px solid #f0f0f0;">
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Notifikasi</h3>
                            </div>
                            <div style="max-height: 300px; overflow-y: auto;">
                                <div class="notification-item" style="padding: 12px 16px; border-bottom: 1px solid #f8f8f8; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                    @foreach ($notifikasi as $n )
                                    <div style="display: flex; align-items: start; gap: 12px;">
                                        <div style="width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                                        <div style="flex: 1;">
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">{{ $n->keterangan }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div style="padding: 12px 16px; border-top: 1px solid #f0f0f0; text-align: center;">
                                <form action="{{ route('atlet.notifikasi') }}" method="POST">
                                    @csrf
                                    @foreach ($notifikasi as $n)
                                        <input type="hidden" name="notif_id[]" value="{{ $n->id }}">
                                    @endforeach
                                    <button type="submit"
                                        style="background: none; border: none; color: #667eea; font-size: 14px; cursor: pointer; font-weight: 500; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;"
                                        onmouseover="this.style.background='#f0f2ff'" 
                                        onmouseout="this.style.background='none'">
                                        Tandai Semua Dibaca
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <a href="{{ route('logout.atlet') }}" class="relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105" >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div id="printable">
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-content">
                    <div>
                        <h1 class="profile-title">Profil Diri</h1>
                        <p class="profile-subtitle">Kelola informasi personal Anda</p>
                    </div>
                    <div class="edit-actions">
                        <a href="{{ route('atlet.editBiodata') }}" class="btn btn-edit">
                            <i class="fas fa-edit"></i>
                            <span id="editText">Edit</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <div class="alert alert-success" style="display: none;" id="successAlert">
                    <i class="fas fa-check-circle"></i>
                    Profil berhasil disimpan!
                </div>

                @if($dataAtlet)
                    <div class="form-grid">
                        <!-- Left Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nama
                                </label>
                                <input type="text" class="form-input" id="nama" name="nama" 
                                    value="Ahmad Fajar Santoso" disabled style="display: none;">
                                <div class="form-display" id="namaDisplay">{{ $dataAtlet->nama }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </label>
                                <input type="email" class="form-input" id="email" name="email" 
                                    value="ahmad.fajar@example.com" disabled style="display: none;">
                                <div class="form-display" id="emailDisplay">{{ $dataAtlet->email }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars"></i>
                                    Jenis Kelamin
                                </label>
                                <div class="form-display" id="jenisKelaminDisplay">{{ $dataAtlet->jenis_kelamin }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    No HP
                                </label>
                                <input type="tel" class="form-input" id="noHP" name="no_hp" 
                                    value="+62 812-3456-7890" disabled style="display: none;">
                                <div class="form-display" id="noHPDisplay">{{ $dataAtlet->no_telp }}</div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-running"></i>
                                    Cabang Olahraga
                                </label>
                                <div class="form-display" id="cabangOlahragaDisplay">{{ $dataAtlet->cabor->nama_cabor }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-birthday-cake"></i>
                                    Tanggal Lahir
                                </label>
                                <input type="date" class="form-input" id="tanggalLahir" name="tanggal_lahir" 
                                    value="1995-05-15" disabled style="display: none;">
                                <div class="form-display" id="tanggalLahirDisplay">{{ $dataAtlet->tanggal_lahir }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-plus"></i>
                                    Tanggal Gabung
                                </label>
                                <input type="date" class="form-input" id="tanggalGabung" name="tanggal_gabung" 
                                    value="2023-01-10" disabled style="display: none;">
                                <div class="form-display" id="tanggalGabungDisplay">{{ $dataAtlet->tanggal_gabung }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Status
                                </label>
                                <textarea class="form-textarea" id="alamat" name="alamat" 
                                        rows="3" disabled style="display: none;"></textarea>
                                <div class="form-display" id="alamatDisplay">{{ $dataAtlet->status }}</div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-print no-print" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        Cetak Profil
                    </button>
                </div>
            </div>
        </div>
        </div>
    </main>

    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById("notificationDropdown");
            dropdown.style.display = dropdown.style.display === "none" || dropdown.style.display === "" ? "block" : "none";
        }

        function markAllAsRead() {
            const badge = document.getElementById("notificationBadge");
            if (badge) {
                badge.style.display = "none"; // sembunyikan badge
            }
            alert("Semua notifikasi ditandai sudah dibaca ✅");
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