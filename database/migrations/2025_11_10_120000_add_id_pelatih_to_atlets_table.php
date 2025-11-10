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
        if (!Schema::hasTable('atlets')) {
            return;
        }

        Schema::table('atlets', function (Blueprint $table) {
            if (!Schema::hasColumn('atlets', 'id_pelatih')) {
                // nullable foreign key to pelatihs table; keep null when pelatih deleted
                $table->foreignId('id_pelatih')->nullable()->constrained('pelatihs')->nullOnDelete()->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('atlets')) {
            return;
        }

        Schema::table('atlets', function (Blueprint $table) {
            if (Schema::hasColumn('atlets', 'id_pelatih')) {
                // drop foreign key then column
                // use dropConstrainedForeignId if available
                if (method_exists($table, 'dropConstrainedForeignId')) {
                    $table->dropConstrainedForeignId('id_pelatih');
                } else {
                    $table->dropForeign(['id_pelatih']);
                    $table->dropColumn('id_pelatih');
                }
            }
        });
    }
};
