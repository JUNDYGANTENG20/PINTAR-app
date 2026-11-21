@php($s = $k->hitungStatus())
@php($sisa = $k->hitungSisaHari())
<article class="card">
    <a class="card-link" href="{{ route('kegiatan.show', $k) }}" aria-label="Lihat detail {{ $k->nama }}"></a>
    <div class="thumb{{ $k->poster_path ? ' has-pic' : '' }}" style="--g1:var(--g1-{{ $k->kategori->slug }});--g2:var(--g2-{{ $k->kategori->slug }}){!! $k->poster_path ? ";--pic:url('".e($k->posterUrl())."')" : '' !!}">
        <div class="t-tags">
            <span class="tag-w"><x-ico :name="$k->kategori->ikon"/> {{ $k->kategori->nama }}</span>
            @if($k->terverifikasi)<span class="tag-w ver"><x-ico name="badge"/> Terverifikasi</span>@endif
        </div>
        @if(!empty($badge) && $s === 'segera-berakhir')
            <div class="t-badges"><span class="deadline-badge num">{{ $sisa === 0 ? 'Hari ini' : 'H-'.$sisa }}</span></div>
        @endif
        @unless($k->poster_path)<span class="t-ico"><x-ico :name="$k->kategori->ikon"/></span>@endunless
    </div>
    <div class="card-body">
        <h3>{{ $k->nama }}</h3>
        <p class="org"><x-ico name="building"/><span>{{ $k->penyelenggara->nama }}</span></p>
        <div class="meta"><span><x-ico name="calendar"/> Tutup {{ $k->tgl_tutup->translatedFormat('j M Y') }}</span>@include('partials.pill')</div>
    </div>
    <div class="card-foot">
        <span class="remain {{ ['dibuka' => 'open', 'segera-berakhir' => 'ending', 'segera-dibuka' => 'soon', 'ditutup' => 'closed'][$s] }} num">{{ $k->teksSisaWaktu() }}</span>
        @include('partials.remind-button')
    </div>
</article>
