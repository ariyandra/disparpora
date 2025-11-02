<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('atlets')) {
            // Add columns first
            Schema::table('atlets', function (Blueprint $table) {
                if (!Schema::hasColumn('atlets', 'kecamatan_id')) {
                    $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->onDelete('set null');
                }
                if (!Schema::hasColumn('atlets', 'nagari_id')) {
                    $table->foreignId('nagari_id')->nullable()->constrained('nagaris')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('atlets')) {
            Schema::table('atlets', function (Blueprint $table) {
                if (Schema::hasColumn('atlets', 'nagari_id')) {
                    $table->dropForeign(['nagari_id']);
                    $table->dropColumn('nagari_id');
                }
                if (Schema::hasColumn('atlets', 'kecamatan_id')) {
                    $table->dropForeign(['kecamatan_id']);
                    $table->dropColumn('kecamatan_id');
                }
            });
        }
    }
};