# PINTAR — Pusat Informasi dan Aktivitas Relevan Mahasiswa

Aplikasi web Sistem Informasi PINTAR (Kelompok 5, APSI 3B-SI). Dibangun dengan **Laravel 13 (PHP 8.3)** dan **MySQL**, sesuai rencana teknologi di Feasibility Study.

## Fitur

| Fitur | Kebutuhan |
|---|---|
| Daftar kegiatan & beasiswa, kategori (Beasiswa, Lomba, Seminar, Workshop, Kepanitiaan, Volunteer) | FR-01, FR-07, FR-10, FR-12 |
| Pencarian kata kunci, filter jenis, kategori & bidang minat (multi-pilih), status, terverifikasi | FR-08, FR-09, FR-13, FR-14 |
| Detail kegiatan: persyaratan, periode & sisa hari, kontak, sumber resmi, poster | FR-02, FR-03, FR-04, FR-06, FR-11, FR-15, FR-17 |
| Pengingat deadline (lonceng, halaman Pengingat, banner di beranda untuk yang tutup ≤ 3 hari) | FR-05 |
| Tanda Terverifikasi + verifikasi oleh admin | FR-16 |
| Login & registrasi mahasiswa, login admin | tambahan |
| Admin: tambah, ubah, hapus kegiatan + upload poster | peran admin/pengelola |

PINTAR tidak menerima pendaftaran: mahasiswa mendaftar lewat tautan sumber resmi penyelenggara.

## Menjalankan di laptop

Butuh PHP 8.3+, Composer, dan MySQL/MariaDB (misalnya lewat XAMPP atau Laragon).

1. Buat database kosong bernama `pintar`.
2. Di folder proyek:

```bash
composer install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

3. Isi `DB_USERNAME` dan `DB_PASSWORD` di `.env` sesuai MySQL kamu.
4. Buat tabel, isi data contoh, dan sambungkan folder poster:

```bash
php artisan migrate --seed
```

```bash
php artisan storage:link
```

5. Jalankan server lalu buka http://localhost:8000

```bash
php artisan serve
```

### Windows dengan Laravel Herd (tanpa MySQL)

Herd versi gratis berisi PHP dan Composer, tetapi tidak berisi MySQL. Untuk mencoba di laptop, pakai SQLite:

1. Ekstrak proyek ke `C:\Users\<nama>\Herd\pintar-app` (folder bawaan Herd).
2. Jalankan `composer install`, salin `.env.example` menjadi `.env`, lalu `php artisan key:generate`.
3. Di `.env`, ubah `DB_CONNECTION=mysql` menjadi `DB_CONNECTION=sqlite`, beri tanda `#` di depan baris `DB_HOST` sampai `DB_PASSWORD`, dan isi `APP_URL=http://pintar-app.test`.
4. Buat file database kosong: `New-Item database\database.sqlite`
5. Jalankan `php artisan migrate --seed` dan `php artisan storage:link`.
6. Buka http://pintar-app.test (Herd menjalankan server otomatis, tidak perlu `php artisan serve`).

### Akun contoh (dari seeder)

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | admin@pintar.test | admin12345 |
| Mahasiswa | mahasiswa@pintar.test | mahasiswa123 |

**Data kegiatan dari seeder adalah data ilustrasi** (tautan memakai example.com). Ganti dengan data resmi lewat halaman Kelola sebelum dipakai sungguhan.

## Menjalankan tes

```bash
php artisan test
```

Tes memakai SQLite di memori, jadi tidak menyentuh database MySQL.

## Memasang di Niagahoster (shared hosting)

1. Buat database MySQL dan user-nya di hPanel.
2. Unggah seluruh folder proyek **di luar** `public_html` (misalnya `~/pintar`), lalu arahkan domain ke folder `~/pintar/public` (atau salin isi `public/` ke `public_html` dan sesuaikan path di `index.php`).
3. Lewat SSH/Terminal hPanel, di folder proyek:

```bash
composer install --no-dev --optimize-autoloader
```

```bash
php artisan key:generate
```

4. Ubah `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-kamu`, dan isi kredensial database.
5. Buat tabel dan isi data awal. Seeder juga membuat akun dan kegiatan contoh; hapus yang tidak dipakai lewat halaman Kelola setelah admin sungguhan dibuat:

```bash
php artisan migrate --force --seed
```

```bash
php artisan storage:link
```

6. Buat akun admin sungguhan (diminta kata sandi), lalu hapus akun contoh dari database:

```bash
php artisan pintar:buat-admin email-admin@kampus.ac.id --nama="Nama Pengelola"
```

```bash
php artisan config:cache
```

## Struktur penting

| Lokasi | Isi |
|---|---|
| `app/Models/Kegiatan.php` | Data kegiatan + `hitungStatus()`, `hitungSisaHari()` (sesuai Class Diagram) |
| `app/Http/Controllers` | Beranda, Jelajahi/Detail/Beasiswa, Pengingat, Auth, Admin |
| `database/migrations` | Tabel `kategoris`, `bidang_minats`, `penyelenggaras`, `kegiatans`, `pengingats` |
| `database/seeders` | Data awal (kategori, bidang minat, kegiatan contoh, akun contoh) |
| `resources/views` | Tampilan Blade (mengikuti mockup UI) |
| `public/css/pintar.css` | Gaya tampilan (palet Navy, Teal, Sky Blue, Beige) |
| `tests/` | Tes otomatis |

## Yang belum dibuat

- Kanal notifikasi di luar situs (email/WhatsApp) untuk pengingat — belum diputuskan di laporan.
- Halaman akun/profil, ulasan, dan dashboard pendapatan (ada di mockup, belum masuk tahap ini).
- Kriteria verifikasi resmi dan alur persetujuan admin masih perlu ditetapkan tim.
