<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - Dinas Pariwisata Pemuda dan Olahraga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    <a href="{{ route('dashboard.atlet') }}" class="nav-link">Dashboard</a>
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
                    <a href="{{ route('atlet.jadwal') }}" class="nav-link active">Jadwal</a>
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

    <main class="main-content">
        <div class="page-container">
            <!-- Page Header Card -->
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Jadwal Olahraga</h1>
                </div>
            </div>
            
            <!-- Table Container Card -->
            <div class="table-container-card">
                <div class="table-header">
                    <h2 class="table-title">Daftar Jadwal</h2>
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Cari jadwal..." id="searchInput">
                    </div>
                </div>
                
                <table class="data-table" id="jadwalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Cabang Olahraga</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="jadwalTableBody">
                        @foreach ($dataJadwal as $jadwal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->cabor->nama_cabor }}</td>
                                <td>{{ $jadwal->lapangan->nama_lapangan }}</td>
                                <td>{{ $jadwal->tanggal }}</td>
                                <td>{{ $jadwal->jam_mulai }}</td>
                                <td>{{ $jadwal->jam_selesai }}</td>
                                <td>{{ $jadwal->keterangan }}</td>
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
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            <script>
                // Generic client-side pagination + search for any .data-table (10 rows/page)
                const rowsPerPage = 10;
                let currentPage = 1;

                function getAllRows() {
                    const tbody = document.querySelector('.data-table tbody');
                    return tbody ? Array.from(tbody.querySelectorAll('tr')) : [];
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
                        row.style.display = (idx >= start && idx < end) ? '' : 'none';
                    });

                    renderPaginationButtons(totalPages);
                }

                function changePage(page) {
                    const visibleRows = getVisibleRows();
                    const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
                    if (page === 'prev') { showPage(currentPage - 1); return; }
                    if (page === 'next') { showPage(currentPage + 1); return; }
                    const target = Number(page);
                    if (!isNaN(target)) showPage(target);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    // reset rows and show first page
                    getAllRows().forEach(r => r.style.display = '');
                    currentPage = 1;
                    showPage(1);

                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.addEventListener('input', function(e) {
                            const term = e.target.value.toLowerCase();
                            getAllRows().forEach(row => {
                                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                            });
                            currentPage = 1;
                            showPage(1);
                        });
                    }
                });
            </script>
            }
    </script>
</body>
</html>