@extends('layouts.app', ['judul' => 'Akses ditolak'])

@section('konten')
<div class="wrap section"><div class="empty"><x-ico name="alert"/><h1 style="font-size:24px">Akses ditolak</h1><p style="color:var(--ink-2);max-width:52ch">Halaman ini khusus admin/pengelola. Masuk dengan akun admin untuk melanjutkan.</p><a class="btn btn-primary" href="{{ route('beranda') }}">Kembali ke beranda</a></div></div>
@endsection
