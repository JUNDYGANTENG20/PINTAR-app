<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FR-05: mahasiswa menyimpan pengingat untuk kegiatan yang pendaftarannya akan berakhir. */
class PengingatController extends Controller
{
    public function index(Request $request): View
    {
        $kegiatans = $request->user()->kegiatanDiingatkan()->with(['kategori', 'penyelenggara'])->urutDeadline()->get();

        return view('pengingat.index', ['kegiatans' => $kegiatans]);
    }

    public function store(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $request->user()->pengingats()->firstOrCreate(['kegiatan_id' => $kegiatan->id]);

        $pesan = $kegiatan->hitungStatus() === 'ditutup'
            ? 'Pendaftaran sudah ditutup, tetapi pengingat tetap disimpan.'
            : "Pengingat disimpan: {$kegiatan->nama} ({$kegiatan->teksSisaWaktu()}).";

        return back()->with('sukses', $pesan);
    }

    public function destroy(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $request->user()->pengingats()->where('kegiatan_id', $kegiatan->id)->delete();

        return back()->with('sukses', 'Pengingat dihapus.');
    }
}
