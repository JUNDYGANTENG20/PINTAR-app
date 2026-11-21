<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /** Id kegiatan yang sudah disimpan sebagai pengingat oleh pengguna yang login. */
    protected function idPengingat(Request $request): array
    {
        return $request->user()?->pengingats()->pluck('kegiatan_id')->all() ?? [];
    }
}
