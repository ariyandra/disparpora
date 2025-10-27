<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Absensi - Dinas Pariwisata Pemuda dan Olahraga</title>
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
                    <h1 class="page-title">Tambah Absensi</h1>
                    <a href="{{ route('absensi.pelatih') }}" class="btn-tambah" style="background: linear-gradient(135deg, #6b7280, #4b5563); text-decoration: none;">
                        <span>←</span> Kembali
                    </a>
                </div>
            </div>
            
            <!-- Form Container Card -->
            <div class="table-container-card">
                <div class="table-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <h2 class="table-title">Form Data Absensi</h2>
                </div>
                
                <form action="{{ route('pelatih.simpanAbsensi') }}" method="POST" style="margin-top: 30px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; position: relative;">
                        
                        <!-- Decorative Elements -->
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(238, 90, 82, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        
                        <!-- Nama Atlet -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="nama_atlet" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🏃‍♂️ Nama Atlet <span style="color: #ff6b6b;">*</span>
                            </label>
                            
                            <input list="list_atlet" id="nama_atlet" name="nama_atlet" placeholder="Ketik untuk mencari..." style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);" placeholder="Masukkan nama atlet" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'" required>
                            <datalist id="list_atlet">
                                @foreach ($dataAtlet as $atlet)
                                    <option value="{{ $atlet->nama }}" data-id="{{ $atlet->id }}"></option>
                                @endforeach
                            </datalist>

                            <input type="hidden" id="atlet_id" name="atlet_id">
                        </div>

                        <!-- Tanggal -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="tanggal_absen" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📆 Tanggal <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_absen" 
                                   name="tanggal_absen" 
                                   required
                                   style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                   onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        </div>

                        <!-- Jadwal -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="jadwal" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🕐 Jadwal <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="time" 
                                   id="jadwal" 
                                   name="jadwal" 
                                   required
                                   style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                   onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        </div>

                        <!-- Status -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="status" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                ✅ Status <span style="color: #ff6b6b;">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    required
                                    style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                                <option value="-">Pilih Status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden; grid-column: 1 / -1;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="keterangan" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📝 Keterangan
                            </label>
                            <textarea id="keterangan" 
                                      name="keterangan" 
                                      required
                                      rows="4"
                                      style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8); resize: vertical; min-height: 100px;"
                                      placeholder="Masukkan keterangan tambahan (opsional)"
                                      onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                      onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 40px; padding-top: 30px; border-top: 2px solid #f0f0f0; position: relative;">
                        <div style="position: absolute; top: -1px; left: 50%; transform: translateX(-50%); width: 60px; height: 2px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 1px;"></div>
                        <a href="{{ route('absensi.pelatih') }}" 
                           style="padding: 12px 24px; border: 2px solid #e0e0e0; background: white; border-radius: 25px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: #666; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"
                           onmouseover="this.style.borderColor='#999'; this.style.color='#333'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.15)'"
                           onmouseout="this.style.borderColor='#e0e0e0'; this.style.color='#666'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)'">
                            <span>✕</span> Batal
                        </a>
                        <button type="submit" 
                                class="btn-tambah" 
                                id="submitBtn"
                                style="padding: 12px 24px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 25px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(102, 126, 234, 0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(102, 126, 234, 0.3)'">
                            <span>💾</span> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Form submission
        document.getElementById('absensiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            
            // Simulate form submission
            setTimeout(() => {
                alert('Data absensi berhasil disimpan!');
                window.location.href = '';
            }, 1500);
        });

        // Enhanced form interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects to form fields
            const formFields = document.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                const indicator = field.closest('div').querySelector('.field-indicator');
                
                field.addEventListener('focus', function() {
                    if (indicator) {
                        indicator.style.opacity = '1';
                    }
                });
                
                field.addEventListener('blur', function() {
                    if (indicator) {
                        indicator.style.opacity = '0';
                    }
                });
            });

            // Animate form fields on load
            const fieldContainers = document.querySelectorAll('form > div > div');
            fieldContainers.forEach((container, index) => {
                container.style.opacity = '0';
                container.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    container.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Set default tanggal to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal').value = today;

            // Add loading animation styles
            const style = document.createElement('style');
            style.textContent = `
                .loading {
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    border: 2px solid rgba(255,255,255,0.3);
                    border-radius: 50%;
                    border-top-color: #fff;
                    animation: spin 0.8s ease-in-out infinite;
                }
                
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
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
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputAtlet = document.getElementById("nama_atlet");
            const hiddenId   = document.getElementById("atlet_id");
            const dataList   = document.getElementById("list_atlet");

            inputAtlet.addEventListener("input", function() {
                const val = this.value;
                hiddenId.value = ""; // reset dulu

                // Cari option yang match dengan input user
                const opts = dataList.querySelectorAll("option");
                opts.forEach(opt => {
                    if (opt.value === val) {
                        hiddenId.value = opt.dataset.id; // ambil ID sesuai nama
                    }
                });
            });
        });
    </script>


</body>
</html>