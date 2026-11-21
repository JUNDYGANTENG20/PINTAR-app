@extends('layouts.app', ['navAktif' => 'beranda'])

@section('konten')
<section class="hero has-img" aria-labelledby="hero-title">
    <div class="wrap">
        <div class="hero-grid">
            <div>
                <span class="eyebrow-chip"><x-ico name="layers"/> Pusat Informasi dan Aktivitas Relevan Mahasiswa</span>
                <h1 id="hero-title">Temukan Peluang, <em>Kembangkan Potensimu.</em></h1>
                <p class="hero-lead">PINTAR mengumpulkan info lomba, seminar, workshop, beasiswa, kepanitiaan, dan volunteer dalam satu platform. Cari sesuai minatmu, cek periode pendaftaran, lalu daftar langsung lewat sumber resminya.</p>
                <div class="hero-cta">
                    <a class="btn btn-white" href="{{ route('kegiatan.index') }}">Jelajahi Kegiatan <x-ico name="arrow"/></a>
                    <a class="btn btn-glass" href="{{ route('beasiswa') }}">Lihat Beasiswa <x-ico name="cap"/></a>
                </div>
                <div class="hero-proof">
                    <div class="src-stack" aria-hidden="true"><span style="background:#567C8D"><x-ico name="msg"/></span><span style="background:#7FA3B5"><x-ico name="mic"/></span><span style="background:#2F4156"><x-ico name="building"/></span><span style="background:#4E7485"><x-ico name="trophy"/></span></div>
                    <span>Info yang biasanya tersebar di grup WhatsApp, Instagram, dan pengumuman kampus, kini terkumpul di satu tempat.</span>
                </div>
            </div>
            @if($terdekat)
                <aside class="hero-card glass" aria-label="Deadline terdekat">
                    <div class="hc-label"><span><x-ico name="clock"/> Deadline terdekat</span>@include('partials.pill', ['k' => $terdekat])</div>
                    <div>
                        <p class="org" style="margin-bottom:6px"><x-ico :name="$terdekat->kategori->ikon"/><span>{{ $terdekat->kategori->nama }} · {{ $terdekat->penyelenggara->nama }}</span></p>
                        <p class="hc-title">{{ $terdekat->nama }}</p>
                    </div>
                    <div class="hc-count num">
                        @if($terdekat->hitungSisaHari() === 0)
                            <b>Hari ini</b><span>terakhir mendaftar</span>
                        @else
                            <b>{{ $terdekat->hitungSisaHari() }}</b><span>hari lagi · tutup {{ $terdekat->tgl_tutup->translatedFormat('j M Y') }}</span>
                        @endif
                    </div>
                    @if($berikutnya->isNotEmpty())
                        <div class="hc-next">
                            @foreach($berikutnya as $k)
                                <a href="{{ route('kegiatan.show', $k) }}"><span>{{ $k->nama }}</span><b class="num">{{ $k->teksSisaWaktu() }}</b></a>
                            @endforeach
                        </div>
                    @endif
                    <div class="hc-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('kegiatan.show', $terdekat) }}">Lihat detail</a>
                    </div>
                </aside>
            @endif
        </div>
    </div>
</section>

<div class="wrap stats">
    <div class="stats-bar glass">
        <div class="stat"><span class="stat-ico"><x-ico name="inbox"/></span><div><b class="num">{{ $statistik['aktif'] }}</b><small>Kegiatan aktif</small></div></div>
        <div class="stat"><span class="stat-ico"><x-ico name="layers"/></span><div><b class="num">{{ $statistik['kategori'] }}</b><small>Kategori kegiatan</small></div></div>
        <div class="stat"><span class="stat-ico"><x-ico name="shield"/></span><div><b class="num">{{ $statistik['terverifikasi'] }}%</b><small>Info terverifikasi</small></div></div>
        <div class="stat"><span class="stat-ico" style="background:var(--danger-bg);color:var(--danger)"><x-ico name="clock"/></span><div><b class="num">{{ $statistik['segera'] }}</b><small>Tutup ≤ 7 hari</small></div></div>
    </div>
</div>

@if($pengingatMendesak->isNotEmpty())
    <div class="wrap">
        <div class="alert alert-err reminder-banner" role="status"><x-ico name="bell"/>
            <span><b>Pengingat:</b>
                @foreach($pengingatMendesak as $k)
                    <a href="{{ route('kegiatan.show', $k) }}">{{ $k->nama }}</a> ({{ strtolower($k->teksSisaWaktu()) }})@if(!$loop->last), @endif
                @endforeach
            </span>
        </div>
    </div>
@endif

