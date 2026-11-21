@extends('layouts.app', ['judul' => 'Sesi kedaluwarsa'])

@section('konten')
<div class="wrap section"><div class="empty"><x-ico name="alert"/><h1 style="font-size:24px">Sesi kedaluwarsa</h1><p style="color:var(--ink-2);max-width:52ch">Halaman terlalu lama dibuka. Muat ulang halaman, lalu coba lagi.</p><a class="btn btn-primary" href="{{ route('beranda') }}">Kembali ke beranda</a></div></div>
@endsection
