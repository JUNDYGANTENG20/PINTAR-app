@extends('layouts.app', ['judul' => 'Pengingatku'])

@section('konten')
<section class="page-hero has-img"><div class="wrap">
    @include('partials.back')
    <nav class="crumbs" aria-label="Jejak halaman"><a href="{{ route('beranda') }}">Beranda</a> / <span>Pengingat</span></nav>
    <h1>Pengingat deadline</h1>
    <p>Kegiatan yang kamu simpan, diurutkan dari pendaftaran yang paling cepat ditutup.</p>
</div></section>

<div class="wrap" style="padding-block:32px">
    @if($kegiatans->isEmpty())
        <div class="empty"><x-ico name="bell"/><h3>Belum ada pengingat</h3><p style="color:var(--ink-2)">Tekan ikon lonceng pada kartu kegiatan untuk menyimpannya di sini.</p><a class="btn btn-primary btn-sm" href="{{ route('kegiatan.index') }}">Jelajahi kegiatan</a></div>
    @else
        <div class="grid-cards">@foreach($kegiatans as $k) @include('partials.card', ['badge' => true, 'idPengingat' => $kegiatans->pluck('id')->all()]) @endforeach</div>
        <div class="notice" style="margin-top:20px"><x-ico name="alert"/><span>Pengingat tampil di situs ini (menu Pengingat dan banner di Beranda untuk kegiatan yang tutup dalam 3 hari). Kanal notifikasi lain seperti email atau WhatsApp belum diputuskan.</span></div>
    @endif
</div>
@endsection
