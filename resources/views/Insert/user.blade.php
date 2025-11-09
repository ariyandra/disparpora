<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Dinas Pariwisata Pemuda dan Olahraga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/pelatih.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pelatih-custom.css') }}" rel="stylesheet">
    <style>
        /* CSS Tambahan untuk animasi loading */
        @keyframes spinner {
            to {transform: rotate(360deg);}
        }
        .loading {
            display: inline-block;
            width: 1em;
            height: 1em;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spinner 0.6s linear infinite;
        }
        /* Style untuk dropdown yang dinonaktifkan sementara */
        #kecamatanField[style*="display:none"] select,
        #nagariField[style*="display:none"] select {
            pointer-events: none !important;
            opacity: 0.6 !important;
        }

        /* PERBAIKAN: Hapus Hotfix CSS sebelumnya karena akan ditangani oleh JS */
        /* Pastikan elemen select selalu punya z-index tinggi saat aktif (akan disetel di JS) */
        #kecamatan_id, #nagari_id {
            /* Pastikan style bawaan tidak menonaktifkan pointer events */
            pointer-events: auto;
            position: relative;
            z-index: 10;
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
                    <a href="{{ route('absensi') }}" class="nav-link">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal') }}" class="nav-link">Jadwal</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link active">User</a>
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
            <div class="page-header-card">
                <div class="flex justify-between items-center">
                    <h1 class="page-title">Tambah User</h1>
                    <a href="{{ route('user') }}" class="btn-tambah" style="background: linear-gradient(135deg, #6b7280, #4b5563); text-decoration: none;">
                        <span>←</span> Kembali
                    </a>
                </div>
            </div>
            
            <div class="table-container-card">
                <div class="table-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <h2 class="table-title">Form Data User</h2>
                </div>
                
                <form id="userForm" action="{{ route('simpanUser') }}" method="POST" style="margin-top: 30px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; position: relative;">
                        
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(238, 90, 82, 0.1)); border-radius: 50%; z-index: -1;"></div>
                        
                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="nama" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                👤 Nama <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="text" 
                                   id="nama" 
                                   name="nama" 
                                   required
                                   style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                   placeholder="Masukkan nama lengkap"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                   onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        </div>

                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="email" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                📧 Email <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required
                                   style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                   placeholder="contoh@email.com"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                   onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        </div>

                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="password" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🔒 Password <span style="color: #ff6b6b;">*</span>
                            </label>
                            <div style="position: relative;">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="8"
                                       style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8); width: 100%; padding-right: 45px;"
                                       placeholder="Minimal 8 karakter"
                                       onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                                <button type="button" 
                                        id="togglePassword"
                                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px; color: #666;"
                                        onclick="togglePasswordVisibility()">
                                    👁️
                                </button>
                            </div>
                            <div id="passwordStrength" style="margin-top: 5px; font-size: 12px; color: #666;"></div>
                        </div>

                        <div id="kecamatanField" style="display:none; display: flex; flex-direction: column;">
                            <label for="kecamatan_id" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🗺️ Kecamatan
                            </label>
                            <select id="kecamatan_id" name="kecamatan_id" style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);" disabled>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan ?? $kec->name ?? 'Kecamatan '.$kec->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="nagariField" style="display:none; display: flex; flex-direction: column;">
                            <label for="nagari_id" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                🏘️ Nagari
                            </label>
                            <select id="nagari_id" name="nagari_id" style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);" disabled>
                                <option value="">Pilih Nagari</option>
                                @foreach($nagaris as $n)
                                    @php
                                        // resolve parent kecamatan id if available
                                        $parentId = $n->kecamatan_id ?? ($n->kecamatan->id ?? null);
                                        $nagName = $n->nama_nagari ?? $n->name ?? 'Nagari '.($n->id ?? '');
                                    @endphp
                                    <option value="{{ $n->id ?? $n->name ?? $n->nama ?? $nagName }}" data-kecamatan="{{ $parentId }}">{{ $nagName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px; opacity: 0; transition: opacity 0.3s ease;" class="field-indicator"></div>
                            <label for="role" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                👥 Role <span style="color: #ff6b6b;">*</span>
                            </label>
                            <select id="role" 
                                    name="role" 
                                    required
                                    style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 14px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.8);"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                                <option value="">Pilih Role</option>
                                <option value=0>Admin</option>
                                <option value=1>Pegawai</option>
                                <option value=2>Kecamatan</option>
                                <option value=3>Nagari</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 40px; padding-top: 30px; border-top: 2px solid #f0f0f0; position: relative;">
                        <div style="position: absolute; top: -1px; left: 50%; transform: translateX(-50%); width: 60px; height: 2px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 1px;"></div>
                        <a href="{{ route('user') }}"
                           style="padding: 12px 24px; border: 2px solid #e0e0e0; background: white; border-radius: 25px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: #666; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"
                           onmouseover="this.style.borderColor='#999'; this.style.color='#333'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.15)'"
                           onmouseout="this.style.borderColor='#e0e0e0'; this.style.color='#666'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)'">
                            <span>✕</span> Batal
                        </a>
            <button id="submitBtn" type="submit" 
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
        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthDiv = document.getElementById('passwordStrength');
            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength++;
            else feedback.push('minimal 8 karakter');

            if (/[a-z]/.test(password)) strength++;
            else feedback.push('huruf kecil');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('huruf besar');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('angka');

            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('karakter khusus');

            const strengthLevels = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
            const strengthColors = ['#ff6b6b', '#ffa500', '#ffeb3b', '#4caf50', '#2e7d32'];

            if (password.length === 0) {
                strengthDiv.textContent = '';
                return;
            }

            strengthDiv.innerHTML = `
                <span style="color: ${strengthColors[strength - 1] || '#ff6b6b'};">
                    Password: ${strengthLevels[strength - 1] || 'Sangat Lemah'}
                </span>
                ${feedback.length > 0 ? `<br><span style="color: #666; font-size: 11px;">Perlu: ${feedback.join(', ')}</span>` : ''}
            `;
        }

        // Form submission (guarded)
        (function(){
            const userForm = document.getElementById('userForm');
            if (!userForm) return;
            userForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');

                // Show loading state and disable button
                if (submitBtn) {
                    try { submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...'; } catch(e){}
                    try { submitBtn.disabled = true; } catch(e){}
                }
                // allow the form to submit normally to the server
            });
        })();

        // Enhanced form interactions and core logic
        document.addEventListener('DOMContentLoaded', function() {
            console.debug('user.blade script loaded');

            // Get the select elements
            const roleField = document.getElementById('role');
            const kecField = document.getElementById('kecamatanField');
            const nagariField = document.getElementById('nagariField');
            const kecSelect = document.getElementById('kecamatan_id');
            const nagSelect = document.getElementById('nagari_id');

            // Store original Nagari options for client-side filtering fallback
            const originalNagOptions = nagSelect ? Array.from(nagSelect.options).map(o => ({ value: o.value, text: o.text, parent: o.getAttribute('data-kecamatan') || '' })) : [];
            let nagariPopulatedByAjaxFor = null;

            // Dependent dropdown logic: filter nagari when kecamatan changes
            async function filterNagariByKecamatan(kecValue){
                console.debug('filterNagariByKecamatan called', { kecValue });

                // clear
                nagSelect.innerHTML = '';
                // add default
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.text = 'Pilih Nagari';
                nagSelect.appendChild(defaultOpt);

                if (!kecValue) return;
                
                // try AJAX first (if URL template is correct)
                try {
                    const res = await fetch(`/api/kecamatan/${encodeURIComponent(kecValue)}/nagari`, { credentials: 'same-origin' });
                    if (res.ok) {
                        const data = await res.json();
                        const list = data.nagari || [];
                        list.forEach(item => {
                            const name = item.nama_nagari || item.name || item.nama || item.nama_desa_kelurahan || '';
                            const id = item.id || item.kode || item.kode_desa_kelurahan || '';
                            if (!name) return;
                            const o = document.createElement('option');
                            o.value = id;
                            o.text = name;
                            o.setAttribute('data-kecamatan', kecValue);
                            nagSelect.appendChild(o);
                        });
                        nagariPopulatedByAjaxFor = kecValue ? kecValue.toString() : null;
                        console.debug('AJAX returned', { kecValue, listLength: list.length, optionsAfterFill: nagSelect.options.length });
                        return;
                    }
                } catch (e) {
                    console.warn('AJAX nagari fetch failed, falling back to client-side filter.', e);
                }

                // fallback: client-side filter using original options
                if (nagariPopulatedByAjaxFor && kecValue && nagariPopulatedByAjaxFor === kecValue.toString()) {
                    console.debug('Skipping client-side fallback because AJAX already populated for', kecValue);
                    return;
                }

                originalNagOptions.forEach(opt => {
                    const parent = (opt.parent || '').toString().trim();
                    const matchesId = parent === kecValue.toString();
                    
                    if (parent && matchesId) {
                        const o = document.createElement('option');
                        o.value = opt.value;
                        o.text = opt.text;
                        o.setAttribute('data-kecamatan', opt.parent);
                        nagSelect.appendChild(o);
                    }
                });
            }

            if (kecSelect) {
                kecSelect.addEventListener('change', function(){
                    filterNagariByKecamatan(this.value);
                });
            }

            // Role-based logic (MAIN FIX HERE)
            if (roleField) {
                roleField.addEventListener('change', function() {
                    const val = this.value;
                    
                    // Reset field states first
                    const resetFields = () => {
                        [kecField, nagariField].forEach(field => {
                            if (field) field.style.display = 'none';
                        });
                        [kecSelect, nagSelect].forEach(select => {
                            if (select) {
                                select.disabled = true;
                                select.setAttribute('disabled', 'disabled');
                                select.style.pointerEvents = 'none'; // NON-INTERACTIVE
                                select.style.opacity = '0.6';
                                select.selectedIndex = 0; // Reset value
                                select.value = '';
                                // Reset Nagari dropdown options when hidden
                                if(select === nagSelect) filterNagariByKecamatan('');
                            }
                        });
                    };

                    resetFields(); // Start by resetting all

                    if(val == '2'){
                        // ROLE KECAMATAN: Show and enable Kecamatan only
                        if (kecField) kecField.style.display = 'flex';
                        if (kecSelect) {
                            kecSelect.disabled = false;
                            kecSelect.removeAttribute('disabled');
                            kecSelect.style.pointerEvents = 'auto'; // FIX: Make it clickable
                            kecSelect.style.opacity = '1';
                            kecSelect.style.zIndex = '99999'; // FIX: High z-index to fix click issues
                        }
                    } else if(val == '3'){
                        // ROLE NAGARI: Show and enable Kecamatan & Nagari
                        if (kecField) kecField.style.display = 'flex';
                        if (nagariField) nagariField.style.display = 'flex';

                        if (kecSelect) {
                            kecSelect.disabled = false;
                            kecSelect.removeAttribute('disabled');
                            kecSelect.style.pointerEvents = 'auto'; // FIX: Make it clickable
                            kecSelect.style.opacity = '1';
                            kecSelect.style.zIndex = '99999'; // FIX: High z-index
                        }

                        if (nagSelect) {
                            nagSelect.disabled = false;
                            nagSelect.removeAttribute('disabled');
                            nagSelect.style.pointerEvents = 'auto'; // FIX: Make it clickable
                            nagSelect.style.opacity = '1';
                            nagSelect.style.zIndex = '99999'; // FIX: High z-index
                        }
                        
                        // Try to populate nagari list if a kecamatan is already selected
                        if (kecSelect && kecSelect.value) {
                             filterNagariByKecamatan(kecSelect.value);
                        } else if (kecSelect) {
                            // If role is 3 but no kecamatan selected, try to auto-select the first one
                             for (let i=0;i<kecSelect.options.length;i++){
                                if (kecSelect.options[i].value && kecSelect.options[i].value !== ''){
                                    kecSelect.value = kecSelect.options[i].value;
                                    filterNagariByKecamatan(kecSelect.value);
                                    break;
                                }
                            }
                        }
                    }
                    // For Admin/Pegawai/Other roles, the fields are hidden and disabled by resetFields()
                });
            }
            
            // Trigger change event on load to set initial state
            if (roleField) {
                roleField.dispatchEvent(new Event('change'));
            }

            // when user picks a nagari, automatically set role to Nagari (3)
            if (nagSelect) {
                nagSelect.addEventListener('change', function(){
                    if (this.value && roleField && roleField.value !== '3') {
                        roleField.value = '3';
                        roleField.dispatchEvent(new Event('change'));
                    }
                });
            }

            // Animate form fields on load
            const fieldContainers = document.querySelectorAll('form > div > div');
            fieldContainers.forEach((container, index) => {
                // Skips kecamatan/nagari fields on initial load if they are hidden
                if (container.style.display === 'none') return; 
                
                container.style.opacity = '0';
                container.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    container.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, index * 100);
            });


            // Other smaller listeners (password, focus effects, email validation)
            
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                });
            }

            const formFields = document.querySelectorAll('input, select');
            formFields.forEach(field => {
                const closestDiv = field.closest('div');
                const indicator = closestDiv ? closestDiv.querySelector('.field-indicator') : null;

                field.addEventListener('focus', function() {
                    if (indicator) indicator.style.opacity = '1';
                });

                field.addEventListener('blur', function() {
                    if (indicator) indicator.style.opacity = '0';
                });
            });

            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value && !emailRegex.test(this.value)) {
                        this.style.borderColor = '#ff6b6b';
                        this.setCustomValidity('Format email tidak valid');
                    } else {
                        this.style.borderColor = '#e0e0e0';
                        this.setCustomValidity('');
                    }
                });
            }

            // HILANGKAN SEMUA FUNGSI YANG TIDAK TERPAKAI (normalize, notification functions)
            // (Semua fungsi notifikasi telah dihapus dari skrip ini untuk fokus pada perbaikan form)

        });
    </script>
</body>
</html>