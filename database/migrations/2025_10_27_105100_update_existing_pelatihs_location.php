<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pelatih;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pelatihs = Pelatih::whereNotNull('created_by')->get();
        foreach($pelatihs as $pelatih) {
            $user = User::find($pelatih->created_by);
            if ($user) {
                $pelatih->kecamatan_id = $user->kecamatan_id;
                $pelatih->nagari_id = $user->nagari_id;
                $pelatih->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Pelatih::query()->update([
            'kecamatan_id' => null,
            'nagari_id' => null
        ]);
    }
};