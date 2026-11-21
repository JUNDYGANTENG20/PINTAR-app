<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'slug', 'ikon', 'urutan'])]
class Kategori extends Model
{
    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }
}
