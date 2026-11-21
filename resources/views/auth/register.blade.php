@extends('layouts.app', ['judul' => 'Daftar akun'])

@section('konten')
<div class="wrap auth-wrap">
    <form class="auth-card" method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div><h1>Buat akun mahasiswa</h1><p class="sub">Akun dipakai untuk menyimpan pengingat deadline kegiatan.</p></div>
        @foreach([
            ['name', 'Nama lengkap', 'text', 'name'],
            ['nim', 'NIM', 'text', 'off'],
            ['prodi', 'Program studi', 'text', 'off'],
            ['email', 'Email', 'email', 'email'],
        ] as [$f, $label, $type, $ac])
            <label class="field @error($f) invalid @enderror"><span>{{ $label }}</span>
                <input class="input" type="{{ $type }}" name="{{ $f }}" value="{{ old($f) }}" autocomplete="{{ $ac }}" @if($f === 'nim') inputmode="numeric" @endif required>
                @error($f)<small class="err">{{ $message }}</small>@enderror</label>
        @endforeach
        <label class="field @error('password') invalid @enderror"><span>Kata sandi (minimal 8 karakter)</span>
            <input class="input" type="password" name="password" autocomplete="new-password" required>
            @error('password')<small class="err">{{ $message }}</small>@enderror</label>
        <label class="field"><span>Ulangi kata sandi</span>
            <input class="input" type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="btn btn-primary" type="submit">Daftar</button>
        <p class="sub">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </form>
</div>
@endsection
