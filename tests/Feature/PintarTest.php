<?php

namespace Tests\Feature;

use App\Models\BidangMinat;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PintarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::query()->where('email', 'admin@pintar.test')->firstOrFail();
    }

    private function mahasiswa(): User
    {
        return User::query()->where('email', 'mahasiswa@pintar.test')->firstOrFail();
    }

    private function dataKegiatan(array $ubah = []): array
    {
        return array_merge([
            'nama' => 'Hackathon Kampus 2026',
            'kategori_id' => Kategori::query()->where('slug', 'lomba')->value('id'),
            'jenis' => 'Non-akademik',
            'bidang' => [BidangMinat::query()->where('nama', 'Teknologi')->value('id')],
            'penyelenggara' => 'HIMSI',
            'kontak_nama' => 'Panitia',
            'kontak_info' => 'hackathon@kampus.example',
            'tgl_buka' => today()->toDateString(),
            'tgl_tutup' => today()->addDays(10)->toDateString(),
            'lokasi' => 'Aula Fakultas',
            'deskripsi' => 'Lomba membuat aplikasi dalam 24 jam.',
            'persyaratan' => "Tim 3 orang\nMahasiswa aktif",
            'sumber_nama' => 'Instagram HIMSI',
            'sumber_url' => 'https://example.com/hackathon',
        ], $ubah);
    }

    // ----- Halaman publik -----

    public function test_beranda_tampil(): void
    {
        $this->get('/')->assertOk()->assertSee('Temukan Peluang')->assertSee('Workshop Figma untuk Pemula');
    }

    public function test_pencarian_kata_kunci(): void // FR-08
    {
        $this->get('/kegiatan?q=figma')->assertOk()
            ->assertSee('Workshop Figma untuk Pemula')
            ->assertDontSee('Business Plan Competition');
    }

    public function test_filter_jenis_kategori_dan_bidang_multi_pilih(): void // FR-09, FR-13, FR-14
    {
        $teknologi = BidangMinat::query()->where('nama', 'Teknologi')->value('id');
        $bisnis = BidangMinat::query()->where('nama', 'Bisnis')->value('id');

        $this->get("/kegiatan?kategori[]=lomba&kategori[]=workshop&bidang[]={$teknologi}&bidang[]={$bisnis}")
            ->assertOk()
            ->assertSee('Lomba UI/UX Design')
            ->assertSee('Business Plan Competition')
            ->assertSee('Workshop Analisis Data')
            ->assertDontSee('Beasiswa Cendekia Nusantara');

        $this->get('/kegiatan?jenis=Non-akademik')->assertOk()
            ->assertSee('Open Recruitment Panitia Dies Natalis')
            ->assertDontSee('Seminar Nasional');
    }

    public function test_filter_status_dan_terverifikasi(): void // FR-04, FR-16
    {
        $this->get('/kegiatan?status=ditutup')->assertOk()
            ->assertSee('Lomba Karya Tulis Ilmiah Lingkungan')
            ->assertDontSee('Workshop Figma untuk Pemula');

        $this->get('/kegiatan?terverifikasi=1')->assertOk()
            ->assertDontSee('Beasiswa Riset Mahasiswa Data');
    }

    public function test_detail_menampilkan_kontak_sumber_dan_verifikasi(): void // FR-06, FR-11, FR-15, FR-16, FR-17
    {
        $k = Kegiatan::query()->where('nama', 'like', 'Lomba UI/UX%')->firstOrFail();

        $this->get("/kegiatan/{$k->id}")->assertOk()
            ->assertSee($k->sumber_url)
            ->assertSee('0813-0000-3344')
            ->assertSee('Terverifikasi')
            ->assertSee('Karya orisinal, belum pernah dilombakan');
    }

    public function test_halaman_beasiswa_hanya_berisi_beasiswa(): void // FR-01
    {
        $this->get('/beasiswa')->assertOk()
            ->assertSee('Beasiswa Cendekia Nusantara')
            ->assertDontSee('Workshop Figma untuk Pemula');
    }

    public function test_kegiatan_tidak_ada_404(): void
    {
        $this->get('/kegiatan/99999')->assertNotFound()->assertSee('Halaman tidak ditemukan');
    }

    // ----- Autentikasi -----

    public function test_registrasi_mahasiswa_tidak_bisa_menjadi_admin(): void
    {
        $this->post('/daftar', [
            'name' => 'Siti', 'nim' => '2510631250099', 'prodi' => 'Sistem Informasi',
            'email' => 'siti@kampus.example', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertSame('mahasiswa', User::query()->where('email', 'siti@kampus.example')->value('role'));
    }

    public function test_registrasi_validasi(): void
    {
        $this->post('/daftar', ['nim' => '12ab', 'email' => 'bukan-email', 'password' => '123', 'password_confirmation' => '321'])
            ->assertSessionHasErrors(['name', 'nim', 'prodi', 'email', 'password']);
    }

    public function test_login_gagal_dan_berhasil(): void
    {
        $this->post('/masuk', ['email' => 'mahasiswa@pintar.test', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/masuk', ['email' => 'admin@pintar.test', 'password' => 'admin12345'])->assertRedirect('/admin/kegiatan');
        $this->assertAuthenticatedAs($this->admin());
    }

    // ----- Pengingat (FR-05) -----

    public function test_tamu_harus_login_untuk_pengingat(): void
    {
        $k = Kegiatan::query()->first();
        $this->post("/kegiatan/{$k->id}/pengingat")->assertRedirect('/masuk');
        $this->get('/pengingat')->assertRedirect('/masuk');
    }

    public function test_mahasiswa_menyimpan_dan_menghapus_pengingat(): void
    {
        $user = $this->mahasiswa();
        $k = Kegiatan::query()->where('nama', 'like', 'Lomba UI/UX%')->firstOrFail();

        $this->actingAs($user)->post("/kegiatan/{$k->id}/pengingat")->assertRedirect();
        $this->actingAs($user)->post("/kegiatan/{$k->id}/pengingat"); // tidak dobel
        $this->assertSame(1, $user->pengingats()->count());

        $this->actingAs($user)->get('/pengingat')->assertOk()->assertSee($k->nama);
        $this->actingAs($user)->get('/')->assertSee('Pengingat:'); // tutup ≤ 3 hari -> banner di beranda

        $this->actingAs($user)->delete("/kegiatan/{$k->id}/pengingat")->assertRedirect();
        $this->assertSame(0, $user->pengingats()->count());
    }

    // ----- Admin -----

    public function test_mahasiswa_tidak_bisa_membuka_admin(): void
    {
        $this->actingAs($this->mahasiswa())->get('/admin/kegiatan')->assertForbidden();
        $this->actingAs($this->mahasiswa())->post('/admin/kegiatan', $this->dataKegiatan())->assertForbidden();
    }

    public function test_admin_menambah_kegiatan_dengan_poster(): void
    {
        $this->actingAs($this->admin())->post('/admin/kegiatan', $this->dataKegiatan([
            'poster' => UploadedFile::fake()->image('poster.png', 800, 1000),
            'terverifikasi' => '1',
        ]))->assertRedirect('/admin/kegiatan');

        $k = Kegiatan::query()->where('nama', 'Hackathon Kampus 2026')->firstOrFail();
        $this->assertTrue($k->terverifikasi);
        $this->assertNotNull($k->tgl_verifikasi);
        $this->assertSame(['Teknologi'], $k->bidangMinats->pluck('nama')->all());
        Storage::disk('public')->assertExists($k->poster_path);
        $this->get("/kegiatan/{$k->id}")->assertOk()->assertSee('Hackathon Kampus 2026')->assertSee('hackathon@kampus.example');
    }

    public function test_admin_validasi_tanggal_dan_tautan(): void
    {
        $this->actingAs($this->admin())->post('/admin/kegiatan', $this->dataKegiatan([
            'tgl_tutup' => today()->subDay()->toDateString(),
            'sumber_url' => 'bukan-tautan',
            'poster' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors(['tgl_tutup', 'sumber_url', 'poster']);

        $this->assertDatabaseMissing('kegiatans', ['nama' => 'Hackathon Kampus 2026']);
    }

    public function test_admin_mengubah_menghapus_dan_memverifikasi(): void
    {
        $admin = $this->admin();
        $k = Kegiatan::query()->where('terverifikasi', false)->firstOrFail();

        $this->actingAs($admin)->patch("/admin/kegiatan/{$k->id}/verifikasi")->assertRedirect();
        $this->assertTrue($k->fresh()->terverifikasi);

        $this->actingAs($admin)->put("/admin/kegiatan/{$k->id}", $this->dataKegiatan(['nama' => 'Nama Baru']))->assertRedirect('/admin/kegiatan');
        $this->assertSame('Nama Baru', $k->fresh()->nama);

        $poster = Kegiatan::query()->whereNotNull('poster_path')->firstOrFail();
        $path = $poster->poster_path;
        $this->actingAs($admin)->delete("/admin/kegiatan/{$poster->id}")->assertRedirect('/admin/kegiatan');
        $this->assertModelMissing($poster);
        Storage::disk('public')->assertMissing($path);
    }
}
