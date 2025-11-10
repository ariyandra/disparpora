<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - Dinas Pariwisata Pemuda dan Olahraga</title>
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
                    <a href="{{ route('dashboard.pegawai') }}" class="nav-link">Dashboard</a>
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
                    <a href="{{ route('absensi') }}" class="nav-link active">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                @if(auth()->user()->role == 0 || auth()->user()->role == 1)
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link">User</a>
                </li>
                @endif
            </ul>
            
            <a href="{{ route('logout.admin') }}" class="logout-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
            </div>
    </nav>

    <main class="main-content">
        <div class="page-container mt-32 sm:mt-40">
            <div class="page-header">
                <h1 class="page-title">Absensi</h1>
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
                        </tr>
                    </thead>
                    <tbody id="absensiTableBody">
                        @foreach ($dataAbsensi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->atlet->nama ?? 'N/A' }}</td> <!-- Menggunakan ?? untuk pencegahan error relasi -->
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_absen)->toDateString() }}</td>
                                <td>{{ substr($item->jadwal ?? 'N/A', 0, 5) }}</td>  <!-- Menggunakan relasi Jadwal -->
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->keterangan }}</td>
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
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#absensiTableBody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.3s ease';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Pagination functionality (10 rows per page)
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

            // If only one page, hide pagination
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

            // hide all visible rows first
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

            // number
            const target = Number(page);
            if (!isNaN(target)) {
                showPage(target);
            }
        }

        // Initialize pagination on load
        document.addEventListener('DOMContentLoaded', function() {
            // ensure search input listener is set (already above) and then initialize pagination
            // Reset all rows display first (search may have hidden some)
            const allRows = getAllRows();
            allRows.forEach(r => r.style.display = '');
            currentPage = 1;
            showPage(1);
        });

        // Integrate pagination with search: after filtering, reset to page 1
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
                // After filtering, go to first page
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

        

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            <script>
                // Standardized pagination + search for Absensi (matches Atlet behavior)
                const rowsPerPage = 10;
                let currentPage = 1;

                function getAllRows() { return Array.from(document.querySelectorAll('#absensiTableBody tr')); }
                function getVisibleRows() { return getAllRows().filter(r => r.style.display !== 'none'); }

                function renderPaginationButtons(totalPages) {
                    const container = document.querySelector('.pagination');
                    if (!container) return;

                    if (totalPages <= 1) { container.style.display = 'none'; return; }
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
                    if (page === 'prev') { showPage(currentPage - 1); return; }
                    if (page === 'next') { showPage(currentPage + 1); return; }
                    const target = Number(page);
                    if (!isNaN(target)) showPage(target);
                }

                // Initialize
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
                        getAllRows().forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = (!searchTerm || text.indexOf(searchTerm) !== -1) ? '' : 'none';
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

                // Notification helpers (kept from original)
                function toggleNotifications() {
                    const dropdown = document.getElementById('notificationDropdown');
                    if (!dropdown) return;
                    const isVisible = dropdown.style.display !== 'none';
                    if (isVisible) {
                        dropdown.style.display = 'none';
                    } else {
                        dropdown.style.display = 'block';
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
                    const badge = document.getElementById('notificationBadge');
                    const bell = document.getElementById('notificationBell');
                    const items = document.querySelectorAll('.notification-item');
                    if (badge) badge.style.display = 'none';
                    if (bell) bell.textContent = '🔕';
                    items.forEach(item => {
                        const dot = item.querySelector('div > div');
                        if (dot) dot.style.background = '#e0e0e0';
                    });
                    const dropdown = document.getElementById('notificationDropdown');
                    if (!dropdown) return;
                    const successMsg = document.createElement('div');
                    successMsg.innerHTML = '<div style="padding: 12px; text-align: center; color: #4CAF50; font-size: 14px; font-weight: 500;">✓ Semua notifikasi telah dibaca</div>';
                    dropdown.appendChild(successMsg);
                    setTimeout(() => { successMsg.remove(); dropdown.style.display = 'none'; }, 2000);
                }

                // Close notification dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    const notificationContainer = document.querySelector('.notification-container');
                    const dropdown = document.getElementById('notificationDropdown');
                    if (!notificationContainer || !dropdown) return;
                    if (!notificationContainer.contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                });

                // Bell animation
                setInterval(() => {
                    const bell = document.getElementById('notificationBell');
                    const badge = document.getElementById('notificationBadge');
                    if (!bell || !badge) return;
                    if (badge.style.display !== 'none') {
                        bell.style.animation = 'bellShake 0.5s ease-in-out';
                        setTimeout(() => { bell.style.animation = ''; }, 500);
                    }
                }, 5000);

                const bellStyle = document.createElement('style');
                bellStyle.textContent = `
                    @keyframes bellShake { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(-10deg); } 75% { transform: rotate(10deg); } }
                    .notification-container:hover .notification-icon { transform: scale(1.1); transition: transform 0.2s ease; }
                `;
                document.head.appendChild(bellStyle);
            </script>
