<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Pelatih;
use App\Models\Atlet;

class TestPelatihSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('pelatihs')) {
            $this->command->info('Table pelatihs does not exist. Aborting TestPelatihSeeder.');
            return;
        }

        $pelatih = Pelatih::first();
        if (!$pelatih) {
            $this->command->info('No Pelatih found — creating a test Pelatih.');
            $pelatih = Pelatih::create([
                'nama' => 'Pelatih Test',
                'email' => 'pelatih-test@local',
                'password' => bcrypt('secret'),
                'status_verifikasi' => 'approved',
            ]);
            $this->command->info('Created Pelatih id=' . $pelatih->id);
        } else {
            $this->command->info('Pelatih already exists id=' . $pelatih->id);
        }

        $atlet = Atlet::first();
        if (!$atlet) {
            $this->command->info('No Atlet rows found — cannot assign pelatih to atlet.');
            return;
        }

        $atlet->id_pelatih = $pelatih->id;
        $atlet->save();
        $this->command->info('Assigned Atlet id=' . $atlet->id . ' to Pelatih id=' . $pelatih->id);
    }
}
