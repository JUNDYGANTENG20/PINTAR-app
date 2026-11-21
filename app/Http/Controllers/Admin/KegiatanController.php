<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KegiatanRequest;
use App\Models\BidangMinat;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Penyelenggara;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/** Admin/pengelola: tambah, perbarui, hapus, dan verifikasi informasi kegiatan. */
class KegiatanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $kegiatans = Kegiatan::query()->with(['kategori', 'penyelenggara'])
            ->when($q, fn ($query) => $query->where(fn ($w) => $w->where('nama', 'like', "%{$q}%")
                ->orWhereHas('penyelenggara', fn ($p) => $p->where('nama', 'like', "%{$q}%"))))
            ->urutDeadline()->paginate(20)->withQueryString();

        $today = today();

        return view('admin.kegiatan.index', [
            'kegiatans' => $kegiatans,
            'q' => $q,
            'ringkasan' => [
                'Total informasi' => Kegiatan::query()->count(),
                'Sedang dibuka' => Kegiatan::query()->whereDate('tgl_buka', '<=', $today)->aktif()->count(),
                'Belum diverifikasi' => Kegiatan::query()->where('terverifikasi', false)->count(),
                'Sudah ditutup' => Kegiatan::query()->whereDate('tgl_tutup', '<', $today)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return $this->form(new Kegiatan([
            'jenis' => 'Non-akademik', 'tgl_buka' => today(), 'tgl_tutup' => today()->addDays(14),
        ]));
    }

    public function store(KegiatanRequest $request): RedirectResponse
    {
        $kegiatan = new Kegiatan(['dibuat_oleh' => $request->user()->id]);
        $this->simpan($request, $kegiatan);

        return redirect()->route('admin.kegiatan.index')->with('sukses', "Kegiatan \"{$kegiatan->nama}\" ditambahkan.");
    }

    public function edit(Kegiatan $kegiatan): View
    {
        return $this->form($kegiatan->load(['penyelenggara', 'bidangMinats']));
    }

    public function update(KegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        $this->simpan($request, $kegiatan);

        return redirect()->route('admin.kegiatan.index')->with('sukses', "Perubahan \"{$kegiatan->nama}\" disimpan.");
    }

    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        if ($kegiatan->poster_path) {
            Storage::disk('public')->delete($kegiatan->poster_path);
        }
        $nama = $kegiatan->nama;
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('sukses', "\"{$nama}\" dihapus.");
    }

    /** FR-16: tandai/batalkan status terverifikasi setelah dicocokkan dengan sumber resmi. */
    public function verifikasi(Kegiatan $kegiatan): RedirectResponse
    {
        $kegiatan->terverifikasi = ! $kegiatan->terverifikasi;
        $kegiatan->tgl_verifikasi = $kegiatan->terverifikasi ? today() : null;
        $kegiatan->save();

        return back()->with('sukses', $kegiatan->terverifikasi
            ? "\"{$kegiatan->nama}\" ditandai terverifikasi."
            : "Tanda verifikasi \"{$kegiatan->nama}\" dibatalkan.");
    }

    private function form(Kegiatan $kegiatan): View
    {
        return view('admin.kegiatan.form', [
            'kegiatan' => $kegiatan,
            'kategoris' => Kategori::query()->orderBy('urutan')->get(),
            'bidangs' => BidangMinat::query()->orderBy('id')->get(),
        ]);
    }

    private function simpan(KegiatanRequest $request, Kegiatan $kegiatan): void
    {
        $data = $request->validated();

        $penyelenggara = Penyelenggara::query()->firstOrCreate(['nama' => trim($data['penyelenggara'])]);
        $penyelenggara->update(['kontak_nama' => $data['kontak_nama'] ?? null, 'kontak_info' => $data['kontak_info'] ?? null]);

        $verifikasiBaru = $request->boolean('terverifikasi');
        $kegiatan->fill([
            'nama' => $data['nama'],
            'kategori_id' => $data['kategori_id'],
            'penyelenggara_id' => $penyelenggara->id,
            'jenis' => $data['jenis'],
            'deskripsi' => $data['deskripsi'],
            'persyaratan' => $data['persyaratan'] ?? null,
            'manfaat' => $data['manfaat'] ?? null,
            'tgl_buka' => $data['tgl_buka'],
            'tgl_tutup' => $data['tgl_tutup'],
            'tgl_pelaksanaan' => $data['tgl_pelaksanaan'] ?? null,
            'lokasi' => $data['lokasi'] ?? null,
            'sumber_nama' => $data['sumber_nama'],
            'sumber_url' => $data['sumber_url'],
            'terverifikasi' => $verifikasiBaru,
            'tgl_verifikasi' => $verifikasiBaru ? ($kegiatan->terverifikasi && $kegiatan->tgl_verifikasi ? $kegiatan->tgl_verifikasi : today()) : null,
        ]);

        if ($request->boolean('hapus_poster') && $kegiatan->poster_path) {
            Storage::disk('public')->delete($kegiatan->poster_path);
            $kegiatan->poster_path = null;
        }
        if ($request->hasFile('poster')) {
            if ($kegiatan->poster_path) {
                Storage::disk('public')->delete($kegiatan->poster_path);
            }
            $kegiatan->poster_path = $request->file('poster')->store('posters', 'public');
        }

        $kegiatan->save();
        $kegiatan->bidangMinats()->sync($data['bidang'] ?? []);
    }
}
