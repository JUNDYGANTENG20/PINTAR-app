<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'nama', 'kategori_id', 'penyelenggara_id', 'jenis', 'deskripsi', 'persyaratan', 'manfaat',
    'tgl_buka', 'tgl_tutup', 'tgl_pelaksanaan', 'lokasi', 'sumber_nama', 'sumber_url',
    'poster_path', 'terverifikasi', 'tgl_verifikasi', 'dibuat_oleh',
])]
class Kegiatan extends Model
{
    public const JENIS = ['Akademik', 'Non-akademik'];

    public const STATUS = [
        'dibuka' => 'Dibuka',
        'segera-berakhir' => 'Segera berakhir',
        'segera-dibuka' => 'Segera dibuka',
        'ditutup' => 'Ditutup',
    ];

    /** Batas "segera berakhir": pendaftaran tutup dalam 7 hari. */
    public const HARI_SEGERA_BERAKHIR = 7;

    protected function casts(): array
    {
        return [
            'tgl_buka' => 'date',
            'tgl_tutup' => 'date',
            'tgl_pelaksanaan' => 'date',
            'tgl_verifikasi' => 'date',
            'terverifikasi' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function penyelenggara(): BelongsTo
    {
        return $this->belongsTo(Penyelenggara::class);
    }

    public function bidangMinats(): BelongsToMany
    {
        return $this->belongsToMany(BidangMinat::class);
    }

    public function pengingats(): HasMany
    {
        return $this->hasMany(Pengingat::class);
    }

    /** Sisa hari sampai tanggal tutup (0 = tutup hari ini, negatif = sudah lewat). FR-04 */
    public function hitungSisaHari(): int
    {
        return (int) Carbon::today()->diffInDays($this->tgl_tutup->copy()->startOfDay(), false);
    }

    public function hariMenujuBuka(): int
    {
        return (int) Carbon::today()->diffInDays($this->tgl_buka->copy()->startOfDay(), false);
    }

    /** Status pendaftaran: dibuka | segera-berakhir | segera-dibuka | ditutup. FR-04, FR-05 */
    public function hitungStatus(): string
    {
        if ($this->hariMenujuBuka() > 0) {
            return 'segera-dibuka';
        }
        $sisa = $this->hitungSisaHari();
        if ($sisa < 0) {
            return 'ditutup';
        }

        return $sisa <= self::HARI_SEGERA_BERAKHIR ? 'segera-berakhir' : 'dibuka';
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->hitungStatus()];
    }

    public function teksSisaWaktu(): string
    {
        return match ($this->hitungStatus()) {
            'segera-dibuka' => 'Dibuka dalam '.$this->hariMenujuBuka().' hari',
            'ditutup' => 'Pendaftaran ditutup',
            default => $this->hitungSisaHari() === 0 ? 'Berakhir hari ini' : 'Sisa '.$this->hitungSisaHari().' hari',
        };
    }

    /** Persen periode pendaftaran yang sudah berlalu, untuk progress bar. */
    public function persenPeriode(): int
    {
        $status = $this->hitungStatus();
        if ($status === 'segera-dibuka') {
            return 0;
        }
        if ($status === 'ditutup') {
            return 100;
        }
        $total = max(1, $this->tgl_buka->diffInDays($this->tgl_tutup));
        $lewat = min($total, max(0, $this->tgl_buka->diffInDays(Carbon::today())));

        return (int) round($lewat / $total * 100);
    }

    /** FR-02: persyaratan disimpan satu per baris. */
    public function daftarPersyaratan(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->persyaratan))));
    }

    public function posterUrl(): ?string
    {
        return $this->poster_path ? asset('storage/'.$this->poster_path) : null;
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereDate('tgl_tutup', '>=', Carbon::today());
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        $today = Carbon::today();
        $batas = $today->copy()->addDays(self::HARI_SEGERA_BERAKHIR);

        return match ($status) {
            'segera-dibuka' => $query->whereDate('tgl_buka', '>', $today),
            'ditutup' => $query->whereDate('tgl_tutup', '<', $today),
            'segera-berakhir' => $query->whereDate('tgl_buka', '<=', $today)->whereDate('tgl_tutup', '>=', $today)->whereDate('tgl_tutup', '<=', $batas),
            'dibuka' => $query->whereDate('tgl_buka', '<=', $today)->whereDate('tgl_tutup', '>', $batas),
            default => $query,
        };
    }

    /** Urutkan: yang masih buka dulu (deadline terdekat), yang sudah tutup di belakang. */
    public function scopeUrutDeadline(Builder $query): Builder
    {
        return $query->orderByRaw('CASE WHEN tgl_tutup < ? THEN 1 ELSE 0 END', [Carbon::today()->toDateString()])
            ->orderBy('tgl_tutup');
    }
}
