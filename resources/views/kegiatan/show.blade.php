@extends('layouts.app', ['navAktif' => $kegiatan->kategori->slug === 'beasiswa' ? 'beasiswa' : 'jelajahi', 'judul' => $kegiatan->nama])

@php
    $k = $kegiatan;
    $s = $k->hitungStatus();
    $induk = $k->kategori->slug === 'beasiswa' ? route('beasiswa') : route('kegiatan.index');
@endphp

@section('konten')
<section class="detail-hero" style="--g1:var(--g1-{{ $k->kategori->slug }});--g2:var(--g2-{{ $k->kategori->slug }})">
    <span class="big" aria-hidden="true"><x-ico :name="$k->kategori->ikon"/></span>
    <div class="wrap">
        @include('partials.back', ['fallback' => $induk])
        <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ $induk }}">{{ $k->kategori->slug === 'beasiswa' ? 'Beasiswa' : 'Jelajahi Kegiatan' }}</a> / <span>{{ $k->kategori->nama }}</span></nav>
        <h1>{{ $k->nama }}</h1>
        <div class="detail-tags">
            <span class="tag-w"><x-ico :name="$k->kategori->ikon"/> {{ $k->kategori->nama }}</span>
            <span class="tag-w">{{ $k->jenis }}</span>
            @foreach($k->bidangMinats as $b)<span class="tag-w">{{ $b->nama }}</span>@endforeach
            @if($k->terverifikasi)<span class="tag-w ver"><x-ico name="badge"/> Terverifikasi</span>@endif
        </div>
    </div>
</section>

<div class="wrap detail-grid">
    <div>
        @if($k->poster_path)<section class="panel poster"><img src="{{ $k->posterUrl() }}" alt="Poster {{ $k->nama }}"></section>@endif
        <section class="panel"><h2><x-ico name="layers"/> Tentang kegiatan</h2><p>{{ $k->deskripsi }}</p>
            @if($k->manfaat)<p style="margin-top:12px"><b style="color:var(--ink)"><x-ico name="gift"/> Manfaat:</b> {{ $k->manfaat }}</p>@endif
        </section>
        <section class="panel"><h2><x-ico name="check"/> Persyaratan &amp; kriteria</h2>
            <ul class="ticks" style="font-size:15px">
                @forelse($k->daftarPersyaratan() as $syarat)<li><x-ico name="check"/><span>{{ $syarat }}</span></li>@empty<li>Belum ada persyaratan tercatat.</li>@endforelse
            </ul>
        </section>
        <section class="panel"><h2><x-ico name="arrow"/> Cara mendaftar</h2>
            <ol class="steps">
                <li><span><b>Cek persyaratan</b>Pastikan kamu memenuhi kriteria di atas.</span></li>
                <li><span><b>Buka sumber resmi</b>Baca pengumuman asli dari {{ $k->penyelenggara->nama }} untuk memastikan keasliannya.</span></li>
                <li><span><b>Daftar sebelum {{ $k->tgl_tutup->translatedFormat('l, j F Y') }}</b>Pendaftaran dilakukan di tautan penyelenggara, bukan di PINTAR.</span></li>
            </ol>
        </section>
        @if($terkait->isNotEmpty())
            <section style="margin-top:28px"><div class="section-head" style="margin-bottom:16px"><h2 style="font-size:20px">{{ $k->kategori->nama }} lain yang masih buka</h2></div>
                <div class="grid-cards">@foreach($terkait as $lain) @include('partials.card', ['k' => $lain]) @endforeach</div>
            </section>
        @endif
    </div>
    <aside class="side">
        <section class="panel">
            <div class="countdown">
                <div class="row"><span class="field-label">Periode pendaftaran</span>@include('partials.pill')</div>
                <div class="row">
                    <span class="big-num num" style="color:{{ $s === 'segera-berakhir' ? 'var(--danger)' : 'var(--ink)' }}">
                        {{ match($s) { 'ditutup' => 'Ditutup', 'segera-dibuka' => 'H-'.$k->hariMenujuBuka(), default => $k->hitungSisaHari() === 0 ? 'Hari ini' : $k->hitungSisaHari().' hari' } }}
                    </span>
                    <span class="num" style="color:var(--ink-2);font-size:14px">{{ $s === 'segera-dibuka' ? 'menuju dibuka' : ($s === 'ditutup' ? '' : 'tersisa') }}</span>
                </div>
                <div class="progress {{ $s === 'segera-berakhir' ? 'ending' : '' }}" role="img" aria-label="{{ $k->persenPeriode() }}% periode pendaftaran telah berlalu"><span style="width:{{ $k->persenPeriode() }}%"></span></div>
                <div class="row num" style="font-size:13px;color:var(--ink-2)"><span>Buka {{ $k->tgl_buka->translatedFormat('j M Y') }}</span><span>Tutup {{ $k->tgl_tutup->translatedFormat('j M Y') }}</span></div>
            </div>
            <div class="stack" style="margin-top:14px">
                <a class="btn btn-primary" href="{{ $k->sumber_url }}" target="_blank" rel="noopener noreferrer"><x-ico name="ext"/> Buka sumber resmi</a>
                @include('partials.remind-button', ['wide' => true])
            </div>
        </section>
        <section class="panel">
            <div class="kv">
                @if($k->tgl_pelaksanaan)<div><span class="k-ico"><x-ico name="calendar"/></span><span><small>Pelaksanaan</small><b class="num">{{ $k->tgl_pelaksanaan->translatedFormat('l, j F Y') }}</b></span></div>@endif
                <div><span class="k-ico"><x-ico name="pin"/></span><span><small>Lokasi</small><b>{{ $k->lokasi ?: 'Lokasi menyusul' }}</b></span></div>
                <div><span class="k-ico"><x-ico name="building"/></span><span><small>Penyelenggara</small><b>{{ $k->penyelenggara->nama }}</b></span></div>
                <div><span class="k-ico"><x-ico name="phone"/></span><span><small>Kontak pendaftaran</small><b>{{ $k->penyelenggara->kontak_nama ?: 'Belum dicantumkan' }}</b>
                    @if($k->penyelenggara->kontak_info)<span class="copy-row" style="margin-top:6px"><code>{{ $k->penyelenggara->kontak_info }}</code><button class="mini-btn" type="button" data-copy="{{ $k->penyelenggara->kontak_info }}"><x-ico name="copy"/> Salin</button></span>@endif
                </span></div>
                <div><span class="k-ico"><x-ico name="link"/></span><span><small>Sumber informasi</small><b>{{ $k->sumber_nama }}</b><a href="{{ $k->sumber_url }}" target="_blank" rel="noopener noreferrer" style="font-size:13px;overflow-wrap:anywhere">{{ $k->sumber_url }}</a></span></div>
            </div>
        </section>
        @if($k->terverifikasi)
            <div class="verify-box yes"><x-ico name="shield"/><div><b>Terverifikasi</b><span>Dicocokkan pengelola dengan sumber resmi{{ $k->tgl_verifikasi ? ' pada '.$k->tgl_verifikasi->translatedFormat('j M Y') : '' }}.</span></div></div>
        @else
            <div class="verify-box no"><x-ico name="alert"/><div><b>Belum diverifikasi</b><span>Pengelola belum memeriksa sumber ini. Cek keasliannya lewat tautan sumber sebelum mendaftar.</span></div></div>
        @endif
    </aside>
</div>
@endsection
