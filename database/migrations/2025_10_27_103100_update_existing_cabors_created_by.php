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
        // Get the first admin user
        $adminUser = DB::table('users')->where('role', 0)->first();
        
        if ($adminUser) {
            // Update all existing cabors to be created by this admin
            DB::table('cabors')
                ->whereNull('created_by')
                ->update(['created_by' => $adminUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data migration
    }
};