<?php

namespace App\Http\Controllers;

use App\Models\BidangMinat;
use App\Models\Kategori;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    /** Jelajahi kegiatan: FR-07, FR-08, FR-09, FR-10, FR-13, FR-14. */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'in:'.implode(',', Kegiatan::JENIS)],
            'kategori' => ['nullable', 'array'],
            'kategori.*' => ['string', 'max:50'],
            'bidang' => ['nullable', 'array'],
            'bidang.*' => ['integer'],
            'status' => ['nullable', 'in:'.implode(',', array_keys(Kegiatan::STATUS))],
            'terverifikasi' => ['nullable', 'boolean'],
            'urut' => ['nullable', 'in:deadline,baru,az'],
        ]);

        $query = Kegiatan::query()->with(['kategori', 'penyelenggara']);

        if ($q = trim($filter['q'] ?? '')) {
            foreach (preg_split('/\s+/', $q) as $kata) {
                $query->where(function ($w) use ($kata) {
                    $w->where('nama', 'like', "%{$kata}%")
                        ->orWhere('deskripsi', 'like', "%{$kata}%")
                        ->orWhereHas('penyelenggara', fn ($p) => $p->where('nama', 'like', "%{$kata}%"))
                        ->orWhereHas('kategori', fn ($p) => $p->where('nama', 'like', "%{$kata}%"))
                        ->orWhereHas('bidangMinats', fn ($p) => $p->where('nama', 'like', "%{$kata}%"));
                });
            }
        }
        if (! empty($filter['jenis'])) {
            $query->where('jenis', $filter['jenis']);
        }
        if (! empty($filter['kategori'])) {
            $query->whereHas('kategori', fn ($k) => $k->whereIn('slug', $filter['kategori']));
        }
        if (! empty($filter['bidang'])) {
            $query->whereHas('bidangMinats', fn ($b) => $b->whereIn('bidang_minats.id', $filter['bidang']));
        }
        if (! empty($filter['status'])) {
            $query->status($filter['status']);
        }
        if (! empty($filter['terverifikasi'])) {
            $query->where('terverifikasi', true);
        }

        match ($filter['urut'] ?? 'deadline') {
            'baru' => $query->orderByDesc('tgl_buka'),
            'az' => $query->orderBy('nama'),
            default => $query->urutDeadline(),
        };

        return view('kegiatan.index', [
            'kegiatans' => $query->paginate(12)->withQueryString(),
            'filter' => $filter,
            'kategoris' => Kategori::query()->orderBy('urutan')->withCount('kegiatans')->get(),
            'bidangs' => BidangMinat::query()->orderBy('id')->get(),
            'idPengingat' => $this->idPengingat($request),
        ]);
    }

    /** Detail kegiatan: FR-02, FR-04, FR-06, FR-11, FR-15, FR-16, FR-17. */
    public function show(Request $request, Kegiatan $kegiatan): View
    {
        $kegiatan->load(['kategori', 'penyelenggara', 'bidangMinats']);

        return view('kegiatan.show', [
            'kegiatan' => $kegiatan,
            'terkait' => Kegiatan::query()->with(['kategori', 'penyelenggara'])
                ->where('kategori_id', $kegiatan->kategori_id)->whereKeyNot($kegiatan->id)
                ->aktif()->orderBy('tgl_tutup')->take(3)->get(),
            'idPengingat' => $this->idPengingat($request),
        ]);
    }

    /** Daftar beasiswa: FR-01, FR-02, FR-03, FR-04. */
    public function beasiswa(Request $request): View
    {
        $status = $request->validate(['status' => ['nullable', 'in:'.implode(',', array_keys(Kegiatan::STATUS))]])['status'] ?? null;

        $query = Kegiatan::query()->with(['kategori', 'penyelenggara'])
            ->whereHas('kategori', fn ($k) => $k->where('slug', 'beasiswa'));
        if ($status) {
            $query->status($status);
        }

        return view('beasiswa', [
            'beasiswas' => $query->urutDeadline()->get(),
            'status' => $status,
        ]);
    }
}
