<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif; color:#222;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <h2 style="color:#333;">Reset Password</h2>
        <p>Halo,</p>
        <p>Kami menerima permintaan untuk mereset password akun Anda (peran: <strong>{{ $role ?? '-' }}</strong>).</p>
        <p>Untuk mereset password, klik tautan berikut:</p>
        <p style="text-align:center;margin:24px 0;">
            <a href="{{ $resetUrl }}" style="display:inline-block;padding:12px 20px;border-radius:8px;background:#667eea;color:#fff;text-decoration:none;">Reset Password</a>
        </p>
        <p>Jika tautan di atas tidak bisa diklik, salin dan tempel URL ini ke browser Anda:</p>
        <p><small>{{ $resetUrl }}</small></p>
        <hr>
        <p style="font-size:12px;color:#666;">Jika Anda tidak meminta reset password, abaikan email ini.</p>
        <p style="font-size:12px;color:#666;">Terima kasih,<br>Tim DISPARPORA</p>
    </div>
</body>
</html>
