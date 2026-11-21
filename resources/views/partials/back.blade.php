@php
    $sebelumnya = url()->previous();
    $kembaliKe = ($sebelumnya && $sebelumnya !== url()->current() && str_starts_with($sebelumnya, url('/'))) ? $sebelumnya : ($fallback ?? route('beranda'));
@endphp
<a class="back-btn" href="{{ $kembaliKe }}" aria-label="Kembali ke halaman sebelumnya"><x-ico name="back"/> Kembali</a>
