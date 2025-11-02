<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First convert numeric roles to string roles
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $newRole = match ($user->role) {
                '0' => 'admin',
                '1' => 'pelatih',
                '2' => 'kecamatan',
                '3' => 'nagari',
                '4' => 'pelatih',
                '5' => 'atlet',
                default => 'atlet'
            };
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $newRole]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert string roles back to numeric
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $oldRole = match ($user->role) {
                'admin' => '0',
                'kecamatan' => '2',
                'nagari' => '3',
                'pelatih' => '4',
                'atlet' => '5',
                default => '5'
            };
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $oldRole]);
        }
    }
};