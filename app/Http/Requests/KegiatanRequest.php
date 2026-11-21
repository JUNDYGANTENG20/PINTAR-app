<?php

namespace App\Http\Requests;

use App\Models\Kegiatan;
use Illuminate\Foundation\Http\FormRequest;

class KegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:200'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'jenis' => ['required', 'in:'.implode(',', Kegiatan::JENIS)],
            'bidang' => ['nullable', 'array'],
            'bidang.*' => ['integer', 'exists:bidang_minats,id'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'kontak_nama' => ['nullable', 'string', 'max:150'],
            'kontak_info' => ['nullable', 'string', 'max:150'],
            'tgl_buka' => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
            'tgl_pelaksanaan' => ['nullable', 'date'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'manfaat' => ['nullable', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'persyaratan' => ['nullable', 'string', 'max:5000'],
            'sumber_nama' => ['required', 'string', 'max:150'],
            'sumber_url' => ['required', 'url:http,https', 'max:500'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_poster' => ['nullable', 'boolean'],
            'terverifikasi' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama kegiatan', 'kategori_id' => 'kategori', 'tgl_buka' => 'tanggal dibuka',
            'tgl_tutup' => 'tanggal ditutup', 'tgl_pelaksanaan' => 'tanggal pelaksanaan',
            'sumber_nama' => 'nama sumber resmi', 'sumber_url' => 'tautan sumber resmi',
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus sama atau setelah tanggal dibuka.',
            'sumber_url.url' => 'Masukkan tautan lengkap, diawali https://',
            'poster.max' => 'Ukuran poster maksimal 2 MB.',
        ];
    }
}
