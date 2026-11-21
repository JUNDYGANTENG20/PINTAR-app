<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-12: kategori kegiatan (Beasiswa, Lomba, Seminar, Workshop, Kepanitiaan, Volunteer)
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('ikon', 30)->default('layers');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
        });

        // FR-13, FR-14: bidang minat (boleh lebih dari satu per kegiatan)
        Schema::create('bidang_minats', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // FR-06: penyelenggara beserta kontak yang dapat dihubungi
        Schema::create('penyelenggaras', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('kontak_nama')->nullable();
            $table->string('kontak_info')->nullable();
            $table->timestamps();
        });

        // FR-07: informasi kegiatan yang terstruktur
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->foreignId('penyelenggara_id')->constrained('penyelenggaras')->restrictOnDelete();
            $table->string('jenis', 20); // Akademik | Non-akademik (FR-09)
            $table->text('deskripsi');
            $table->text('persyaratan')->nullable(); // satu baris = satu syarat (FR-02)
            $table->string('manfaat')->nullable();
            $table->date('tgl_buka');            // FR-04
            $table->date('tgl_tutup');           // FR-04
            $table->date('tgl_pelaksanaan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('sumber_nama');       // FR-03, FR-15
            $table->string('sumber_url', 500);   // FR-17
            $table->string('poster_path')->nullable();
            $table->boolean('terverifikasi')->default(false); // FR-16
            $table->date('tgl_verifikasi')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tgl_tutup', 'tgl_buka']);
        });

        Schema::create('bidang_minat_kegiatan', function (Blueprint $table) {
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->cascadeOnDelete();
            $table->foreignId('bidang_minat_id')->constrained('bidang_minats')->cascadeOnDelete();
            $table->primary(['kegiatan_id', 'bidang_minat_id']);
        });

        // FR-05: pengingat deadline milik mahasiswa
        Schema::create('pengingats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'kegiatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengingats');
        Schema::dropIfExists('bidang_minat_kegiatan');
        Schema::dropIfExists('kegiatans');
        Schema::dropIfExists('penyelenggaras');
        Schema::dropIfExists('bidang_minats');
        Schema::dropIfExists('kategoris');
    }
};
