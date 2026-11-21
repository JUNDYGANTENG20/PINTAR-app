@extends('layouts.app', ['navAktif' => 'kelola', 'judul' => $kegiatan->exists ? 'Ubah kegiatan' : 'Tambah kegiatan'])

@php
    $k = $kegiatan;
    $bidangDipilih = array_map('intval', old('bidang', $k->exists ? $k->bidangMinats->pluck('id')->all() : []));
    $tgl = fn ($f) => old($f, $k->{$f}?->format('Y-m-d'));
@endphp

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back', ['fallback' => route('admin.kegiatan.index')])
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('admin.kegiatan.index') }}">Kelola</a> / <span>{{ $k->exists ? 'Ubah' : 'Tambah' }}</span></nav>
    <h1>{{ $k->exists ? 'Ubah informasi kegiatan' : 'Tambah kegiatan baru' }}</h1>
</div></section>

<div class="wrap" style="padding-block:28px">
    @if($errors->any())
        <div class="alert alert-err" role="alert"><x-ico name="alert"/><span>Ada {{ $errors->count() }} isian yang perlu diperbaiki. Lihat pesan di bawah kolomnya.</span></div>
    @endif
    <form class="panel" method="POST" action="{{ $k->exists ? route('admin.kegiatan.update', $k) : route('admin.kegiatan.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if($k->exists) @method('PUT') @endif
        <div class="form-grid">
            <label class="field full @error('nama') invalid @enderror"><span>Nama kegiatan *</span><input class="input" name="nama" value="{{ old('nama', $k->nama) }}" required>@error('nama')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field @error('kategori_id') invalid @enderror"><span>Kategori *</span>
                <select class="select input" name="kategori_id">@foreach($kategoris as $kat)<option value="{{ $kat->id }}" @selected((int) old('kategori_id', $k->kategori_id) === $kat->id)>{{ $kat->nama }}</option>@endforeach</select>
                @error('kategori_id')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field @error('jenis') invalid @enderror"><span>Jenis *</span>
                <select class="select input" name="jenis">@foreach(\App\Models\Kegiatan::JENIS as $j)<option @selected(old('jenis', $k->jenis) === $j)>{{ $j }}</option>@endforeach</select>
                @error('jenis')<small class="err">{{ $message }}</small>@enderror</label>
            <fieldset class="f-group full"><legend>Bidang minat</legend>
                <div class="chips">@foreach($bidangs as $b)<label class="chip-check"><input type="checkbox" name="bidang[]" value="{{ $b->id }}" @checked(in_array($b->id, $bidangDipilih, true))><span>{{ $b->nama }}</span></label>@endforeach</div>
            </fieldset>
            <label class="field full @error('penyelenggara') invalid @enderror"><span>Penyelenggara *</span><input class="input" name="penyelenggara" value="{{ old('penyelenggara', $k->penyelenggara?->nama) }}" required>@error('penyelenggara')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Nama kontak</span><input class="input" name="kontak_nama" value="{{ old('kontak_nama', $k->penyelenggara?->kontak_nama) }}"></label>
            <label class="field"><span>Nomor / email kontak</span><input class="input" name="kontak_info" value="{{ old('kontak_info', $k->penyelenggara?->kontak_info) }}"></label>
            <label class="field @error('tgl_buka') invalid @enderror"><span>Pendaftaran dibuka *</span><input class="input" type="date" name="tgl_buka" value="{{ $tgl('tgl_buka') }}" required>@error('tgl_buka')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field @error('tgl_tutup') invalid @enderror"><span>Pendaftaran ditutup *</span><input class="input" type="date" name="tgl_tutup" value="{{ $tgl('tgl_tutup') }}" required>@error('tgl_tutup')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field @error('tgl_pelaksanaan') invalid @enderror"><span>Tanggal pelaksanaan</span><input class="input" type="date" name="tgl_pelaksanaan" value="{{ $tgl('tgl_pelaksanaan') }}">@error('tgl_pelaksanaan')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Lokasi</span><input class="input" name="lokasi" value="{{ old('lokasi', $k->lokasi) }}" placeholder="Contoh: Aula Fakultas Ilmu Komputer, atau Zoom Meeting"></label>
            <div class="field full @error('poster') invalid @enderror"><span>Gambar / poster kegiatan (JPG, PNG, WebP, maks. 2 MB)</span>
                <div class="pic-picker">
                    <div class="pic-prev" id="poster-prev">@if($k->poster_path)<img src="{{ $k->posterUrl() }}" alt="Poster saat ini">@else Belum ada gambar @endif</div>
                    <div class="stack" style="justify-items:start">
                        <input class="input" type="file" id="poster" name="poster" accept="image/png,image/jpeg,image/webp" style="padding:10px">
                        @if($k->poster_path)<label class="check"><input type="checkbox" name="hapus_poster" value="1"> Hapus poster saat ini</label>@endif
                        @error('poster')<small class="err">{{ $message }}</small>@enderror
                    </div>
                </div>
            </div>
            <label class="field full"><span>Manfaat / hadiah</span><input class="input" name="manfaat" value="{{ old('manfaat', $k->manfaat) }}"></label>
            <label class="field full @error('deskripsi') invalid @enderror"><span>Deskripsi *</span><textarea class="input" name="deskripsi" required>{{ old('deskripsi', $k->deskripsi) }}</textarea>@error('deskripsi')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field full"><span>Persyaratan (satu per baris)</span><textarea class="input" name="persyaratan">{{ old('persyaratan', $k->persyaratan) }}</textarea></label>
            <label class="field @error('sumber_nama') invalid @enderror"><span>Nama sumber resmi *</span><input class="input" name="sumber_nama" value="{{ old('sumber_nama', $k->sumber_nama) }}" placeholder="Contoh: Instagram resmi HIMSI" required>@error('sumber_nama')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="field @error('sumber_url') invalid @enderror"><span>Tautan sumber resmi *</span><input class="input" type="url" name="sumber_url" value="{{ old('sumber_url', $k->sumber_url) }}" placeholder="https://" required>@error('sumber_url')<small class="err">{{ $message }}</small>@enderror</label>
            <label class="check full"><input type="checkbox" name="terverifikasi" value="1" @checked(old('terverifikasi', $k->terverifikasi))> Sudah dicocokkan dengan sumber resmi (tandai terverifikasi)</label>
        </div>
        <div class="dlg-foot" style="position:static;padding-inline:0;margin-top:18px">
            <a class="btn btn-ghost" href="{{ route('admin.kegiatan.index') }}">Batal</a>
            <button class="btn btn-primary" type="submit">{{ $k->exists ? 'Simpan perubahan' : 'Tambah kegiatan' }}</button>
        </div>
    </form>
</div>
@endsection
