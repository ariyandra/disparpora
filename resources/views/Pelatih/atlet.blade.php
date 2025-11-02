<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atlet - Dinas Pariwisata Pemuda dan Olahraga</title>
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
                    <a href="{{ route('atlet.pelatih') }}" class="nav-link active">Atlet</a>
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
                    <a href="{{ route('jadwal.pelatih') }}" class="nav-link">Jadwal</a>
                </li>
                <li>
                    <a href="{{ route('logout.admin') }}" class="relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </a>
                </li>
            </ul>
            
        </div>
    </nav>

    <main class="main-content">
        <div class="page-container mt-32 sm:mt-40">
            <!-- Page Header Card -->
            <div class="page-header-card bg-white sticky top-16 z-10 shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center p-4">
                    <h1 class="page-title text-xl sm:text-2xl font-bold">Atlet</h1>
                </div>
            </div>

            <!-- Inline import box (hidden by default) -->
            <div id="importBox" style="display:none;padding:12px;border:1px dashed #e5e7eb;margin-bottom:16px;border-radius:8px;">
                @if(session('error'))
                    <div style="color:red;margin-bottom:8px;">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div style="color:green;margin-bottom:8px;">{{ session('success') }}</div>
                @endif
                <form id="importForm" action="{{ route('import.atlet.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                        <input type="file" name="file" accept=".xlsx,.xls" required>
                        <button id="importSubmitBtn" type="submit" style="padding:8px 14px;background:#10b981;color:#fff;border-radius:8px;border:none;">Upload & Import</button>
                        <button type="button" id="importCancelBtn" style="padding:8px 12px;background:#ef4444;color:#fff;border-radius:8px;border:none;">Batal</button>
                        <small style="color:#666;">Format: nama, email, password, jenis_kelamin, no_telp, cabor (id), tanggal_lahir (YYYY-MM-DD), tanggal_gabung, status</small>
                    </div>
                </form>
            </div>
            
            <!-- Table Container Card -->
            <div class="table-container-card">
                <div class="table-header">
                    <h2 class="table-title">Daftar Atlet</h2>
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Cari atlet..." id="searchInput">
                    </div>
                </div>
                
                <table class="data-table" id="atletTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Foto</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>No HP</th>
                            <th>Cabor</th>
                            <th>Tanggal Lahir</th>
                            <th>Tanggal Gabung</th>
                        </tr>
                    </thead>
                    <tbody id="atletTableBody">
                        @foreach ($dataAtlet as $atlet)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $atlet->nama }}</td>
                                <td>
                                    @if(!empty($atlet->foto))
                                        <img src="{{ asset('storage/' . $atlet->foto) }}" alt="Foto {{ $atlet->nama }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        <span>❓</span>
                                    @endif
                                </td>
                                <td>{{ $atlet->email }}</td>
                                <td>{{ $atlet->jenis_kelamin }}</td>
                                <td>{{ $atlet->no_telp }}</td>
                                <td>{{ $atlet->cabor->nama_cabor }}</td>
                                <td>{{ $atlet->tanggal_lahir }}</td>
                                <td>{{ $atlet->tanggal_gabung }}</td>
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
            const tableBodySelector = '#atletTableBody';
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
            const tableRows = document.querySelectorAll('#atletTableBody tr');
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

        // Import Excel toggle and submit handling
        document.addEventListener('DOMContentLoaded', function(){
            const toggle = document.getElementById('importToggleBtn');
            const box = document.getElementById('importBox');
            const cancel = document.getElementById('importCancelBtn');
            const importForm = document.getElementById('importForm');
            const importBtn = document.getElementById('importSubmitBtn');

            if(toggle && box){
                toggle.addEventListener('click', function(){
                    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
                });
            }
            if(cancel && box){
                cancel.addEventListener('click', function(){ box.style.display = 'none'; });
            }
            if(importForm && importBtn){
                importForm.addEventListener('submit', function(){
                    importBtn.disabled = true;
                    importBtn.textContent = 'Mengunggah...';
                });
            }
        });

        // Lightbox for athlete photos
        (function(){
            // create modal elements
            const modal = document.createElement('div');
            modal.id = 'photoModal';
            modal.style.display = 'none';
            modal.style.position = 'fixed';
            modal.style.left = '0';
            modal.style.top = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.background = 'rgba(0,0,0,0.75)';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '9999';
            modal.style.padding = '20px';
            modal.innerHTML = '<div id="photoModalInner" style="max-width:90%;max-height:90%;display:flex;align-items:center;justify-content:center;"><img id="photoModalImg" src="" alt="" style="max-width:100%;max-height:100%;border-radius:8px;box-shadow:0 8px 30px rgba(0,0,0,0.5)"></div>';
            document.body.appendChild(modal);

            function openModal(src, alt){
                const img = document.getElementById('photoModalImg');
                img.src = src;
                img.alt = alt || '';
                modal.style.display = 'flex';
            }

            function closeModal(){
                modal.style.display = 'none';
                const img = document.getElementById('photoModalImg');
                img.src = '';
            }

            // delegate click on images inside the table
            document.addEventListener('click', function(e){
                const target = e.target;
                if(target.tagName === 'IMG' && target.closest('#atletTableBody')){
                    // open modal
                    openModal(target.src, target.alt);
                }
                // close when clicking outside image
                if(e.target === modal){
                    closeModal();
                }
            });

            // close on Escape
            document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeModal(); });
        })();
    </script>
</body>
</html>