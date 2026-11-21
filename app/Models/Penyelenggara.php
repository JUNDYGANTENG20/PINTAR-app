<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kontak_nama', 'kontak_info'])]
class Penyelenggara extends Model
{
    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }

    /** FR-06: kontak yang dapat dihubungi terkait pendaftaran. */
    public function tampilkanKontak(): string
    {
        return trim(($this->kontak_nama ?? '').' '.($this->kontak_info ? "({$this->kontak_info})" : ''));
    }
}
