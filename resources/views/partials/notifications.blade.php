<li class="nav-item">
    <div class="notification-container" style="position: relative; cursor: pointer;" onclick="toggleNotifications()">
        <span class="notification-icon" id="notificationBell">🔔</span>
        <span class="notification-badge" id="notificationBadge" style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold;">{{ $notif->count() }}</span>

        <!-- Notification Dropdown -->
        <div class="notification-dropdown" id="notificationDropdown" style="position: absolute; top: 100%; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 1000; display: none; margin-top: 10px; border: 1px solid #e0e0e0;">
            <div style="padding: 16px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Notifikasi</h3>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                <div class="notification-item" style="padding: 12px 16px; border-bottom: 1px solid #f8f8f8; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                    @foreach ($notif as $n)
                    <div style="display: flex; align-items: start; gap: 12px;">
                        <div style="width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">{{ $n->keterangan }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="padding: 12px 16px; border-top: 1px solid #f0f0f0; text-align: center;">
                <button type="button" onclick="markAllAsRead()"
                    style="background: none; border: none; color: #667eea; font-size: 14px; cursor: pointer; font-weight: 500; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;"
                    onmouseover="this.style.background='#f0f2ff'"
                    onmouseout="this.style.background='none'">
                    Tandai Semua Dibaca
                </button>
            </div>
        </div>
    </div>
</li>

<script>
const notifIds = [{{ $notif->pluck('id')->implode(',') }}];

function markAllAsRead() {
    if (notifIds.length === 0) return;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    notifIds.forEach(id => formData.append('notif_id[]', id));

    fetch('{{ route("admin.notifikasi") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(() => {
        const badge = document.getElementById('notificationBadge');
        const bell = document.getElementById('notificationBell');
        const items = document.querySelectorAll('.notification-item');
        const dropdown = document.getElementById('notificationDropdown');

        // Hide badge
        if (badge) badge.style.display = 'none';

        // Change bell to read state
        if (bell) bell.textContent = '🔕';

        // Mark all items as read (remove colored dots)
        items.forEach(item => {
            const dot = item.querySelector('div > div');
            if (dot) {
                dot.style.background = '#e0e0e0';
            }
        });

        // Show success message
        const successMsg = document.createElement('div');
        successMsg.innerHTML = '<div style="padding: 12px; text-align: center; color: #4CAF50; font-size: 14px; font-weight: 500;">✓ Semua notifikasi telah dibaca</div>';
        dropdown.appendChild(successMsg);

        setTimeout(() => {
            successMsg.remove();
            if (dropdown) dropdown.style.display = 'none';
        }, 2000);
    })
    .catch(error => console.error('Error:', error));
}
</script>
