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
    <style>
        /* Ensure full page prints (override any global "print only #printable" rules) */
        @media print {
            /* make all elements visible for this page */
            body * { visibility: visible !important; }
            /* keep layout full width */
            html, body { height: auto !important; }
            /* hide elements explicitly marked no-print */
            .no-print { display: none !important; }
            /* prevent fixed navbar from covering content in print */
            .navbar { position: static !important; }
        }
    </style>
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
        <div class="page-container mt-32 sm:mt-40">
            <!-- Page Header Card -->
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Absensi</h1>
                    <a href="{{ route('pelatih.isiAbsensi') }}" class="btn-tambah" style="text-decoration: none;">
                        <span>➕</span> Tambah Absensi
                    </a>
                </div>
            </div>
            
                <div id="rekapPrintable" class="table-container-card mb-6">
                <div class="table-header">
                    <h2 class="table-title">Rekapitulasi Absensi</h2>
                </div>
                <form method="GET" action="{{ route('absensi.pelatih') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
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
                        <a href="{{ route('pelatih.absensi.export.csv', request()->query()) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">Export CSV</a>
                        <button type="button" onclick="printRekap()" class="px-4 py-2 bg-gray-700 text-white rounded-lg">Print / PDF</button>
                    </div>
                </form>
                <div class="overflow-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Atlet</th>
                                <th>Pertemuan</th>
                                <th>Hadir</th>
                                <th>Sakit</th>
                                <th>Izin</th>
                                <th>Alpa</th>
                                <th>%Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($rekap)
                                @forelse($rekap as $r)
                                <tr>
                                    <td>{{ $r['atlet_nama'] }}</td>
                                    <td>{{ $r['total'] }}</td>
                                    <td>{{ $r['hadir'] }}</td>
                                    <td>{{ $r['sakit'] }}</td>
                                    <td>{{ $r['izin'] }}</td>
                                    <td>{{ $r['alpa'] }}</td>
                                    <td>{{ isset($r['persen_hadir']) ? $r['persen_hadir'].'%' : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-sm text-gray-500">Tidak ada data rekap.</td>
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
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_absen)->toDateString() }}</td>
                                <td>{{ substr($item->jadwal ?? 'N/A', 0, 5) }}</td>  <!-- Menggunakan relasi Jadwal -->
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="action-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('pelatih.ubahAbsensi', ['id' => $item->id]) }}" class="btn-action btn-edit" style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;text-decoration:none;">
                                            <span>✏️</span> Edit
                                        </a>
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

        // Print only the rekap table (without filters, Atlet select or action buttons)
        function printRekap(){
            const rekapEl = document.getElementById('rekapPrintable');
            if(!rekapEl){
                alert('Bagian rekap tidak ditemukan.');
                return;
            }
            // find the rekap table inside the container (first .data-table)
            const rekapTable = rekapEl.querySelector('table.data-table');
            if(!rekapTable){
                alert('Tabel rekap tidak ditemukan.');
                return;
            }

            // read date filters to show in header (if present)
            const startInput = document.querySelector('input[name="start_date"]');
            const endInput = document.querySelector('input[name="end_date"]');
            const startVal = startInput ? startInput.value : '';
            const endVal = endInput ? endInput.value : '';

            const win = window.open('', '_blank', 'toolbar=0,location=0,menubar=0');
            const cssLinks = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l=>l.href);
            let head = '<meta charset="utf-8"><title>Rekap Absensi</title>';
            // include same stylesheets (if accessible)
            cssLinks.forEach(href=>{ head += '<link rel="stylesheet" href="'+href+'">'; });
            // minimal print styles to make table readable
            head += '<style>@page{size:A4;margin:12mm;}body{font-family:Arial,Helvetica,sans-serif;margin:6px 10px;padding:0;color:#111} h1{font-size:18px;margin-bottom:6px} h2{font-size:18px;margin:4px 0 12px 0;font-weight:700} .meta{font-size:12px;margin:4px 0 14px 0} table{border-collapse:collapse;width:100%;table-layout:fixed} th,td{border:1px solid #d5d9e2;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top} thead th{background:#f0f3f9;color:#222;font-weight:700} .letterhead{display:flex;align-items:flex-start;gap:14px} .letterhead img{height:82px} .lh-text{flex:1;text-align:center} .gov{font-size:12px;font-weight:600;letter-spacing:0.4px} .dept{font-size:20px;font-weight:900;letter-spacing:0.8px} .addr{font-size:11px;margin-top:2px;line-height:1.3} .city{font-size:11px} .contact-row{display:flex;justify-content:center;gap:18px;margin-top:6px;font-size:11px} .contact-row .sep{width:2px;background:#000;height:14px;display:inline-block;align-self:center} .divider-thick{border-top:3px solid #000;margin:6px 0 2px 0} .divider-thin{border-top:1px solid #000;margin:0 0 12px 0} </style>';

            // Build printable HTML: department header + title + date range + the table only
            // Build a richer header similar to the provided banner.
            // Use a public asset path for the letterhead logo. Place your file at public/images/logo-surat.jpg
            // You can change the file name below if needed.
            const logoUrl = "{{ asset('images/logo-surat.jpg') }}";
            const headerHtml = `
                <div class="letterhead">
                    <img id="print-logo" src="${logoUrl}" alt="Logo" onerror="this.style.display='none'" style="object-fit:contain;" />
                    <div class="lh-text" style="padding-top:1px;">
                        <div class="gov">PEMERINTAH KABUPATEN TANAH DATAR</div>
                        <div class="dept">DINAS PARIWISATA, PEMUDA DAN OLAH RAGA</div>
                        <div class="addr">Komplek Benteng Van Der Capellen - Telepon (0752) 574821, 574364 &nbsp; Faks (0752) 574821</div>
                        <div class="city">BATUSANGKAR</div>
                        <div class="contact-row"><span>Website: www.tanahdatar.go.id</span><span class="sep"></span><span>Email: disparpora@tanahdatar.go.id</span></div>
                    </div>
                </div>
                <div class="divider-thick"></div>
                <div class="divider-thin"></div>
            `;
            const titleHtml = '<h2 style="margin:6px 0 10px 0;font-size:18px;">Rekapitulasi Absensi</h2>';
            const metaHtml = '<div class="meta">' + (startVal ? ('Tanggal Mulai: ' + startVal) : '') + (startVal && endVal ? ' &nbsp;|&nbsp; ' : '') + (endVal ? ('Tanggal Selesai: ' + endVal) : '') + '</div>';
            const tableHtml = rekapTable.outerHTML;

            const html = '<html><head>'+head+'</head><body>' + headerHtml + titleHtml + metaHtml + tableHtml +
            '<script>\n' +
            'const logoImg=document.getElementById("print-logo");\n' +
            'function doPrint(){setTimeout(function(){window.focus();window.print();window.close();},120);}\n' +
            'if(logoImg){ if(logoImg.complete){ doPrint(); } else { logoImg.onload=doPrint; logoImg.onerror=doPrint; } } else { doPrint(); }\n' +
            '<\/script></body></html>';
            win.document.open();
            win.document.write(html);
            win.document.close();
            // wait a bit then print
            // printing now waits for image load handled inside injected script
            win.onload = function(){};
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
