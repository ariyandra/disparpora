<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .card { max-width:480px; margin:60px auto; padding:28px; border-radius:16px; background:rgba(255,255,255,0.95); box-shadow:0 10px 30px rgba(0,0,0,0.08);} 
        .form-input{padding:12px 14px; border-radius:10px; border:1px solid #e6e6e6; width:100%;}
        .btn{background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:12px 16px; border-radius:10px; border:none; font-weight:600}
        .error-message{background:#fee2e2;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:12px}
        .success-message{background:#d1fae5;color:#059669;padding:12px;border-radius:8px;margin-bottom:12px}
    </style>
</head>
<body>
    <div class="card">
        <h2>Lupa Password</h2>
        <p>Masukkan email dan pilih peran akun Anda. Kami akan mengirimkan tautan reset password.</p>

        @if($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.forgot.submit') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label for="role">Peran</label>
                <select id="role" name="role" class="form-input" required>
                    <option value="">Pilih peran</option>
                    <option value="admin">Admin/Pegawai</option>
                    <option value="pelatih">Pelatih</option>
                    <option value="atlet">Atlet</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}" required>
            </div>

            <button type="submit" class="btn">Kirim Tautan Reset</button>
        </form>

        <div style="margin-top:12px;">
            <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
        </div>
    </div>
</body>
</html>