<section class="section" aria-labelledby="cat-title" style="padding-top:48px">
    <div class="wrap">
        <div class="section-head center"><p class="eyebrow">Kategori kegiatan</p><h2 id="cat-title">Mau cari kesempatan apa hari ini?</h2><p>Pilih kategori untuk langsung melihat kegiatan yang masih membuka pendaftaran.</p></div>
        <div class="cat-grid">
            @foreach($kategoris as $kat)
                <a class="cat-tile" href="{{ route('kegiatan.index', ['kategori' => [$kat->slug]]) }}" style="--g1:var(--g1-{{ $kat->slug }});--g2:var(--g2-{{ $kat->slug }});text-decoration:none">
                    <span class="big" aria-hidden="true"><x-ico :name="$kat->ikon"/></span>
                    <span class="glass-ico" aria-hidden="true"><x-ico :name="$kat->ikon"/></span>
                    <span class="cat-meta"><span class="cat-name">{{ $kat->nama }}</span><span class="pill-white num">{{ $kat->aktif_count }} aktif</span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="band" aria-labelledby="why-title">
    <div class="wrap">
        <div class="section-head center"><p class="eyebrow">Kenapa PINTAR</p><h2 id="why-title">Info kegiatan yang rapi, bukan tertumpuk di chat</h2><p>Dirancang dari hasil wawancara mahasiswa: informasi tersebar, sulit dipastikan masih buka, dan mudah terlewat.</p></div>
        <div class="why-grid">
            <div class="why"><span class="w-ico"><x-ico name="inbox"/></span><h3>Satu platform</h3><p>Kegiatan akademik dan non-akademik terkumpul dan tersusun per kategori.</p><span class="fr-tag">FR-07 · FR-10 · FR-12</span></div>
            <div class="why"><span class="w-ico"><x-ico name="search"/></span><h3>Cari sesuai minat</h3><p>Cari dengan kata kunci, jenis kegiatan, dan beberapa bidang minat sekaligus.</p><span class="fr-tag">FR-08 · FR-09 · FR-13 · FR-14</span></div>
            <div class="why"><span class="w-ico"><x-ico name="bell"/></span><h3>Deadline terlihat</h3><p>Status dan sisa hari pendaftaran tampil di setiap kartu, plus pengingat sebelum tutup.</p><span class="fr-tag">FR-04 · FR-05</span></div>
            <div class="why"><span class="w-ico"><x-ico name="shield"/></span><h3>Sumber resmi</h3><p>Setiap info menautkan sumber aslinya dan diberi tanda bila sudah diverifikasi.</p><span class="fr-tag">FR-15 · FR-16 · FR-17</span></div>
        </div>
        <div class="planner-wrap">
            <form class="planner glass" method="GET" action="{{ route('kegiatan.index') }}" role="search" aria-labelledby="planner-title">
                <h3 id="planner-title">Kegiatan apa yang kamu cari?</h3>
                <p class="sub">Pilih lebih dari satu bidang minat bila perlu.</p>
                <div class="planner-row">
                    <label class="field"><span>Kata kunci</span><span class="input-ico"><x-ico name="search"/><input class="input" name="q" type="search" placeholder="Contoh: UI/UX, data, beasiswa prestasi" autocomplete="off"></span></label>
                    <label class="field"><span>Jenis kegiatan</span><select class="select input" name="jenis"><option value="">Semua jenis</option>@foreach(\App\Models\Kegiatan::JENIS as $j)<option>{{ $j }}</option>@endforeach</select></label>
                    <button class="btn btn-primary" type="submit" style="min-height:48px"><x-ico name="search"/> Cari kegiatan</button>
                </div>
                <div class="chips-row"><span class="field-label" id="pb-label">Bidang minat</span>
                    <div class="chips" role="group" aria-labelledby="pb-label">
                        @foreach(\App\Models\BidangMinat::query()->orderBy('id')->get() as $b)
                            <label class="chip-check"><input type="checkbox" name="bidang[]" value="{{ $b->id }}"><span>{{ $b->nama }}</span></label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section after-band" aria-labelledby="end-title">
    <div class="wrap">
        <div class="section-head"><div><p class="eyebrow">Pengingat deadline</p><h2 id="end-title">Segera ditutup, jangan sampai terlewat</h2><p>Pendaftaran yang berakhir dalam 7 hari. Tekan ikon lonceng untuk menyimpan pengingat.</p></div><a class="link-arrow" href="{{ route('kegiatan.index', ['status' => 'segera-berakhir']) }}">Lihat semua yang segera ditutup <x-ico name="arrow"/></a></div>
        @if($segeraBerakhir->isNotEmpty())
            <div class="grid-cards">@foreach($segeraBerakhir as $k) @include('partials.card', ['badge' => true]) @endforeach</div>
        @else
            <p style="color:var(--ink-2)">Tidak ada pendaftaran yang tutup dalam 7 hari.</p>
        @endif
    </div>
