{{-- Tombol pengingat (FR-05). Tamu diarahkan ke halaman masuk. --}}
@php($aktif = in_array($k->id, $idPengingat ?? [], true))
@auth
    <form method="POST" action="{{ route($aktif ? 'pengingat.destroy' : 'pengingat.store', $k) }}" class="inline-form remind-form">
        @csrf
        @if($aktif) @method('DELETE') @endif
        @if(!empty($wide))
            <button class="btn btn-ghost" style="width:100%" type="submit" aria-pressed="{{ $aktif ? 'true' : 'false' }}"><x-ico name="bell"/> {{ $aktif ? 'Pengingat aktif (hapus)' : 'Ingatkan saya' }}</button>
        @else
            <button class="icon-btn" type="submit" aria-pressed="{{ $aktif ? 'true' : 'false' }}" aria-label="{{ $aktif ? 'Hapus pengingat' : 'Ingatkan saya' }}: {{ $k->nama }}"><x-ico name="bell"/></button>
        @endif
    </form>
@else
    @if(!empty($wide))
        <a class="btn btn-ghost" href="{{ route('login') }}"><x-ico name="bell"/> Masuk untuk mengatur pengingat</a>
    @else
        <a class="icon-btn" href="{{ route('login') }}" aria-label="Masuk untuk mengatur pengingat: {{ $k->nama }}"><x-ico name="bell"/></a>
    @endif
@endauth
