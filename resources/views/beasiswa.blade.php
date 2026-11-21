@extends('layouts.app', ['navAktif' => 'beasiswa', 'judul' => 'Beasiswa'])

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back')
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <span>Beasiswa</span></nav>
    <h1>Beasiswa</h1>
    <p>Daftar beasiswa beserta persyaratan, kriteria, periode pendaftaran, dan sumber resmi penyedia. Bandingkan dulu, baru daftar.</p>
</div></section>

<div class="wrap" style="padding-top:28px">
    <nav class="chips" aria-label="Filter status beasiswa">
        <a class="chip-toggle" href="{{ route('beasiswa') }}" aria-pressed="{{ $status ? 'false' : 'true' }}" style="text-decoration:none">Semua</a>
        @foreach(\App\Models\Kegiatan::STATUS as $kode => $label)
            <a class="chip-toggle" href="{{ route('beasiswa', ['status' => $kode]) }}" aria-pressed="{{ $status === $kode ? 'true' : 'false' }}" style="text-decoration:none">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="sch-list">
        @forelse($beasiswas as $k)
            <article class="sch-row">
                <div>
                    <div class="detail-tags">@include('partials.pill') @if($k->terverifikasi)<span class="vbadge"><x-ico name="badge"/> Terverifikasi</span>@else<span class="vbadge no">Belum diverifikasi</span>@endif</div>
                    <h3>{{ $k->nama }}</h3>
                    <p class="org"><x-ico name="building"/><span>{{ $k->penyelenggara->nama }}</span></p>
                    @if($k->manfaat)<p style="margin-top:10px;font:800 15px/1.4 var(--font-display);color:var(--primary-ink)">{{ $k->manfaat }}</p>@endif
                </div>
                <div><p class="lbl">Persyaratan &amp; kriteria</p><ul class="ticks">@foreach($k->daftarPersyaratan() as $syarat)<li><x-ico name="check"/><span>{{ $syarat }}</span></li>@endforeach</ul></div>
                <div class="stack" style="align-content:start">
                    <p class="lbl">Periode pendaftaran</p>
                    <p class="num" style="font-weight:600">{{ $k->tgl_buka->translatedFormat('j M Y') }} – {{ $k->tgl_tutup->translatedFormat('j M Y') }}</p>
                    <p class="remain {{ ['dibuka' => 'open', 'segera-berakhir' => 'ending', 'segera-dibuka' => 'soon', 'ditutup' => 'closed'][$k->hitungStatus()] }} num" style="font:700 14px/1.2 var(--font-display)">{{ $k->teksSisaWaktu() }}</p>
                    <p class="lbl" style="margin-top:6px">Sumber resmi</p>
                    <p style="font-size:14px;color:var(--ink-2)">{{ $k->sumber_nama }}</p>
                    <div class="stack" style="margin-top:6px">
                        <a class="btn btn-primary btn-sm" href="{{ route('kegiatan.show', $k) }}">Lihat detail</a>
                        <a class="btn btn-ghost btn-sm" href="{{ $k->sumber_url }}" target="_blank" rel="noopener noreferrer"><x-ico name="ext"/> Buka sumber resmi</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty"><x-ico name="cap"/><h3>Tidak ada beasiswa dengan status ini</h3><a class="btn btn-ghost btn-sm" href="{{ route('beasiswa') }}">Tampilkan semua</a></div>
        @endforelse
    </div>
</div>
@endsection
