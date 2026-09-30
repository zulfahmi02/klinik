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
        // 1. Doctors Table
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('spesialisasi')->default('Dokter Umum');
            $table->string('no_sip')->nullable();
            $table->string('no_hp')->nullable();
            $table->integer('tarif_konsultasi')->default(50000);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });

        // 2. Treatments (Master Tindakan Medis) Table
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('kategori')->default('Umum'); // Umum, Gigi, Tindakan Medis, Laboratorium
            $table->integer('tarif')->default(0);
            $table->integer('jasa_dokter')->default(0);
            $table->timestamps();
        });

        // 3. Visits (Kunjungan Pasien) Table
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('no_antrean');
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->string('poli')->default('Poli Umum'); // Poli Umum, Poli Gigi, KIA/KB, dll
            $table->date('tanggal_kunjungan');
            $table->string('jam_kunjungan')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('diagnosa')->nullable();
            $table->string('tensi_darah')->nullable();
            $table->string('suhu_tubuh')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('penjamin')->default('Umum'); // Umum, BPJS, Asuransi
            $table->enum('status', ['Menunggu', 'Sedang Diperiksa', 'Selesai', 'Batal'])->default('Menunggu');
            $table->text('catatan_dokter')->nullable();
            $table->integer('biaya_konsultasi')->default(0);
            $table->integer('biaya_tindakan')->default(0);
            $table->integer('biaya_obat')->default(0);
            $table->integer('total_biaya')->default(0);
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            $table->string('metode_pembayaran')->nullable(); // Tunai, QRIS, Transfer, BPJS
            $table->timestamps();
        });

        // 4. Visit Treatments (Detail Tindakan per Kunjungan)
        Schema::create('visit_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained('treatments')->cascadeOnDelete();
            $table->integer('biaya')->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });

        // 5. Odontograms (Pemeriksaan Gigi Pasien) Table
        Schema::create('odontograms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->integer('nomor_gigi'); // 11-18, 21-28, 31-38, 41-48
            $table->string('kondisi')->default('Sehat'); // Sehat, Karies, Tambalan Komposit, Tambalan Sementara, Hilang, Sisa Akar, Mahkota, Impaksi, Gigi Tiruan
            $table->string('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontograms');
        Schema::dropIfExists('visit_treatments');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('doctors');
    }
};
