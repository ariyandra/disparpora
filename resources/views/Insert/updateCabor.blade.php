<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Cabang Olahraga - Dinas Pariwisata Pemuda dan Olahraga</title>
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
                    <a href="{{ route('cabor') }}" class="nav-link active">Cabor</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('lapangan') }}" class="nav-link">Lapangan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('asesmen') }}" class="nav-link">Asesmen</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi') }}" class="nav-link">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link">User</a>
                </li>
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
            <!-- Page Header Card -->
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Ubah Cabang Olahraga</h1>
                    <a href="{{ route('cabor') }}" class="btn-tambah" style="background: linear-gradient(135deg, #6b7280, #4b5563); text-decoration: none;">
                        <span>←</span> Kembali
                    </a>
                </div>
            </div>
            
            <!-- Form Container Card -->
            <div class="table-container-card">
                <div class="table-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <h2 class="table-title">Form Ubah Data Cabang Olahraga</h2>
                </div>
                
                <form action="{{ route('simpan.ubah.cabor') }}" method="POST" style="margin-top: 30px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; position: relative;">
                        
                        <!-- Decorative Elements -->
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(238, 90, 82, 0.1)); border-radius: 50%; z-index: -1;"></div>

                        <!-- Nama Cabang Olahraga -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="nama_cabor" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🏆 Nama Cabang Olahraga <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="hidden" name="id_cabor" value="{{ $dataCabor->id }}">
                            <input type="text" 
                                   id="nama_cabor" 
                                   name="nama_cabor" 
                                   required
                                   value="{{ $dataCabor->nama_cabor }}"
                                   style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                   placeholder="Contoh: Sepak Bola, Basket, Voli, dll"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                   onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        </div>

                        <!-- Deskripsi -->
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden; grid-column: 1 / -1;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="deskripsi" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📝 Deskripsi <span style="color: #ff6b6b;">*</span>
                            </label>
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      required
                                      rows="4"
                                      style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8); resize: vertical; min-height: 120px;"
                                      placeholder="Masukkan deskripsi lengkap cabang olahraga ini, termasuk aturan dasar, peralatan yang dibutuhkan, dan informasi penting lainnya..."
                                      onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                      onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">{{ $dataCabor->deskripsi }}</textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 40px; padding-top: 30px; border-top: 2px solid #f0f0f0; position: relative;">
                        <div style="position: absolute; top: -1px; left: 50%; transform: translateX(-50%); width: 60px; height: 2px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 1px;"></div>
                        <a href="{{ route('cabor') }}" 
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
        document.getElementById('caborForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                alert('Data cabang olahraga berhasil disimpan!');
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
                }, index * 150);
            });

            // Auto-generate number (optional - you can remove this if you want manual input)
            const noField = document.getElementById('no');
            if (!noField.value) {
                // Generate next number based on existing data or use a default
                const nextNumber = Math.floor(Math.random() * 100) + 1; // Replace with actual logic
                noField.value = nextNumber;
            }

            // Character counter for description
            const deskripsiField = document.getElementById('deskripsi');
            const counterDiv = document.createElement('div');
            counterDiv.style.cssText = 'text-align: right; font-size: 12px; color: #666; margin-top: 5px;';
            deskripsiField.parentNode.appendChild(counterDiv);
            
            function updateCounter() {
                const length = deskripsiField.value.length;
                counterDiv.textContent = `${length} karakter`;
                if (length > 500) {
                    counterDiv.style.color = '#ff6b6b';
                } else if (length > 400) {
                    counterDiv.style.color = '#ffa500';
                } else {
                    counterDiv.style.color = '#666';
                }
            }
            
            deskripsiField.addEventListener('input', updateCounter);
            updateCounter();
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