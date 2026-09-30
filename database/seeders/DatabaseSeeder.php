<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\Visit;
use App\Models\VisitTreatment;
use App\Models\Odontogram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Tables Exist
        if (!Schema::hasTable('doctors')) {
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
        }

        if (!Schema::hasTable('treatments')) {
            Schema::create('treatments', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                $table->string('nama');
                $table->string('kategori')->default('Umum');
                $table->integer('tarif')->default(0);
                $table->integer('jasa_dokter')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visits')) {
            Schema::create('visits', function (Blueprint $table) {
                $table->id();
                $table->string('no_antrean');
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
                $table->string('poli')->default('Poli Umum');
                $table->date('tanggal_kunjungan');
                $table->string('jam_kunjungan')->nullable();
                $table->text('keluhan')->nullable();
                $table->text('diagnosa')->nullable();
                $table->string('tensi_darah')->nullable();
                $table->string('suhu_tubuh')->nullable();
                $table->string('berat_badan')->nullable();
                $table->string('penjamin')->default('Umum');
                $table->enum('status', ['Menunggu', 'Sedang Diperiksa', 'Selesai', 'Batal'])->default('Menunggu');
                $table->text('catatan_dokter')->nullable();
                $table->integer('biaya_konsultasi')->default(0);
                $table->integer('biaya_tindakan')->default(0);
                $table->integer('biaya_obat')->default(0);
                $table->integer('total_biaya')->default(0);
                $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
                $table->string('metode_pembayaran')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visit_treatments')) {
            Schema::create('visit_treatments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
                $table->foreignId('treatment_id')->constrained('treatments')->cascadeOnDelete();
                $table->integer('biaya')->default(0);
                $table->string('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('odontograms')) {
            Schema::create('odontograms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
                $table->integer('nomor_gigi');
                $table->string('kondisi')->default('Sehat');
                $table->string('tindakan')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 2. Users
        User::updateOrCreate(
            ['email' => 'admin@klinik.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'dokter@klinik.com'],
            [
                'name' => 'dokter',
                'password' => Hash::make('dokter123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Doctors
        $doc1 = Doctor::updateOrCreate(
            ['nama' => 'dr. Alberto Senju'],
            [
                'spesialisasi' => 'Dokter Umum',
                'no_sip' => 'SIP/DU/2024/0112',
                'no_hp' => '081234567890',
                'tarif_konsultasi' => 50000,
                'status' => 'Aktif',
            ]
        );

        $doc2 = Doctor::updateOrCreate(
            ['nama' => 'drg. Sarah Putri, Sp.KG'],
            [
                'spesialisasi' => 'Dokter Gigi',
                'no_sip' => 'SIP/DG/2024/0234',
                'no_hp' => '081298765432',
                'tarif_konsultasi' => 60000,
                'status' => 'Aktif',
            ]
        );

        $doc3 = Doctor::updateOrCreate(
            ['nama' => 'dr. Hendra Wijaya, Sp.A'],
            [
                'spesialisasi' => 'Spesialis Anak',
                'no_sip' => 'SIP/SPA/2023/0451',
                'no_hp' => '081377889900',
                'tarif_konsultasi' => 75000,
                'status' => 'Aktif',
            ]
        );

        $doc4 = Doctor::updateOrCreate(
            ['nama' => 'dr. Maya Anggraini'],
            [
                'spesialisasi' => 'Dokter Umum',
                'no_sip' => 'SIP/DU/2023/0088',
                'no_hp' => '081311223344',
                'tarif_konsultasi' => 50000,
                'status' => 'Aktif',
            ]
        );

        // 4. Treatments
        $t1 = Treatment::updateOrCreate(
            ['kode' => 'UM01'],
            ['nama' => 'Konsultasi Dokter Umum', 'kategori' => 'Umum', 'tarif' => 50000, 'jasa_dokter' => 30000]
        );
        $t2 = Treatment::updateOrCreate(
            ['kode' => 'UM02'],
            ['nama' => 'Nebulizer Terapi Asma', 'kategori' => 'Tindakan Medis', 'tarif' => 75000, 'jasa_dokter' => 35000]
        );
        $t3 = Treatment::updateOrCreate(
            ['kode' => 'UM03'],
            ['nama' => 'Jahit Luka Minor (Hecting)', 'kategori' => 'Bedah Minor', 'tarif' => 150000, 'jasa_dokter' => 80000]
        );
        $t4 = Treatment::updateOrCreate(
            ['kode' => 'UM04'],
            ['nama' => 'Pemeriksaan Gula Darah Acak', 'kategori' => 'Laboratorium', 'tarif' => 35000, 'jasa_dokter' => 10000]
        );
        $t5 = Treatment::updateOrCreate(
            ['kode' => 'GG01'],
            ['nama' => 'Konsultasi Dokter Gigi', 'kategori' => 'Gigi', 'tarif' => 60000, 'jasa_dokter' => 35000]
        );
        $t6 = Treatment::updateOrCreate(
            ['kode' => 'GG02'],
            ['nama' => 'Pembersihan Karang Gigi (Scalling)', 'kategori' => 'Gigi', 'tarif' => 250000, 'jasa_dokter' => 120000]
        );
        $t7 = Treatment::updateOrCreate(
            ['kode' => 'GG03'],
            ['nama' => 'Penambalan Gigi Komposit', 'kategori' => 'Gigi', 'tarif' => 200000, 'jasa_dokter' => 100000]
        );
        $t8 = Treatment::updateOrCreate(
            ['kode' => 'GG04'],
            ['nama' => 'Cabut Gigi Tetap (Ekstraksi)', 'kategori' => 'Gigi', 'tarif' => 180000, 'jasa_dokter' => 90000]
        );

        // 5. Patients
        $p1 = Patient::firstOrCreate(
            ['no_rm' => 'RM-20260921-0001'],
            [
                'nama' => 'prabowo',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1985-10-17',
                'alamat' => 'Jl. Merdeka No. 45, Jakarta Pusat',
                'no_hp' => '0846456',
                'penjamin' => 'BPJS',
                'no_penjamin' => '0001234567890',
            ]
        );

        $p2 = Patient::firstOrCreate(
            ['no_rm' => 'RM-20260921-0002'],
            [
                'nama' => 'Siti Rahayu',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-05-12',
                'alamat' => 'Jl. Melati No. 12, Jakarta',
                'no_hp' => '08123456789',
                'penjamin' => 'BPJS',
                'no_penjamin' => '0009876543210',
            ]
        );

        $p3 = Patient::firstOrCreate(
            ['no_rm' => 'RM-20260922-0003'],
            [
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1978-08-20',
                'alamat' => 'Jl. Sudirman No. 88, Jakarta',
                'no_hp' => '081399887766',
                'penjamin' => 'Umum',
            ]
        );

        $p4 = Patient::firstOrCreate(
            ['no_rm' => 'RM-20260922-0004'],
            [
                'nama' => 'Dewi Lestari',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1995-12-03',
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'no_hp' => '081544332211',
                'penjamin' => 'Asuransi Swasta',
            ]
        );

        // 6. Sample Visits
        // Visit 1: prabowo - Selesai (Poli Umum)
        $v1 = Visit::updateOrCreate(
            ['no_antrean' => 'A-001', 'tanggal_kunjungan' => now()->toDateString()],
            [
                'patient_id' => $p1->id,
                'doctor_id' => $doc1->id,
                'poli' => 'Poli Umum',
                'jam_kunjungan' => '08:30',
                'keluhan' => 'Demam, flu, batuk sejak 3 hari',
                'diagnosa' => 'ISPA (Infeksi Saluran Pernapasan Akut)',
                'tensi_darah' => '120/80 mmHg',
                'suhu_tubuh' => '38.2 C',
                'berat_badan' => '72 kg',
                'penjamin' => 'BPJS',
                'status' => 'Selesai',
                'catatan_dokter' => 'Istirahat cukup, perbanyak minum air hangat, obat antipiretik 3x1.',
                'biaya_konsultasi' => 50000,
                'biaya_tindakan' => 35000,
                'biaya_obat' => 45000,
                'total_biaya' => 130000,
                'status_pembayaran' => 'Lunas',
                'metode_pembayaran' => 'BPJS',
            ]
        );
        VisitTreatment::firstOrCreate(['visit_id' => $v1->id, 'treatment_id' => $t4->id], ['biaya' => 35000, 'catatan' => 'Pemeriksaan gula darah']);

        // Visit 2: Siti Rahayu - Selesai (Poli Gigi)
        $v2 = Visit::updateOrCreate(
            ['no_antrean' => 'G-001', 'tanggal_kunjungan' => now()->toDateString()],
            [
                'patient_id' => $p2->id,
                'doctor_id' => $doc2->id,
                'poli' => 'Poli Gigi',
                'jam_kunjungan' => '09:15',
                'keluhan' => 'Gigi geraham kanan bawah ngilu saat minum dingin',
                'diagnosa' => 'Karies Dentin Gigi 46',
                'tensi_darah' => '115/75 mmHg',
                'suhu_tubuh' => '36.6 C',
                'berat_badan' => '54 kg',
                'penjamin' => 'Umum',
                'status' => 'Selesai',
                'catatan_dokter' => 'Dilakukan preparasi kavitas dan penambalan komposit gigi 46.',
                'biaya_konsultasi' => 60000,
                'biaya_tindakan' => 200000,
                'biaya_obat' => 30000,
                'total_biaya' => 290000,
                'status_pembayaran' => 'Lunas',
                'metode_pembayaran' => 'Tunai',
            ]
        );
        VisitTreatment::firstOrCreate(['visit_id' => $v2->id, 'treatment_id' => $t7->id], ['biaya' => 200000, 'catatan' => 'Tambal komposit gigi 46']);

        // Visit 3: Budi Santoso - Sedang Diperiksa
        $v3 = Visit::updateOrCreate(
            ['no_antrean' => 'A-002', 'tanggal_kunjungan' => now()->toDateString()],
            [
                'patient_id' => $p3->id,
                'doctor_id' => $doc1->id,
                'poli' => 'Poli Umum',
                'jam_kunjungan' => '10:00',
                'keluhan' => 'Sesak napas kambuh, batuk berdahak',
                'diagnosa' => 'Asma Bronkial Eksaserbasi Ringan',
                'tensi_darah' => '130/85 mmHg',
                'suhu_tubuh' => '36.8 C',
                'berat_badan' => '68 kg',
                'penjamin' => 'Umum',
                'status' => 'Sedang Diperiksa',
                'catatan_dokter' => 'Diberikan terapi nebulizer ventolin 1 ampul.',
                'biaya_konsultasi' => 50000,
                'biaya_tindakan' => 75000,
                'biaya_obat' => 50000,
                'total_biaya' => 175000,
                'status_pembayaran' => 'Belum Lunas',
                'metode_pembayaran' => 'QRIS',
            ]
        );
        VisitTreatment::firstOrCreate(['visit_id' => $v3->id, 'treatment_id' => $t2->id], ['biaya' => 75000, 'catatan' => 'Nebulizer 1 sesi']);

        // Visit 4: Dewi Lestari - Menunggu
        Visit::updateOrCreate(
            ['no_antrean' => 'G-002', 'tanggal_kunjungan' => now()->toDateString()],
            [
                'patient_id' => $p4->id,
                'doctor_id' => $doc2->id,
                'poli' => 'Poli Gigi',
                'jam_kunjungan' => '10:30',
                'keluhan' => 'Pembersihan karang gigi rutin',
                'diagnosa' => null,
                'tensi_darah' => null,
                'suhu_tubuh' => null,
                'berat_badan' => null,
                'penjamin' => 'Asuransi Swasta',
                'status' => 'Menunggu',
                'biaya_konsultasi' => 60000,
                'biaya_tindakan' => 250000,
                'biaya_obat' => 0,
                'total_biaya' => 310000,
                'status_pembayaran' => 'Belum Lunas',
            ]
        );

        // Visit 5 (Kemarin): prabowo - Selesai
        Visit::updateOrCreate(
            ['no_antrean' => 'A-010', 'tanggal_kunjungan' => now()->subDay()->toDateString()],
            [
                'patient_id' => $p1->id,
                'doctor_id' => $doc4->id,
                'poli' => 'Poli Umum',
                'jam_kunjungan' => '14:00',
                'keluhan' => 'Luka robek di telapak tangan akibat terkena kaca',
                'diagnosa' => 'Vulnus Laceratum Regio Palmar Dextra',
                'tensi_darah' => '120/80 mmHg',
                'suhu_tubuh' => '36.5 C',
                'berat_badan' => '72 kg',
                'penjamin' => 'BPJS',
                'status' => 'Selesai',
                'catatan_dokter' => 'Debridement luka, jahit 4 jahitan, injeksi tetanus.',
                'biaya_konsultasi' => 50000,
                'biaya_tindakan' => 150000,
                'biaya_obat' => 60000,
                'total_biaya' => 260000,
                'status_pembayaran' => 'Lunas',
                'metode_pembayaran' => 'BPJS',
            ]
        );

        // 7. Odontogram for Patient Prabowo
        Odontogram::updateOrCreate(
            ['patient_id' => $p1->id, 'nomor_gigi' => 16],
            ['kondisi' => 'Karies', 'tindakan' => 'Perlu Tambalan Komposit', 'catatan' => 'Karies oklusal sedang']
        );
        Odontogram::updateOrCreate(
            ['patient_id' => $p1->id, 'nomor_gigi' => 21],
            ['kondisi' => 'Tambalan Komposit', 'tindakan' => 'Restorasi Baik', 'catatan' => 'Tambalan sisi mesial']
        );
        Odontogram::updateOrCreate(
            ['patient_id' => $p1->id, 'nomor_gigi' => 36],
            ['kondisi' => 'Hilang', 'tindakan' => 'Disarankan Gigi Tiruan', 'catatan' => 'Ekstraksi 2 tahun lalu']
        );
        Odontogram::updateOrCreate(
            ['patient_id' => $p1->id, 'nomor_gigi' => 46],
            ['kondisi' => 'Karies', 'tindakan' => 'Perawatan Saluran Akar', 'catatan' => 'Karies profunda']
        );
    }
}
