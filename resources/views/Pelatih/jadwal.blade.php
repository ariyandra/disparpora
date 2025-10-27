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
                    <a href="{{ route('absensi.pelatih') }}" class="nav-link">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal.pelatih') }}" class="nav-link active">Jadwal</a>
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
                    <h1 class="page-title">Jadwal</h1>
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
        // Generic client-side pagination + search (10 rows per page)
        (function () {
            const rowsPerPage = 10;
            const tableBodySelector = '#jadwalTableBody';
            const searchInput = document.getElementById('searchInput');

            function getAllRows() { return Array.from(document.querySelectorAll(`${tableBodySelector} tr`)); }
            function getVisibleRows() { return getAllRows().filter(r => r.style.display !== 'none'); }

            function renderPaginationButtons(totalPages) {
                const pagContainer = document.querySelector('.pagination');
                const buttons = Array.from(pagContainer.querySelectorAll('.page-num'));
                buttons.forEach(b => b.remove());
                const nextBtn = pagContainer.querySelector('button[onclick*="next"]');
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.className = 'pagination-btn page-num' + (i === 1 ? ' active' : '');
                    btn.textContent = i;
                    btn.setAttribute('data-page', i);
                    btn.addEventListener('click', () => changePage(i));
                    pagContainer.insertBefore(btn, nextBtn);
                }
            }

            function showPage(page) {
                const visibleRows = getVisibleRows();
                const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
                const p = Math.max(1, Math.min(page, totalPages));
                getAllRows().forEach(r => (r.style.display = 'none'));
                const start = (p - 1) * rowsPerPage;
                visibleRows.slice(start, start + rowsPerPage).forEach(r => { r.style.display = ''; r.style.animation = 'fadeIn 0.25s ease'; });
                document.querySelectorAll('.pagination-btn').forEach(b => b.classList.remove('active'));
                const activeBtn = document.querySelector(`.pagination-btn.page-num[data-page='${p}']`);
                if (activeBtn) activeBtn.classList.add('active');
                const prev = document.querySelector('.pagination button[onclick*="prev"]');
                const next = document.querySelector('.pagination button[onclick*="next"]');
                if (prev) prev.disabled = p <= 1;
                if (next) next.disabled = p >= totalPages;
            }

            function changePage(page) {
                const visibleRows = getVisibleRows();
                const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
                let current = 1;
                const active = document.querySelector('.pagination-btn.page-num.active');
                if (active) current = parseInt(active.getAttribute('data-page')) || 1;
                if (page === 'prev') page = Math.max(1, current - 1);
                else if (page === 'next') page = Math.min(totalPages, current + 1);
                else page = Number(page);
                showPage(page);
            }

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const term = (e.target.value || '').toLowerCase();
                    getAllRows().forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = (!term || text.indexOf(term) !== -1) ? '' : 'none';
                    });
                    const visibleCount = getVisibleRows().length;
                    const totalPages = Math.max(1, Math.ceil(visibleCount / rowsPerPage));
                    renderPaginationButtons(totalPages);
                    showPage(1);
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (!document.getElementById('copilot-fadein-style')) {
                    const style = document.createElement('style');
                    style.id = 'copilot-fadein-style';
                    style.textContent = `@keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }`;
                    document.head.appendChild(style);
                }
                const visibleCount = getVisibleRows().length || getAllRows().length;
                const totalPages = Math.max(1, Math.ceil(visibleCount / rowsPerPage));
                renderPaginationButtons(totalPages);
                showPage(1);
                getAllRows().forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(12px)';
                    setTimeout(() => { row.style.transition = 'all 0.45s ease'; row.style.opacity = '1'; row.style.transform = 'translateY(0)'; }, index * 30);
                });
            });

            window.changePage = changePage;
        })();

        // Preserve notification and bell code below
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
            if (dropdown) {
                const successMsg = document.createElement('div');
                successMsg.innerHTML = '<div style="padding: 12px; text-align: center; color: #4CAF50; font-size: 14px; font-weight: 500;">✓ Semua notifikasi telah dibaca</div>';
                dropdown.appendChild(successMsg);
                setTimeout(() => { successMsg.remove(); dropdown.style.display = 'none'; }, 2000);
            }
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationContainer = document.querySelector('.notification-container');
            const dropdown = document.getElementById('notificationDropdown');
            if (!notificationContainer) return;
            if (!notificationContainer.contains(event.target)) {
                if (dropdown) dropdown.style.display = 'none';
            }
        });

        // Add notification bell animation
        setInterval(() => {
            const bell = document.getElementById('notificationBell');
            const badge = document.getElementById('notificationBadge');
            if (!bell || !badge) return;
            if (badge.style.display !== 'none') {
                bell.style.animation = 'bellShake 0.5s ease-in-out';
                setTimeout(() => { bell.style.animation = ''; }, 500);
            }
        }, 5000);

        // Add bell shake animation CSS
        if (!document.getElementById('copilot-bell-style')) {
            const bellStyle = document.createElement('style');
            bellStyle.id = 'copilot-bell-style';
            bellStyle.textContent = `
                @keyframes bellShake {
                    0%, 100% { transform: rotate(0deg); }
                    25% { transform: rotate(-10deg); }
                    75% { transform: rotate(10deg); }
                }
                .notification-container:hover .notification-icon { transform: scale(1.1); transition: transform 0.2s ease; }
            `;
            document.head.appendChild(bellStyle);
        }
    </script>
</body>
</html>