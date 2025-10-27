<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - Dinas Pariwisata Pemuda dan Olahraga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Asumsi file CSS ini sudah ada atau perlu Anda tambahkan di public/css -->
    <link href="{{ asset('css/pelatih.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pelatih-custom.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>DISPARPORA</h1>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard.pelatih') }}" class="nav-link">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('atlet.pelatih') }}" class="nav-link">Atlet</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cabor.pelatih') }}" class="nav-link">Cabang Olahraga</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('lapangan.pelatih') }}" class="nav-link">Lapangan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('asesmen.pelatih') }}" class="nav-link">Asesmen</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi.pelatih') }}" class="nav-link active">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal.pelatih') }}" class="nav-link">Jadwal</a>
                </li>
            </ul>
            
            <a href="{{ route('logout.pelatih') }}" class="logout-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
            </div>
    </nav>

    <main class="main-content">
        <div class="page-container">
            <!-- Page Header Card -->
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Absensi</h1>
                    <a href="{{ route('pelatih.isiAbsensi') }}" class="btn-tambah" style="text-decoration: none;">
                        <span>➕</span> Tambah Absensi
                    </a>
                </div>
            </div>
            
            <!-- Table Container Card -->
            <div class="table-container-card">
                <div class="table-header">
                    <h2 class="table-title">Daftar Absensi</h2>
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Cari absensi..." id="searchInput">
                    </div>
                </div>
                
                <table class="data-table" id="absensiTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Atlet</th>
                            <th>Tanggal</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th> <!-- Kolom Aksi Ditambahkan -->
                        </tr>
                    </thead>
                    <tbody id="absensiTableBody">
                        @foreach ($dataAbsensi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->atlet->nama ?? 'N/A' }}</td> <!-- Menggunakan ?? untuk pencegahan error relasi -->
                                <td>{{ $item->tanggal_absen }}</td>
                                <td>{{ substr($item->jadwal ?? 'N/A', 0, 5) }}</td>  <!-- Menggunakan relasi Jadwal -->
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="action-cell">
                                    <div class="action-buttons">
                                        <!-- Formulir Ubah -->
                                        <form action="{{ route('pelatih.ubahAbsensi') }}" method="POST">
                                            @csrf
                                            <!-- Variabel $absensi diganti menjadi $item -->
                                            <input type="hidden" name="id_absensi" value="{{ $item->id }}">
                                            <button type="submit" class="btn-action btn-edit">
                                                <span>✏️</span> Edit
                                            </button>
                                        </form>

                                        <!-- Formulir Hapus -->
                                        <form action="{{ route('pelatih.hapusAbsensi') }}" method="POST">
                                            @csrf
                                            <!-- Variabel $absensi diganti menjadi $item -->
                                            <input type="hidden" name="id_absensi" value="{{ $item->id }}">
                                            <button type="submit" class="btn-action btn-delete">
                                                <span>🗑️</span> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="pagination">
                    <button class="pagination-btn" onclick="changePage('prev')">‹ Sebelumnya</button>
                    <!-- page buttons inserted by JS -->
                    <button class="pagination-btn" onclick="changePage('next')">Selanjutnya ›</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Pagination functionality (10 rows per page) + integrated search
        const rowsPerPage = 10;
        let currentPage = 1;

        function getAllRows() {
            return Array.from(document.querySelectorAll('#absensiTableBody tr'));
        }

        function getVisibleRows() {
            return getAllRows().filter(r => r.style.display !== 'none');
        }

        function renderPaginationButtons(totalPages) {
            const container = document.querySelector('.pagination');
            if (!container) return;

            if (totalPages <= 1) {
                container.style.display = 'none';
                return;
            }
            container.style.display = '';

            let html = '';
            html += `<button class="pagination-btn" onclick="changePage('prev')">‹ Sebelumnya</button>`;
            for (let i = 1; i <= totalPages; i++) {
                const activeClass = (i === currentPage) ? ' active' : '';
                html += `<button class="pagination-btn${activeClass}" onclick="changePage(${i})">${i}</button>`;
            }
            html += `<button class="pagination-btn" onclick="changePage('next')">Selanjutnya ›</button>`;
            container.innerHTML = html;
        }

        function showPage(page) {
            const visibleRows = getVisibleRows();
            const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));

            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            currentPage = page;

            visibleRows.forEach((row, idx) => {
                const start = (currentPage - 1) * rowsPerPage;
                const end = currentPage * rowsPerPage;
                if (idx >= start && idx < end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            renderPaginationButtons(totalPages);
        }

        function changePage(page) {
            const visibleRows = getVisibleRows();
            const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));

            if (page === 'prev') {
                showPage(currentPage - 1);
                return;
            }
            if (page === 'next') {
                showPage(currentPage + 1);
                return;
            }
            const target = Number(page);
            if (!isNaN(target)) {
                showPage(target);
            }
        }

        // Initialize on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            const allRows = getAllRows();
            allRows.forEach(r => r.style.display = '');
            currentPage = 1;
            showPage(1);
        });

        // Integrate search with pagination: reset to page 1 after filtering
        const searchInputEl = document.getElementById('searchInput');
        if (searchInputEl) {
            searchInputEl.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const tableRows = getAllRows();
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                currentPage = 1;
                showPage(1);
            });
        }

        // Add fade in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            /* Styling untuk Aksi */
            .action-cell {
                white-space: nowrap;
                text-align: center;
            }
        `;
        document.head.appendChild(style);

        // Loading animation on page load
        window.addEventListener('load', function() {
            const tableRows = document.querySelectorAll('#absensiTableBody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
        
        // Notification functions (dibiarkan seperti semula)
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const isVisible = dropdown.style.display !== 'none';
            
            if (isVisible) {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
                // Add animation
                dropdown.style.opacity = '0';
                dropdown.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    dropdown.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    dropdown.style.opacity = '1';
                    dropdown.style.transform = 'translateY(0)';
                }, 10);
            }
        }

        function markAllAsRead() {
            // ... (fungsi markAllAsRead Anda)
        }

        document.addEventListener('click', function(event) {
            const notificationContainer = document.querySelector('.notification-container');
            const dropdown = document.getElementById('notificationDropdown');
            
            if (dropdown && notificationContainer && !notificationContainer.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        setInterval(() => {
            const bell = document.getElementById('notificationBell');
            const badge = document.getElementById('notificationBadge');
            
            if (badge && badge.style.display !== 'none') {
                bell.style.animation = 'bellShake 0.5s ease-in-out';
                setTimeout(() => {
                    bell.style.animation = '';
                }, 500);
            }
        }, 5000);

        const bellStyle = document.createElement('style');
        bellStyle.textContent += `
            @keyframes bellShake {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(-10deg); }
                75% { transform: rotate(10deg); }
            }
            
            .notification-container:hover .notification-icon {
                transform: scale(1.1);
                transition: transform 0.2s ease;
            }
        `;
        document.head.appendChild(bellStyle);
    </script>
</body>
</html>
