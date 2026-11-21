<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(Request $request): View
    {
        $dengan = ['kategori', 'penyelenggara'];
        $terbuka = Kegiatan::query()->with($dengan)->whereDate('tgl_buka', '<=', today())->aktif()->orderBy('tgl_tutup')->get();

        $segeraBerakhir = $terbuka->filter(fn (Kegiatan $k) => $k->hitungStatus() === 'segera-berakhir')->values();
        $pilihan = Kegiatan::query()->with($dengan)->aktif()
            ->whereHas('kategori', fn ($q) => $q->where('slug', '!=', 'beasiswa'))
            ->status('dibuka')->orderBy('tgl_tutup')->take(6)->get();
        $beasiswa = Kegiatan::query()->with($dengan)->aktif()
            ->whereHas('kategori', fn ($q) => $q->where('slug', 'beasiswa'))
            ->orderBy('tgl_tutup')->take(3)->get();

        $kategoris = Kategori::query()->orderBy('urutan')
            ->withCount(['kegiatans as aktif_count' => fn ($q) => $q->aktif()])->get();

        $total = Kegiatan::query()->count();
        $statistik = [
            'aktif' => Kegiatan::query()->aktif()->count(),
            'kategori' => $kategoris->count(),
            'terverifikasi' => $total ? (int) round(Kegiatan::query()->where('terverifikasi', true)->count() / $total * 100) : 0,
            'segera' => $segeraBerakhir->count(),
        ];

        // FR-05: pengingat yang tutup dalam 3 hari untuk mahasiswa yang login.
        $pengingatMendesak = $request->user()
            ? $request->user()->kegiatanDiingatkan()->aktif()->whereDate('tgl_tutup', '<=', today()->addDays(3))->orderBy('tgl_tutup')->get()
            : collect();

        return view('beranda', [
            'terdekat' => $terbuka->first(),
            'berikutnya' => $terbuka->slice(1, 2),
            'segeraBerakhir' => $segeraBerakhir->take(4),
            'pilihan' => $pilihan,
            'beasiswa' => $beasiswa,
            'kategoris' => $kategoris,
            'statistik' => $statistik,
            'pengingatMendesak' => $pengingatMendesak,
            'idPengingat' => $this->idPengingat($request),
        ]);
    }
}
