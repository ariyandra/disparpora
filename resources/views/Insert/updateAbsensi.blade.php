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
                    <a href="{{ route('absensi.pelatih') }}" class="nav-link active">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                <li class="nav-item">
                    <div class="notification-container" style="position: relative; cursor: pointer;" onclick="toggleNotifications()">
                        <span class="notification-icon" id="notificationBell">🔔</span>
                        <span class="notification-badge" id="notificationBadge" style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold;">3</span>
                        
                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="notificationDropdown" style="position: absolute; top: 100%; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 1000; display: none; margin-top: 10px; border: 1px solid #e0e0e0;">
                            <div style="padding: 16px; border-bottom: 1px solid #f0f0f0;">
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Notifikasi</h3>
                            </div>
                            <div style="max-height: `300px; overflow-y: auto;">
                                <div class="notification-item" style="padding: 12px 16px; border-bottom: 1px solid #f8f8f8; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                    <div style="display: flex; align-items: start; gap: 12px;">
                                        <div style="width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                                        <div style="flex: 1;">
                                            <p style="margin: 0; font-size: 14px; color: #333; font-weight: 500;">Asesmen Baru Ditambahkan</p>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Asesmen untuk atlet John Doe telah berhasil disimpan</p>
                                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #999;">2 menit yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item" style="padding: 12px 16px; border-bottom: 1px solid #f8f8f8; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                    <div style="display: flex; align-items: start; gap: 12px;">
                                        <div style="width: 8px; height: 8px; background: #FF9800; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                                        <div style="flex: 1;">
                                            <p style="margin: 0; font-size: 14px; color: #333; font-weight: 500;">Jadwal Latihan Hari Ini</p>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Latihan sepak bola dimulai pukul 16:00 WIB</p>
                                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #999;">1 jam yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item" style="padding: 12px 16px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                    <div style="display: flex; align-items: start; gap: 12px;">
                                        <div style="width: 8px; height: 8px; background: #2196F3; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                                        <div style="flex: 1;">
                                            <p style="margin: 0; font-size: 14px; color: #333; font-weight: 500;">Absensi Terupdate</p>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">5 atlet telah melakukan absensi hari ini</p>
                                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #999;">3 jam yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 12px 16px; border-top: 1px solid #f0f0f0; text-align: center;">
                                <button onclick="markAllAsRead()" style="background: none; border: none; color: #667eea; font-size: 14px; cursor: pointer; font-weight: 500; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f0f2ff'" onmouseout="this.style.background='none'">
                                    Tandai Semua Dibaca
                                </button>
                            </div>
                        </div>
                    </div>
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
                    <h1 class="page-title">Update Data Absensi</h1>
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
                
                <form action="{{ route('simpan.update.absensi') }}" method="POST" style="margin-top: 30px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; position: relative;">
                        
                        <!-- Decorative Elements -->
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(238, 90, 82, 0.1)); border-radius: 50%; z-index: -1;"></div>

                        <!-- Atlet -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="atlet" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🏅 Atlet <span style="color: #ff6b6b;">*</span>
                            </label>
                            <select id="atlet" 
                                    name="atlet" 
                                    required
                                    style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                                <option value="">Pilih Atlet</option>
                                @foreach ($dataAtlet as $atlet)
                                    <option value="{{ $atlet->id }}">{{ $atlet->nama }}</option>
                                @endforeach
                            </select>


                        <!-- Tanggal -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="tanggal" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📅 Tanggal <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_absen" 
                                   name="tanggal_absen" 
                                   required
                                   value="{{ $absensi->tanggal_absen }}"
                                   value="{{ $absensi->tanggal_absen }}"
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
                                📋 Status <span style="color: #ff6b6b;">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    required
                                    value="{{ $absensi->status }}"
                                    style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                                <option value="">Pilih Status</option>
                                <option value="Hadir" {{ $absensi->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="Tidak Hadir" {{ $absensi->status == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                <option value="Izin" {{ $absensi->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                <option value="Sakit" {{ $absensi->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            </select>

                        <!-- Keterangan -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden; grid-column: 1 / -1;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="keterangan" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📝 Keterangan
                            </label>
                            <textarea id="keterangan" 
                                      name="keterangan" 
                                      rows="4"
                                      value="{{ $absensi->keterangan }}"
                                      style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8); resize: vertical; min-height: 120px;"
                                      placeholder="Masukkan keterangan tambahan untuk absensi ini (opsional). Contoh: Latihan rutin mingguan, persiapan turnamen, dll..."
                                      onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                      onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">{{ $absensi->keterangan }}</textarea>
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
                                style="padding: 12px 24px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);">
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

            // Character counter for keterangan
            const keteranganField = document.getElementById('keterangan');
            const counterDiv = document.createElement('div');
            counterDiv.style.cssText = 'text-align: right; font-size: 12px; color: #666; margin-top: 5px;';
            keteranganField.parentNode.appendChild(counterDiv);
            
            function updateCounter() {
                const length = keteranganField.value.length;
                counterDiv.textContent = `${length} karakter`;
                if (length > 300) {
                    counterDiv.style.color = '#ff6b6b';
                } else if (length > 250) {
                    counterDiv.style.color = '#ffa500';
                } else {
                    counterDiv.style.color = '#666';
                }
            }
            
            keteranganField.addEventListener('input', updateCounter);
            updateCounter();

            // Time validation
            const jamMulaiField = document.getElementById('jam_mulai');
            const jamSelesaiField = document.getElementById('jam_selesai');
            
            function validateTime() {
                const jamMulai = jamMulaiField.value;
                const jamSelesai = jamSelesaiField.value;
                
                if (jamMulai && jamSelesai) {
                    if (jamSelesai <= jamMulai) {
                        jamSelesaiField.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
                        jamSelesaiField.style.borderColor = '#ff6b6b';
                    } else {
                        jamSelesaiField.setCustomValidity('');
                        jamSelesaiField.style.borderColor = '#e0e0e0';
                    }
                }
            }
            
            jamMulaiField.addEventListener('change', validateTime);
            jamSelesaiField.addEventListener('change', validateTime);
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
</body>
</html>