<?php

namespace Tests\Unit;

use App\Models\Kegiatan;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StatusKegiatanTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public static function kasus(): array
    {
        // [hari buka relatif, hari tutup relatif, status, teks]
        return [
            'belum dibuka' => [3, 20, 'segera-dibuka', 'Dibuka dalam 3 hari'],
            'tutup hari ini' => [-5, 0, 'segera-berakhir', 'Berakhir hari ini'],
            'tutup 7 hari lagi' => [-5, 7, 'segera-berakhir', 'Sisa 7 hari'],
            'tutup 8 hari lagi' => [-5, 8, 'dibuka', 'Sisa 8 hari'],
            'sudah lewat' => [-20, -1, 'ditutup', 'Pendaftaran ditutup'],
        ];
    }

    #[DataProvider('kasus')]
    public function test_status_dan_sisa_hari(int $buka, int $tutup, string $status, string $teks): void
    {
        Carbon::setTestNow('2026-11-20 15:30:00');
        $k = new Kegiatan(['tgl_buka' => Carbon::today()->addDays($buka), 'tgl_tutup' => Carbon::today()->addDays($tutup)]);

        $this->assertSame($status, $k->hitungStatus());
        $this->assertSame($teks, $k->teksSisaWaktu());
    }

    public function test_persyaratan_dipecah_per_baris(): void
    {
        $k = new Kegiatan(['persyaratan' => "IPK minimal 3,00\r\n\n  Aktif semester 3 \nSurat rekomendasi"]);

        $this->assertSame(['IPK minimal 3,00', 'Aktif semester 3', 'Surat rekomendasi'], $k->daftarPersyaratan());
    }
}
