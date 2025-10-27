<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Atlet - DISPARPORA</title>
    <link href="{{ asset('css/pelatih.css') }}" rel="stylesheet">
</head>
<body>
    <main style="padding:24px;">
        <h1>Import Data Atlet (Excel)</h1>

        @if(session('error'))
            <div style="color:red;margin:12px 0;">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div style="color:green;margin:12px 0;">{{ session('success') }}</div>
        @endif

        <form action="{{ route('import.atlet.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin:12px 0;">
                <label for="file">Pilih file Excel (.xlsx atau .xls):</label><br>
                <input type="file" name="file" id="file" accept=".xlsx,.xls" required>
            </div>
            <div style="margin-top:16px;">
                <button type="submit" style="padding:10px 18px;">Import</button>
                <a href="{{ route('data.atlet') }}" style="margin-left:12px;">Kembali</a>
            </div>
        </form>

        <p style="margin-top:20px;color:#666;">Format kolom Excel yang didukung: nama, email, password, jenis_kelamin, no_telp, cabor (id), tanggal_lahir (YYYY-MM-DD), tanggal_gabung (YYYY-MM-DD), status</p>
    </main>
</body>
</html>