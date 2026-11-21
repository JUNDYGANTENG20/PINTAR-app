@extends('layouts.app', ['navAktif' => 'kelola', 'judul' => 'Kelola Kegiatan'])

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back')
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <span>Kelola</span></nav>
    <h1>Kelola Informasi Kegiatan</h1>
    <p>Tambah, perbarui, hapus, dan verifikasi informasi agar tidak ada yang kedaluwarsa.</p>
</div></section>

<div class="wrap" style="padding-bottom:32px">
    <div class="admin-stats">
        @foreach($ringkasan as $label => $n)<div class="astat"><b class="num">{{ $n }}</b><small>{{ $label }}</small></div>@endforeach
    </div>
    <div class="toolbar">
        <form method="GET" action="{{ route('admin.kegiatan.index') }}" class="input-ico" style="flex:1;min-width:220px;max-width:420px" role="search">
            <x-ico name="search"/><label class="sr" for="adm-q">Cari kegiatan</label>
            <input class="input" id="adm-q" name="q" type="search" placeholder="Cari nama atau penyelenggara" value="{{ $q }}">
        </form>
        <a class="btn btn-primary btn-sm" href="{{ route('admin.kegiatan.create') }}"><x-ico name="plus"/> Tambah kegiatan</a>
    </div>
    <div class="table-scroll">
        <table class="admin-table">
            <thead><tr><th>Kegiatan</th><th>Kategori</th><th>Periode</th><th>Status</th><th>Verifikasi</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($kegiatans as $k)
                <tr>
                    <td><div class="t-cell">
                        @if($k->poster_path)<img class="t-thumb" src="{{ $k->posterUrl() }}" alt="">@else<span class="t-thumb" style="display:grid;place-items:center;color:var(--ink-2)"><x-ico :name="$k->kategori->ikon"/></span>@endif
                        <div><a class="t-name" href="{{ route('kegiatan.show', $k) }}">{{ $k->nama }}</a><small>{{ $k->penyelenggara->nama }}</small></div>
                    </div></td>
                    <td>{{ $k->kategori->nama }}<br><small>{{ $k->jenis }}</small></td>
                    <td class="num" style="white-space:nowrap">{{ $k->tgl_buka->translatedFormat('j M Y') }}<br><small>s.d. {{ $k->tgl_tutup->translatedFormat('j M Y') }}</small></td>
                    <td>@include('partials.pill')</td>
                    <td>
                        <form method="POST" action="{{ route('admin.kegiatan.verifikasi', $k) }}" class="inline-form">@csrf @method('PATCH')
                            <button class="vbadge {{ $k->terverifikasi ? '' : 'no' }}" style="border:0;cursor:pointer" type="submit" title="{{ $k->terverifikasi ? 'Klik untuk membatalkan verifikasi' : 'Klik setelah mencocokkan dengan sumber resmi' }}">
                                @if($k->terverifikasi)<x-ico name="badge"/> Ya @else Belum · verifikasi @endif
                            </button>
                        </form>
                    </td>
                    <td><div class="row-actions">
                        <a class="icon-btn" href="{{ route('admin.kegiatan.edit', $k) }}" aria-label="Ubah {{ $k->nama }}"><x-ico name="pencil"/></a>
                        <form method="POST" action="{{ route('admin.kegiatan.destroy', $k) }}" class="inline-form" data-confirm="Hapus “{{ $k->nama }}”? Tindakan ini tidak bisa dibatalkan.">@csrf @method('DELETE')
                            <button class="icon-btn danger" type="submit" aria-label="Hapus {{ $k->nama }}"><x-ico name="trash"/></button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:var(--ink-2)">Tidak ada kegiatan yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $kegiatans->links('partials.pagination') }}
</div>
@endsection
