@extends('layouts.app', ['judul' => 'Halaman tidak ditemukan'])

@section('konten')
<div class="wrap section"><div class="empty"><x-ico name="alert"/><h1 style="font-size:24px">Halaman tidak ditemukan</h1><p style="color:var(--ink-2);max-width:52ch">Informasi ini mungkin sudah dihapus pengelola atau alamatnya salah.</p><a class="btn btn-primary" href="{{ route('beranda') }}">Kembali ke beranda</a></div></div>
@endsection
