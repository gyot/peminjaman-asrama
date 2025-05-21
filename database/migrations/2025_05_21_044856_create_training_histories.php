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
        Schema::create('training_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');              // ID user yang mengikuti pelatihan
            $table->string('nama_pelatihan');                   // Nama pelatihan
            $table->string('durasi')->nullable();               // Durasi pelatihan (misal: 3 hari, 1 minggu)
            $table->text('materi')->nullable();                 // Ringkasan atau daftar materi
            $table->string('penyelenggara')->nullable();        // Lembaga penyelenggara
            $table->date('tanggal_mulai')->nullable();          // Tanggal mulai pelatihan
            $table->date('tanggal_selesai')->nullable();        // Tanggal selesai pelatihan
            $table->string('lokasi')->nullable();               // Lokasi pelatihan
            $table->string('sertifikat')->nullable();           // File sertifikat (path file)
            $table->string('tingkat')->nullable();              // Tingkat pelatihan: internal, nasional, dll
            $table->string('jenis_pelatihan')->nullable();      // Jenis pelatihan: teknis, fungsional, dll
            $table->text('catatan')->nullable();                // Catatan tambahan
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_histories');
    }
};
