@php($kodeStatus = $k->hitungStatus())
<span class="pill {{ ['dibuka' => 'open', 'segera-berakhir' => 'ending', 'segera-dibuka' => 'soon', 'ditutup' => 'closed'][$kodeStatus] }}">{{ $k->labelStatus() }}</span>
