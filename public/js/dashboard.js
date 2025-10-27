let isEditing = false;

function toggleEdit() {
    isEditing = !isEditing;
    const editBtn = document.querySelector('.btn-edit, .btn-save');
    const editText = document.getElementById('editText');
    const editActions = document.querySelector('.edit-actions');
    
    // Get all form elements
    const inputs = document.querySelectorAll('.form-input, .form-select');
    const displays = document.querySelectorAll('.form-display');
    
    if (isEditing) {
        // Switch to edit mode
        editText.textContent = 'Simpan';
        editBtn.className = 'btn btn-save';
        editBtn.innerHTML = '<i class="fas fa-save"></i><span id="editText">Simpan</span>';
        editBtn.onclick = saveProfile;
        
        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-cancel';
        cancelBtn.innerHTML = '<i class="fas fa-times"></i>Batal';
        cancelBtn.onclick = cancelEdit;
        editActions.appendChild(cancelBtn);
        
        // Enable inputs and hide displays
        inputs.forEach(input => {
            input.disabled = false;
            input.style.display = 'block';
        });
        displays.forEach(display => {
            display.style.display = 'none';
        });
        
    } else {
        // Save and switch to view mode
        saveProfile();
    }
}

function cancelEdit() {
    isEditing = false;
    switchToViewMode();
    
    // Reset form values to original (you might want to store original values)
    const originalValues = {
        nama: document.getElementById('namaDisplay').textContent,
        email: document.getElementById('emailDisplay').textContent,
        jenisKelamin: document.getElementById('jenisKelaminDisplay').textContent,
        noHP: document.getElementById('noHPDisplay').textContent,
        cabangOlahraga: document.getElementById('cabangOlahragaDisplay').textContent
    };
    
    document.getElementById('nama').value = originalValues.nama;
    document.getElementById('email').value = originalValues.email;
    document.getElementById('jenisKelamin').value = originalValues.jenisKelamin;
    document.getElementById('noHP').value = originalValues.noHP;
    document.getElementById('cabangOlahraga').value = originalValues.cabangOlahraga;
}

function switchToViewMode() {
    const editBtn = document.querySelector('.btn-edit, .btn-save');
    const editActions = document.querySelector('.edit-actions');
    
    // Reset edit button
    editBtn.className = 'btn btn-edit';
    editBtn.innerHTML = '<i class="fas fa-edit"></i><span id="editText">Edit</span>';
    editBtn.onclick = toggleEdit;
    
    // Remove cancel button
    const cancelBtn = editActions.querySelector('.btn-cancel');
    if (cancelBtn) {
        cancelBtn.remove();
    }
    
    // Disable inputs and show displays
    const inputs = document.querySelectorAll('.form-input, .form-select');
    const displays = document.querySelectorAll('.form-display');
    
    inputs.forEach(input => {
        input.disabled = true;
        input.style.display = 'none';
    });
    displays.forEach(display => {
        display.style.display = 'flex';
    });
    
    updateDisplayValues();
}

function saveProfile() {
    if (isEditing) {
        // Submit the form
        const form = document.getElementById('profileForm');
        
        // Basic validation
        const nama = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();
        const noHP = document.getElementById('noHP').value.trim();
        
        if (!nama || !email || !noHP) {
            alert('Mohon lengkapi semua field yang wajib diisi!');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Format email tidak valid!');
            return;
        }
        
        // If using AJAX instead of form submission
        // You can uncomment and modify this section
        /*
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Profil berhasil disimpan!', 'success');
                isEditing = false;
                switchToViewMode();
            } else {
                showAlert('Terjadi kesalahan saat menyimpan profil!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat menyimpan profil!', 'error');
        });
        */
        
        // For now, just simulate save and switch to view mode
        isEditing = false;
        switchToViewMode();
        showAlert('Profil berhasil disimpan!', 'success');
    }
}

function updateDisplayValues() {
    // Update display values with current input values
    document.getElementById('namaDisplay').textContent = document.getElementById('nama').value;
    document.getElementById('emailDisplay').textContent = document.getElementById('email').value;
    document.getElementById('jenisKelaminDisplay').textContent = document.getElementById('jenisKelamin').value;
    document.getElementById('noHPDisplay').textContent = document.getElementById('noHP').value;
    document.getElementById('cabangOlahragaDisplay').textContent = document.getElementById('cabangOlahraga').value;
    
    // Format dates for display
    const tanggalLahir = document.getElementById('tanggalLahir').value;
    const tanggalGabung = document.getElementById('tanggalGabung').value;
    
    if (tanggalLahir) {
        const date = new Date(tanggalLahir);
        document.getElementById('tanggalLahirDisplay').textContent = formatDateToIndonesian(date);
    }
    
    if (tanggalGabung) {
        const date = new Date(tanggalGabung);
        document.getElementById('tanggalGabungDisplay').textContent = formatDateToIndonesian(date);
    }
}

function formatDateToIndonesian(date) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${day} ${month} ${year}`;
}

function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Insert alert at the beginning of the form
    const form = document.getElementById('profileForm');
    form.insertBefore(alert, form.firstChild);
    
    // Auto remove alert after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Initialize display mode on page load
document.addEventListener('DOMContentLoaded', function() {
    switchToViewMode();
    
    // Add smooth scrolling for navigation links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            // Add active class to clicked link (only if it's not an actual navigation)
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                this.classList.add('active');
            }
        });
    });
    
    // Handle form submission
    const form = document.getElementById('profileForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProfile();
        });
    }
});

// Print functionality
function printProfile() {
    // Hide edit elements before printing
    const editElements = document.querySelectorAll('.edit-actions, .form-actions');
    editElements.forEach(el => el.style.display = 'none');
    
    window.print();
    
    // Restore edit elements after printing
    setTimeout(() => {
        editElements.forEach(el => el.style.display = '');
    }, 1000);
}