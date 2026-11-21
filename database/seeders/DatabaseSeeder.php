<?php

namespace Database\Seeders;

use App\Models\BidangMinat;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Penyelenggara;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Data awal PINTAR. Nama kegiatan, tanggal, kontak, dan tautan adalah DATA ILUSTRASI
     * (tautan memakai domain example.com) dan harus diganti dengan data resmi.
     * Tanggal dihitung relatif dari hari ini supaya status pendaftaran selalu bervariasi.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(['email' => 'admin@pintar.test'], [
            'name' => 'Admin PINTAR',
            'password' => 'admin12345',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        User::query()->updateOrCreate(['email' => 'mahasiswa@pintar.test'], [
            'name' => 'Mahasiswa Contoh',
            'password' => 'mahasiswa123',
            'nim' => '2510631250000',
            'prodi' => 'Sistem Informasi',
        ]);

        $kategori = [
            ['Beasiswa', 'cap'], ['Lomba', 'trophy'], ['Seminar', 'mic'],
            ['Workshop', 'wrench'], ['Kepanitiaan', 'users'], ['Volunteer', 'heart'],
        ];
        foreach ($kategori as $i => [$nama, $ikon]) {
            Kategori::query()->updateOrCreate(['nama' => $nama], ['slug' => Str::slug($nama), 'ikon' => $ikon, 'urutan' => $i]);
        }

        foreach (['Teknologi', 'Desain', 'Bisnis', 'Sains', 'Sosial', 'Bahasa', 'Umum'] as $nama) {
            BidangMinat::query()->firstOrCreate(['nama' => $nama]);
        }

        $posters = ['l1', 'w1', 's1', 'v1'];
        $data = json_decode(file_get_contents(database_path('seeders/kegiatan.json')), true);
        $hari = fn (int $n) => Carbon::today()->addDays($n)->toDateString();

        foreach ($data as $row) {
            $penyelenggara = Penyelenggara::query()->updateOrCreate(['nama' => $row['penyelenggara']], [
                'kontak_nama' => $row['kontak']['nama'],
                'kontak_info' => $row['kontak']['info'],
            ]);

            $poster = null;
            if (in_array($row['id'], $posters, true)) {
                $poster = "posters/{$row['id']}.jpg";
                Storage::disk('public')->put($poster, file_get_contents(database_path("seeders/posters/{$row['id']}.jpg")));
            }

            $kegiatan = Kegiatan::query()->updateOrCreate(['nama' => $row['nama']], [
                'kategori_id' => Kategori::query()->where('nama', $row['kategori'])->value('id'),
                'penyelenggara_id' => $penyelenggara->id,
                'jenis' => $row['jenis'],
                'deskripsi' => $row['deskripsi'],
                'persyaratan' => implode("\n", $row['syarat']),
                'manfaat' => $row['manfaat'] ?? null,
                'tgl_buka' => $hari($row['buka']),
                'tgl_tutup' => $hari($row['tutup']),
                'tgl_pelaksanaan' => isset($row['acara']) ? $hari($row['acara']) : null,
                'lokasi' => $row['lokasi'] ?? null,
                'sumber_nama' => $row['sumber']['nama'],
                'sumber_url' => $row['sumber']['url'],
                'poster_path' => $poster,
                'terverifikasi' => $row['verified'],
                'tgl_verifikasi' => $row['verified'] ? $hari($row['cek'] ?? 0) : null,
                'dibuat_oleh' => $admin->id,
            ]);

            $kegiatan->bidangMinats()->sync(BidangMinat::query()->whereIn('nama', $row['bidang'])->pluck('id'));
        }
    }
}
