@extends('layouts.app', ['navAktif' => 'jelajahi', 'judul' => 'Jelajahi Kegiatan'])

@php
    $katDipilih = $filter['kategori'] ?? [];
    $bidDipilih = array_map('intval', $filter['bidang'] ?? []);
    $adaFilter = collect($filter)->except('urut')->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])->isNotEmpty();
@endphp

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back')
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <span>Jelajahi Kegiatan</span></nav>
    <h1>Jelajahi Kegiatan</h1>
    <p>Semua info lomba, seminar, workshop, beasiswa, kepanitiaan, dan volunteer. Gabungkan beberapa filter untuk mempersempit hasil.</p>
    <form class="page-search input-ico" role="search" method="GET" action="{{ route('kegiatan.index') }}">
        <x-ico name="search"/><label class="sr" for="cari">Cari kegiatan</label>
        <input class="input" id="cari" name="q" type="search" placeholder="Cari nama kegiatan, penyelenggara, atau topik" value="{{ $filter['q'] ?? '' }}" autocomplete="off">
        @foreach($katDipilih as $k)<input type="hidden" name="kategori[]" value="{{ $k }}">@endforeach
        @foreach($bidDipilih as $b)<input type="hidden" name="bidang[]" value="{{ $b }}">@endforeach
        @foreach(['jenis', 'status', 'terverifikasi', 'urut'] as $f)@if(!empty($filter[$f]))<input type="hidden" name="{{ $f }}" value="{{ $filter[$f] }}">@endif @endforeach
    </form>
</div></section>

<div class="wrap explore">
    <aside class="filters" id="filters" aria-label="Filter kegiatan">
        <h2>Filter <button class="btn btn-ghost btn-sm filters-toggle" type="button" id="filters-toggle" aria-expanded="false"><x-ico name="filter"/> Tampilkan</button></h2>
        <form class="f-body" method="GET" action="{{ route('kegiatan.index') }}" data-autosubmit>
            @if(!empty($filter['q']))<input type="hidden" name="q" value="{{ $filter['q'] }}">@endif
            @if(!empty($filter['urut']))<input type="hidden" name="urut" value="{{ $filter['urut'] }}">@endif
            <label class="field"><span>Jenis kegiatan</span>
                <select class="select input" name="jenis"><option value="">Semua jenis</option>
                    @foreach(\App\Models\Kegiatan::JENIS as $j)<option @selected(($filter['jenis'] ?? '') === $j)>{{ $j }}</option>@endforeach
                </select></label>
            <fieldset class="f-group"><legend>Kategori (boleh lebih dari satu)</legend>
                @foreach($kategoris as $kat)
                    <label class="check"><input type="checkbox" name="kategori[]" value="{{ $kat->slug }}" @checked(in_array($kat->slug, $katDipilih, true))> {{ $kat->nama }}<span class="cnt num">{{ $kat->kegiatans_count }}</span></label>
                @endforeach
            </fieldset>
            <fieldset class="f-group"><legend>Bidang minat (boleh lebih dari satu)</legend>
                <div class="chips">
                    @foreach($bidangs as $b)
                        <label class="chip-check"><input type="checkbox" name="bidang[]" value="{{ $b->id }}" @checked(in_array($b->id, $bidDipilih, true))><span>{{ $b->nama }}</span></label>
                    @endforeach
                </div>
            </fieldset>
            <label class="field"><span>Status pendaftaran</span>
                <select class="select input" name="status"><option value="">Semua status</option>
                    @foreach(\App\Models\Kegiatan::STATUS as $kode => $label)<option value="{{ $kode }}" @selected(($filter['status'] ?? '') === $kode)>{{ $label }}</option>@endforeach
                </select></label>
            <label class="check"><input type="checkbox" name="terverifikasi" value="1" @checked(!empty($filter['terverifikasi']))> Hanya yang terverifikasi</label>
            <button class="btn btn-primary btn-sm" type="submit">Terapkan filter</button>
            @if($adaFilter)<a class="btn btn-ghost btn-sm" href="{{ route('kegiatan.index') }}"><x-ico name="reset"/> Atur ulang filter</a>@endif
        </form>
    </aside>

    <div>
        <div class="toolbar">
            <p class="count num">{{ $kegiatans->total() }} kegiatan</p>
            <form method="GET" action="{{ route('kegiatan.index') }}" data-autosubmit class="field" style="grid-auto-flow:column;align-items:center;gap:8px">
                @foreach(request()->except(['urut', 'page']) as $nama => $nilai)
                    @if(is_array($nilai)) @foreach($nilai as $v)<input type="hidden" name="{{ $nama }}[]" value="{{ $v }}">@endforeach
                    @else<input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">@endif
                @endforeach
                <label for="urut"><span class="field-label">Urutkan</span></label>
                <select class="select input" id="urut" name="urut">
                    <option value="deadline" @selected(($filter['urut'] ?? 'deadline') === 'deadline')>Deadline terdekat</option>
                    <option value="baru" @selected(($filter['urut'] ?? '') === 'baru')>Baru dibuka</option>
                    <option value="az" @selected(($filter['urut'] ?? '') === 'az')>Nama A–Z</option>
                </select>
                <noscript><button class="btn btn-ghost btn-sm" type="submit">Urutkan</button></noscript>
            </form>
        </div>

        @if($kegiatans->isEmpty())
            <div class="empty"><x-ico name="search"/><h3>Belum ada kegiatan yang cocok</h3><p style="color:var(--ink-2)">Coba kurangi filter atau gunakan kata kunci lain.</p><a class="btn btn-primary btn-sm" href="{{ route('kegiatan.index') }}">Atur ulang filter</a></div>
        @else
            <div class="grid-cards">@foreach($kegiatans as $k) @include('partials.card', ['badge' => true]) @endforeach</div>
            {{ $kegiatans->links('partials.pagination') }}
        @endif
    </div>
</div>
@endsection
