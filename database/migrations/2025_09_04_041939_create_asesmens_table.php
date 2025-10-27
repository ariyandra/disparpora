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
        Schema::create('asesmens', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_asesmen');
            $table->char('aspek_fisik');
            $table->char('aspek_teknik');
            $table->char('aspek_sikap');
            $table->text('keterangan');
            $table->foreignId('id_atlet')->constrained('atlets')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_pelatih')->constrained('pelatihs')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesmens');
    }
};
