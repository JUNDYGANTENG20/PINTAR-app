@extends('layouts.app', ['judul' => 'Masuk'])

@section('konten')
<div class="wrap auth-wrap">
    <form class="auth-card" method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div><h1>Masuk ke PINTAR</h1><p class="sub">Masuk untuk menyimpan pengingat deadline. Admin masuk untuk mengelola informasi kegiatan.</p></div>
        <label class="field @error('email') invalid @enderror"><span>Email</span>
            <input class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')<small class="err">{{ $message }}</small>@enderror</label>
        <label class="field @error('password') invalid @enderror"><span>Kata sandi</span>
            <input class="input" type="password" name="password" autocomplete="current-password" required>
            @error('password')<small class="err">{{ $message }}</small>@enderror</label>
        <label class="check"><input type="checkbox" name="ingat" value="1"> Ingat saya di perangkat ini</label>
        <button class="btn btn-primary" type="submit">Masuk</button>
        <p class="sub">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai mahasiswa</a></p>
    </form>
</div>
@endsection
