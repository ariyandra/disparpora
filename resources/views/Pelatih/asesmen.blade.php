<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesmen - Dinas Pariwisata Pemuda dan Olahraga</title>
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
                    <a href="{{ route('asesmen.pelatih') }}" class="nav-link active">Asesmen</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi.pelatih') }}" class="nav-link">Absensi</a>
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
        <div class="page-container mt-32 sm:mt-40">
            <!-- Page Header Card -->
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Asesmen</h1>
                    <a href="{{ route('tambah.asesmen') }}" class="btn-tambah" style="text-decoration: none;">
                        <span>➕</span> Tambah Asesmen
                    </a>
                </div>
            </div>
            
            <div id="rekapPrintable" class="table-container-card mb-6">
                <div class="table-header">
                    <h2 class="table-title">Rekapitulasi Asesmen</h2>
                </div>
                <form method="GET" action="{{ route('asesmen.pelatih') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Atlet</label>
                        <select name="atlet_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Semua</option>
                            @isset($atlets)
                                @foreach($atlets as $a)
                                    <option value="{{ $a->id }}" {{ ($filters['atlet_id'] ?? '') == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="md:col-span-4 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Terapkan</button>
                        <a href="{{ route('pelatih.asesmen.export.csv', request()->query()) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">Export CSV</a>
                        <button type="button" onclick="printRekap()" class="px-4 py-2 bg-gray-700 text-white rounded-lg">Print / PDF</button>
                    </div>
                </form>
                <div class="overflow-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Atlet</th>
                                <th>Jumlah</th>
                                <th>Rata2 Fisik</th>
                                <th>Rata2 Teknik</th>
                                <th>Rata2 Sikap</th>
                                <th>Terakhir Asesmen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($rekap)
                                @forelse($rekap as $r)
                                <tr>
                                    <td>{{ $r['atlet_nama'] }}</td>
                                    <td>{{ $r['jumlah'] }}</td>
                                    <td>{{ $r['avg_fisik'] }}</td>
                                    <td>{{ $r['avg_teknik'] }}</td>
                                    <td>{{ $r['avg_sikap'] }}</td>
                                    <td>{{ $r['last_asesmen'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-sm text-gray-500">Tidak ada data rekap.</td>
                                </tr>
                                @endforelse
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Table Container Card -->
            <div class="table-container-card">
                <div class="table-header">
                    <h2 class="table-title">Daftar Asesmen</h2>
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Cari asesmen..." id="searchInput">
                    </div>
                </div>
                
                <table class="data-table" id="asesmenTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Atlet</th>
                            <th>Tanggal</th>
                            <th>Aspek Fisik</th>
                            <th>Aspek Teknik</th>
                            <th>Aspek Sikap</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="asesmenTableBody">
                        @foreach ($dataAsesmen as $asesmen)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $asesmen->atlet->nama }}</td>
                            <td>{{ $asesmen->tanggal_asesmen }}</td>
                            <td>{{ $asesmen->aspek_fisik }}</td>
                            <td>{{ $asesmen->aspek_teknik }}</td>
                            <td>{{ $asesmen->aspek_sikap }}</td>
                            <td>{{ $asesmen->keterangan }}</td>
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
            const tableBodySelector = '#asesmenTableBody';
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

        // Action functions
        function tambahASesmen() {
            alert('Fitur Tambah Asesmen akan segera tersedia!');
        }

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
            
            // Hide badge
            badge.style.display = 'none';
            
            // Change bell to read state
            bell.textContent = '🔕';
            
            // Mark all items as read (remove colored dots)
            items.forEach(item => {
                const dot = item.querySelector('div > div');
                if (dot) {
                    dot.style.background = '#e0e0e0';
                }
            });
            
            // Show success message
            const dropdown = document.getElementById('notificationDropdown');
            const successMsg = document.createElement('div');
            successMsg.innerHTML = '<div style="padding: 12px; text-align: center; color: #4CAF50; font-size: 14px; font-weight: 500;">✓ Semua notifikasi telah dibaca</div>';
            dropdown.appendChild(successMsg);
            
            setTimeout(() => {
                successMsg.remove();
                dropdown.style.display = 'none';
            }, 2000);
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationContainer = document.querySelector('.notification-container');
            const dropdown = document.getElementById('notificationDropdown');
            
            if (!notificationContainer.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Add notification bell animation
        setInterval(() => {
            const bell = document.getElementById('notificationBell');
            const badge = document.getElementById('notificationBadge');
            
            if (badge.style.display !== 'none') {
                bell.style.animation = 'bellShake 0.5s ease-in-out';
                setTimeout(() => {
                    bell.style.animation = '';
                }, 500);
            }
        }, 5000);

        // Add bell shake animation CSS
        const bellStyle = document.createElement('style');
        bellStyle.textContent = `
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

        // Print only the rekap table (without filters, Atlet select or action buttons)
        function printRekap(){
            const rekapEl = document.getElementById('rekapPrintable');
            if(!rekapEl){
                alert('Bagian rekap tidak ditemukan.');
                return;
            }
            const rekapTable = rekapEl.querySelector('table.data-table');
            if(!rekapTable){
                alert('Tabel rekap tidak ditemukan.');
                return;
            }

            const startInput = document.querySelector('input[name="start_date"]');
            const endInput = document.querySelector('input[name="end_date"]');
            const startVal = startInput ? startInput.value : '';
            const endVal = endInput ? endInput.value : '';

            const win = window.open('', '_blank', 'toolbar=0,location=0,menubar=0');
            const cssLinks = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l=>l.href);
            let head = '<meta charset="utf-8"><title>Rekap Asesmen</title>';
            cssLinks.forEach(href=>{ head += '<link rel="stylesheet" href="'+href+'">'; });
            head += '<style>@page{size:A4;margin:8mm;}body{font-family:Arial,Helvetica,sans-serif;margin:6px;padding:0} h1{font-size:18px;margin-bottom:6px} h2{font-size:16px;margin:6px 0 10px 0} .meta{font-size:12px;margin-bottom:10px} table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:6px 8px;text-align:left;font-size:12px} thead th{background:#f3f6ff;color:#222;font-weight:700} /* scale to appear closer */ .print-scale{transform:scale(1.15);transform-origin:top left;width:calc(100% / 1.15);} </style>';

            // Build a richer header similar to the provided banner.
            // The image file you mentioned will be used: /public/models/download (1).jpg
            // URL-encode the filename portion so browsers request it correctly.
            const logoUrl = window.location.origin + encodeURI('/models/download (1).jpg');
            const headerHtml = `
                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:6px;position:relative;">
                    <img src="${logoUrl}" onerror="this.style.display='none'" style="height:64px;object-fit:contain;margin-top:0" />
                    <div style="flex:1;text-align:center;padding-top:6px;">
                        <div style="font-size:12px;font-weight:600;letter-spacing:0.5px;">PEMERINTAH KABUPATEN TANAH DATAR</div>
                        <div style="font-size:18px;font-weight:800;letter-spacing:0.8px;">DINAS PARIWISATA, PEMUDA DAN OLAH RAGA</div>
                        <div style="font-size:11px;">Komplek Benteng Van Der Capellen - Telepon (0752) 574821, 574364 &nbsp; Faks (0752) 574821</div>
                        <div style="font-size:11px;">BATUSANGKAR</div>
                    </div>
                </div>
                <hr style="border:none;border-top:2px solid #000;margin:8px 0 12px 0">
            `;
            const titleHtml = '<h2 style="margin:6px 0 10px 0;font-size:18px;">Rekapitulasi Asesmen</h2>';
            const metaHtml = '<div class="meta">' + (startVal ? ('Tanggal Mulai: ' + startVal) : '') + (startVal && endVal ? ' &nbsp;|&nbsp; ' : '') + (endVal ? ('Tanggal Selesai: ' + endVal) : '') + '</div>';
            const tableHtml = rekapTable.outerHTML;

            const html = '<html><head>'+head+'</head><body><div class="print-scale">' + headerHtml + titleHtml + metaHtml + tableHtml + '</div></body></html>';
            win.document.open();
            win.document.write(html);
            win.document.close();
            win.onload = function(){
                setTimeout(()=>{ win.focus(); win.print(); win.close(); }, 300);
            };
        }
    </script>
</body>
</html>