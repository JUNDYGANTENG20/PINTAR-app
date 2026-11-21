@extends('layouts.app', ['navAktif' => 'tentang', 'judul' => 'Tentang'])

@php
$fr = [
 ['FR-01','Menampilkan daftar informasi beasiswa yang tersedia.','Halaman Beasiswa, bagian beasiswa di Beranda'],
 ['FR-02','Menampilkan persyaratan dan kriteria setiap beasiswa.','Kartu beasiswa, Detail › Persyaratan & kriteria'],
 ['FR-03','Menyediakan sumber resmi penyedia untuk setiap beasiswa.','Tombol “Buka sumber resmi” pada beasiswa'],
 ['FR-04','Menampilkan periode pendaftaran setiap kegiatan.','Tanggal tutup di kartu, panel Periode pendaftaran di Detail'],
 ['FR-05','Memberi informasi/pengingat saat pendaftaran akan berakhir.','Ikon lonceng, halaman Pengingat, banner pengingat di Beranda, bagian Segera ditutup'],
 ['FR-06','Menampilkan kontak pihak penyelenggara.','Detail › Kontak pendaftaran (dengan tombol Salin)'],
 ['FR-07','Memuat informasi kegiatan secara terstruktur.','Setiap kegiatan punya kategori, jenis, bidang, periode, sumber'],
 ['FR-08','Pencarian berdasarkan kata kunci.','Kolom pencarian di Beranda dan Jelajahi'],
 ['FR-09','Pencarian berdasarkan jenis kegiatan.','Pilihan Jenis kegiatan (Akademik / Non-akademik)'],
 ['FR-10','Informasi akademik dan non-akademik dalam satu platform.','Jelajahi Kegiatan memuat semua kategori'],
 ['FR-11','Menampilkan detail kegiatan yang dipilih.','Halaman Detail Kegiatan'],
 ['FR-12','Kategori beasiswa, lomba, seminar, workshop, kepanitiaan.','Kartu kategori di Beranda (+ Volunteer)'],
 ['FR-13','Filter berdasarkan bidang atau kategori.','Panel Filter di Jelajahi'],
 ['FR-14','Memilih lebih dari satu kategori/bidang minat.','Checkbox kategori & chip bidang minat (multi-pilih)'],
 ['FR-15','Menampilkan sumber informasi resmi yang mempublikasikan.','Detail › Sumber informasi'],
 ['FR-16','Tanda verifikasi untuk info dari sumber terpercaya.','Lencana Terverifikasi; admin memverifikasi di halaman Kelola'],
 ['FR-17','Tautan ke sumber asli untuk cek keaslian.','Tautan sumber di Detail dan Beasiswa'],
];
@endphp

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back')
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <span>Tentang</span></nav>
    <h1>Tentang PINTAR</h1>
    <p>Pusat Informasi dan Aktivitas Relevan Mahasiswa. Sistem informasi Kelompok 5, mata kuliah Analisis dan Perancangan Sistem Informasi.</p>
</div></section>

<div class="wrap" style="padding-block:32px;display:grid;grid-template-columns:minmax(0,1fr);gap:18px">
    <div class="about-grid">
        <section class="panel prose"><h2><x-ico name="alert"/> Masalah yang diselesaikan</h2>
            <p>Info lomba, seminar, workshop, dan beasiswa umumnya dibagikan di grup WhatsApp kelas, angkatan, dan prodi. Karena tersebar, mahasiswa harus mencari dan menyaring sendiri.</p>
            <p>Pesan baru membuat info lama tertumpuk, sehingga sulit tahu apakah pendaftaran masih buka. Akibatnya banyak kesempatan terlewat, padahal kegiatan ini penting untuk pengalaman, prestasi, poin SKP, relasi, dan biaya kuliah.</p></section>
        <section class="panel"><h2><x-ico name="check"/> Tujuan sistem</h2>
            <ul class="list"><li>Memusatkan info lomba, seminar, workshop, beasiswa, volunteer, dan kegiatan lain.</li><li>Memudahkan mahasiswa menemukan info sesuai minat dan kebutuhan.</li><li>Mengurangi risiko kesempatan terlewat karena info tertumpuk.</li><li>Membantu meningkatkan partisipasi mahasiswa.</li><li>Membuat info lebih terorganisir dan mudah diakses.</li></ul></section>
        <section class="panel"><h2><x-ico name="user"/> Mahasiswa</h2><p style="color:var(--ink-2)">Mencari, melihat, dan mengikuti informasi kegiatan. Dengan akun, mahasiswa bisa menyimpan pengingat deadline.</p></section>
        <section class="panel"><h2><x-ico name="shield"/> Admin / pengelola</h2><p style="color:var(--ink-2)">Memasukkan, memperbarui, menghapus, dan memverifikasi informasi kegiatan agar tetap relevan.</p></section>
    </div>
    <section class="panel" id="fr"><h2><x-ico name="layers"/> Kebutuhan fungsional</h2>
        <p style="margin-bottom:14px">ID mengikuti dokumen analisis kebutuhan (Tugas 2). Kolom kanan menunjukkan di mana kebutuhan diwujudkan.</p>
        <div class="table-scroll"><table><thead><tr><th>ID</th><th>Kebutuhan fungsional</th><th>Diwujudkan di</th></tr></thead><tbody>
            @foreach($fr as [$id, $isi, $di])<tr><td class="id">{{ $id }}</td><td>{{ $isi }}</td><td style="color:var(--ink-2)">{{ $di }}</td></tr>@endforeach
        </tbody></table></div>
    </section>
</div>
@endsection
