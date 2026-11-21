<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ isset($judul) ? $judul.' · PINTAR' : 'PINTAR · Pusat Informasi dan Aktivitas Relevan Mahasiswa' }}</title>
    <meta name="description" content="PINTAR mengumpulkan info lomba, seminar, workshop, beasiswa, kepanitiaan, dan volunteer mahasiswa dalam satu platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="{{ asset('css/pintar.css') }}?v=2">
</head>
<body>
@php($navAktif = $navAktif ?? '')
<header class="site-head">
    <div class="wrap head-row">
        <a class="brand" href="{{ route('beranda') }}" aria-label="PINTAR, ke beranda">
            <span class="brand-mark" aria-hidden="true">P</span>
            <span class="brand-text"><b>PINTAR</b><small>Pusat Informasi &amp; Aktivitas Mahasiswa</small></span>
        </a>
        <nav class="nav" id="nav" aria-label="Navigasi utama">
            <a href="{{ route('beranda') }}" @if($navAktif==='beranda') aria-current="page" @endif>Beranda</a>
            <a href="{{ route('kegiatan.index') }}" @if($navAktif==='jelajahi') aria-current="page" @endif>Jelajahi Kegiatan</a>
            <a href="{{ route('beasiswa') }}" @if($navAktif==='beasiswa') aria-current="page" @endif>Beasiswa</a>
            <a href="{{ route('tentang') }}" @if($navAktif==='tentang') aria-current="page" @endif>Tentang</a>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.kegiatan.index') }}" @if($navAktif==='kelola') aria-current="page" @endif>Kelola</a>
            @endif
        </nav>
        <div class="head-actions">
            @auth
                <a class="icon-btn" href="{{ route('pengingat.index') }}" aria-label="Pengingat deadline ({{ $jumlahPengingat }})"><x-ico name="bell"/>@if($jumlahPengingat)<span class="nav-badge num">{{ $jumlahPengingat }}</span>@endif</a>
                <details class="user-menu">
                    <summary class="btn btn-primary"><x-ico name="user"/><span class="login-label">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span></summary>
                    <div class="menu">
                        <div class="who"><b>{{ auth()->user()->name }}</b>{{ auth()->user()->isAdmin() ? 'Admin/pengelola' : 'Mahasiswa'.(auth()->user()->nim ? ' · '.auth()->user()->nim : '') }}</div>
                        <a href="{{ route('pengingat.index') }}"><x-ico name="bell"/> Pengingatku</a>
                        @if(auth()->user()->isAdmin())<a href="{{ route('admin.kegiatan.index') }}"><x-ico name="layers"/> Kelola kegiatan</a>@endif
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><x-ico name="back"/> Keluar</button></form>
                    </div>
                </details>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}"><x-ico name="user"/><span class="login-label">Masuk</span></a>
            @endauth
            <button class="icon-btn menu-btn" id="menu-btn" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="nav"><x-ico name="menu"/></button>
        </div>
    </div>
</header>

<main id="app">
    @if(session('sukses') || $errors->has('umum'))
        <div class="wrap">
            @if(session('sukses'))<div class="alert alert-ok" role="status"><x-ico name="check"/><span>{{ session('sukses') }}</span></div>@endif
            @error('umum')<div class="alert alert-err" role="alert"><x-ico name="alert"/><span>{{ $message }}</span></div>@enderror
        </div>
    @endif
    @yield('konten')
</main>

<footer class="site-foot">
    <div class="wrap">
        <div class="foot-grid">
            <div class="stack">
                <a class="brand" href="{{ route('beranda') }}"><span class="brand-mark" aria-hidden="true">P</span><span class="brand-text"><b>PINTAR</b><small>Pusat Informasi dan Aktivitas Relevan Mahasiswa</small></span></a>
                <p style="max-width:44ch">Satu tempat untuk info lomba, seminar, workshop, beasiswa, kepanitiaan, dan volunteer. Setiap informasi menautkan ke sumber resminya.</p>
            </div>
            <div><h4>Jelajahi</h4><ul>
                <li><a href="{{ route('kegiatan.index') }}">Semua kegiatan</a></li>
                <li><a href="{{ route('beasiswa') }}">Beasiswa</a></li>
                <li><a href="{{ route('kegiatan.index', ['kategori' => ['lomba']]) }}">Lomba</a></li>
                <li><a href="{{ route('kegiatan.index', ['kategori' => ['kepanitiaan']]) }}">Kepanitiaan</a></li>
            </ul></div>
            <div><h4>Informasi</h4><ul>
                <li><a href="{{ route('tentang') }}">Tentang PINTAR</a></li>
                <li><a href="{{ route('tentang') }}#fr">Kebutuhan fungsional</a></li>
                @guest<li><a href="{{ route('register') }}">Daftar akun mahasiswa</a></li>@endguest
            </ul></div>
        </div>
        <div class="foot-note"><span>Sistem Informasi PINTAR · Kelompok 5 · APSI 3B-SI</span><span>Pendaftaran kegiatan dilakukan di situs resmi penyelenggara.</span></div>
    </div>
</footer>

@include('partials.icons')
<script src="{{ asset('js/pintar.js') }}?v=1" defer></script>
</body>
</html>
