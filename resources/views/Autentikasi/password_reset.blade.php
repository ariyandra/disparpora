<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .card { max-width:520px; margin:60px auto; padding:28px; border-radius:16px; background:rgba(255,255,255,0.95); box-shadow:0 10px 30px rgba(0,0,0,0.08);} 
        .form-input{padding:12px 14px; border-radius:10px; border:1px solid #e6e6e6; width:100%;}
        .btn{background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:12px 16px; border-radius:10px; border:none; font-weight:600}
        .error-message{background:#fee2e2;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:12px}
        .success-message{background:#d1fae5;color:#059669;padding:12px;border-radius:8px;margin-bottom:12px}
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Password</h2>
        <p>Masukkan password baru untuk akun Anda.</p>

        @if($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset.submit') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div style="margin-bottom:12px;">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="form-input" value="{{ $email ?? old('email') }}" required>
            </div>

            <div style="margin-bottom:12px;">
                <label for="role">Peran</label>
                <select id="role" name="role" class="form-input" required>
                    <option value="">Pilih peran</option>
                    <option value="admin" {{ (isset($role) && $role=='admin')? 'selected':'' }}>Admin/Pegawai</option>
                    <option value="pelatih" {{ (isset($role) && $role=='pelatih')? 'selected':'' }}>Pelatih</option>
                    <option value="atlet" {{ (isset($role) && $role=='atlet')? 'selected':'' }}>Atlet</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label for="password">Password Baru</label>
                <input id="password" name="password" type="password" class="form-input" required>
            </div>

            <div style="margin-bottom:12px;">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required>
            </div>

            <button type="submit" class="btn">Simpan Password Baru</button>
        </form>

        <div style="margin-top:12px;">
            <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
        </div>
    </div>
</body>
</html>