</section>

<section class="section" style="padding-top:0" aria-labelledby="feat-title">
    <div class="wrap">
        <div class="section-head"><div><p class="eyebrow">Kegiatan pilihan</p><h2 id="feat-title">Masih buka, waktu masih cukup</h2></div><a class="link-arrow" href="{{ route('kegiatan.index') }}">Lihat semua kegiatan <x-ico name="arrow"/></a></div>
        <div class="grid-cards">@foreach($pilihan as $k) @include('partials.card') @endforeach</div>
    </div>
</section>

<section class="section" style="padding-top:0" aria-labelledby="sch-title">
    <div class="wrap">
        <div class="section-head"><div><p class="eyebrow">Beasiswa</p><h2 id="sch-title">Beasiswa yang sedang dibuka</h2><p>Cek kriteria sebelum mendaftar supaya tahu beasiswa mana yang sesuai dengan kondisimu.</p></div><a class="link-arrow" href="{{ route('beasiswa') }}">Semua beasiswa <x-ico name="arrow"/></a></div>
        <div class="sch-layout">
            <div class="sch-grid">
                @foreach($beasiswa as $k)
                    @php($unggulan = $k->hitungStatus() === 'segera-berakhir' && $loop->first)
                    <article class="sch{{ $unggulan ? ' featured' : '' }}">
                        @if($unggulan)<span class="flag">Segera berakhir</span>@endif
                        <div class="sch-head"><span class="s-ico"><x-ico name="cap"/></span><div><h3>{{ $k->nama }}</h3><p class="org"><span>{{ $k->penyelenggara->nama }}</span></p></div></div>
                        @if($k->manfaat)<p class="benefit">{{ $k->manfaat }}</p>@endif
                        <ul class="ticks">@foreach(array_slice($k->daftarPersyaratan(), 0, 3) as $syarat)<li><x-ico name="check"/><span>{{ $syarat }}</span></li>@endforeach</ul>
                        <p class="period num"><x-ico name="calendar"/> {{ $k->tgl_buka->translatedFormat('j M Y') }} – {{ $k->tgl_tutup->translatedFormat('j M Y') }}</p>
                        <div>@include('partials.pill') @if($k->terverifikasi)<span class="vbadge"><x-ico name="badge"/> Terverifikasi</span>@else<span class="vbadge no">Belum diverifikasi</span>@endif</div>
                        <div class="actions">
                            <a class="btn {{ $unggulan ? 'btn-primary' : 'btn-ghost' }} btn-sm" href="{{ route('kegiatan.show', $k) }}">Lihat persyaratan lengkap</a>
                            <a class="link-arrow" style="justify-content:center;font-size:13px" href="{{ $k->sumber_url }}" target="_blank" rel="noopener noreferrer"><x-ico name="ext"/> Sumber resmi</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="faq">
                <h3>Pertanyaan umum</h3>
                <details><summary>Apakah pendaftaran dilakukan di PINTAR? <x-ico name="chev"/></summary><p>Tidak. PINTAR merangkum informasinya, lalu tombol “Buka sumber resmi” mengarahkanmu ke halaman penyelenggara untuk mendaftar.</p></details>
                <details><summary>Apa arti tanda Terverifikasi? <x-ico name="chev"/></summary><p>Pengelola sudah mencocokkan informasi dengan sumber resmi penyelenggara.</p></details>
                <details><summary>Bagaimana pengingat deadline bekerja? <x-ico name="chev"/></summary><p>Masuk dengan akun mahasiswa, lalu tekan ikon lonceng pada kegiatan. Daftarnya ada di menu Pengingat, dan kegiatan yang tutup dalam 3 hari muncul di beranda.</p></details>
                <details><summary>Siapa yang memperbarui informasi? <x-ico name="chev"/></summary><p>Admin/pengelola menambah, memperbarui, dan menghapus informasi supaya tidak ada info kedaluwarsa.</p></details>
            </div>
        </div>
    </div>
</section>

<section class="wrap" aria-label="Ajakan">
    <div class="cta-banner">
        <div><h2>Kesempatan berikutnya sudah menunggu.</h2><p>Simpan pengingat untuk kegiatan incaranmu dan daftar sebelum pendaftarannya ditutup.</p></div>
        <div class="hero-cta"><a class="btn btn-white" href="{{ route('kegiatan.index') }}">Mulai jelajahi <x-ico name="arrow"/></a>
            @auth<a class="btn btn-glass" href="{{ route('pengingat.index') }}"><x-ico name="bell"/> Lihat pengingatku</a>@else<a class="btn btn-glass" href="{{ route('register') }}"><x-ico name="user"/> Buat akun mahasiswa</a>@endauth
        </div>
    </div>
</section>
@endsection
