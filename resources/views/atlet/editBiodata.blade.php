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
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-content">
                    <div>
                        <h1 class="profile-title">Perbaharui Biodata</h1>
                        <p class="profile-subtitle">Kelola informasi personal Anda</p>
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
                <form action="{{ route('atlet.simpanUpdateBiodata') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <!-- Left Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nama
                                </label>
                                <input type="hidden" name="id_atlet" value="{{ $dataAtlet->id }}">
                                <input type="text" class="form-input" id="nama" name="nama" required value="{{ $dataAtlet->nama}}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </label>
                                <input type="email" class="form-input" id="email" name="email" 
                                    value="{{ $dataAtlet->email }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars"></i>
                                    Jenis Kelamin
                                </label>
                                <select class="form-select" id="jenisKelamin" name="jenis_kelamin">
                                    <option value="Laki-laki" {{ $dataAtlet->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ $dataAtlet->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    No HP
                                </label>
                                <input type="tel" class="form-input" id="noHP" name="no_hp" required value="{{ $dataAtlet->no_telp }}">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-running"></i>
                                    Cabang Olahraga
                                </label>
                                <select class="form-select" id="cabangOlahraga" name="cabang_olahraga">
                                    @foreach ($dataCabor as $cabor)
                                        <option value="{{ $cabor->id }}" {{ $dataAtlet->id_cabor == $cabor->id ? 'selected' : '' }}>{{ $cabor->nama_cabor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-birthday-cake"></i>
                                    Tanggal Lahir
                                </label>
                                <input type="date" class="form-input" id="tanggalLahir" name="tanggal_lahir" 
                                    value="{{ $dataAtlet->tanggal_lahir }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-plus"></i>
                                    Tanggal Gabung
                                </label>
                                <input type="date" class="form-input" id="tanggalGabung" name="tanggal_gabung" 
                                    value="{{ $dataAtlet->tanggal_gabung }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Status
                                </label>
                                <input type="text" class="form-input" id="status" name="status" 
                                    value="{{ $dataAtlet->status }}" required>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-print">
                            <i class="fas fa-print"></i>
                            Simpan
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </main>
</body>
</html>