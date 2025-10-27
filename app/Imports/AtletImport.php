<?php

namespace App\Imports;

use App\Models\Pelatih;
use App\Models\Atlet;
use App\Models\Cabor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AtletImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    // counters
    public $inserted = 0;
    public $skipped = 0;

    /**
     * Convert possible Excel date serial or string to Y-m-d or return null
     */
    protected function parseDate($value)
    {
        if ($value === null || $value === '') return null;
        // numeric from Excel (serial)
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float)$value);
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        // try parseable string
        $ts = strtotime($value);
        if ($ts !== false) return date('Y-m-d', $ts);
        return null;
    }

    /**
     * Map cabor value (id or name) to id or null
     */
    protected function resolveCaborId($value)
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (int)$value;
        // try find by nama_cabor (case-insensitive)
        $c = Cabor::whereRaw('LOWER(nama_cabor) = ?', [strtolower(trim($value))])->first();
        return $c ? $c->id : null;
    }

    public function model(array $row)
    {
        $nama = isset($row['nama']) ? trim($row['nama']) : null;
        $email = isset($row['email']) ? trim($row['email']) : null;

        // basic required checks
        if (empty($nama) || empty($email)) {
            $this->skipped++;
            return null; // skip row
        }

        // skip if email already exists
        if (Atlet::where('email', $email)->exists()) {
            $this->skipped++;
            return null;
        }

        $password = isset($row['password']) && $row['password'] !== '' ? $row['password'] : 'password123';
        $jenis_kelamin = $row['jenis_kelamin'] ?? 'Laki-laki';
        $no_telp = $row['no_telp'] ?? null;
        $id_cabor = $this->resolveCaborId($row['cabor'] ?? null);
        $tanggal_lahir = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tanggal_gabung = $this->parseDate($row['tanggal_gabung'] ?? null) ?? now()->toDateString();
        $status = $row['status'] ?? 'Aktif';

        // if cabor is required but not found, skip to avoid FK errors
        if (is_null($id_cabor)) {
            $this->skipped++;
            return null;
        }

        $model = new Atlet([
            'nama' => $nama,
            'email' => $email,
            'password' => bcrypt($password),
            'jenis_kelamin' => $jenis_kelamin,
            'no_telp' => $no_telp,
            'id_cabor' => $id_cabor,
            'tanggal_lahir' => $tanggal_lahir,
            'tanggal_gabung' => $tanggal_gabung,
            'status' => $status,
        ]);

        $this->inserted++;
        return $model;
    }
}

class PelatihImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    // counters
    public $inserted = 0;
    public $skipped = 0;

    /**
     * Convert possible Excel date serial or string to Y-m-d or return null
     */
    protected function parseDate($value)
    {
        if ($value === null || $value === '') return null;
        // numeric from Excel (serial)
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float)$value);
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        // try parseable string
        $ts = strtotime($value);
        if ($ts !== false) return date('Y-m-d', $ts);
        return null;
    }

    /**
     * Map cabor value (id or name) to id or null
     */
    protected function resolveCaborId($value)
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (int)$value;
        // try find by nama_cabor (case-insensitive)
        $c = Cabor::whereRaw('LOWER(nama_cabor) = ?', [strtolower(trim($value))])->first();
        return $c ? $c->id : null;
    }

    public function model(array $row)
    {
        $nama = isset($row['nama']) ? trim($row['nama']) : null;
        $email = isset($row['email']) ? trim($row['email']) : null;

        // basic required checks
        if (empty($nama) || empty($email)) {
            $this->skipped++;
            return null; // skip row
        }

        // skip if email already exists
        if (Pelatih::where('email', $email)->exists()) {
            $this->skipped++;
            return null;
        }

        $password = isset($row['password']) && $row['password'] !== '' ? $row['password'] : 'password123';
        $jenis_kelamin = $row['jenis_kelamin'] ?? 'Laki-laki';
        $no_telp = $row['no_telp'] ?? null;
        $id_cabor = $this->resolveCaborId($row['cabor'] ?? null);
        $tanggal_lahir = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tanggal_gabung = $this->parseDate($row['tanggal_gabung'] ?? null) ?? now()->toDateString();
        $status = $row['status'] ?? 'Aktif';

        // if cabor is required but not found, skip to avoid FK errors
        if (is_null($id_cabor)) {
            $this->skipped++;
            return null;
        }

        $model = new Pelatih([
            'nama' => $nama,
            'email' => $email,
            'password' => bcrypt($password),
            'jenis_kelamin' => $jenis_kelamin,
            'no_telp' => $no_telp,
            'id_cabor' => $id_cabor,
            'tanggal_lahir' => $tanggal_lahir,
            'tanggal_gabung' => $tanggal_gabung,
            'status' => $status,
        ]);

        $this->inserted++;
        return $model;
    }
}