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
        Schema::create('atlets', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('password', 255);
            $table->string('email', 255);
            $table->string('jenis_kelamin', 10);
            $table->string('no_telp', 15);
            $table->date('tanggal_lahir');
            $table->date('tanggal_gabung');
            $table->foreignId('id_cabor')->constrained('cabors')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atlets');
    }
};
