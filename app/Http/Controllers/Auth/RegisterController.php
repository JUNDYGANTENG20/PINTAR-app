<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Registrasi hanya untuk mahasiswa. Akun admin dibuat pengelola lewat seeder/perintah. */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nim' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/', 'unique:users,nim'],
            'prodi' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], ['nim.regex' => 'NIM hanya berisi angka.'], ['name' => 'nama', 'password' => 'kata sandi', 'prodi' => 'program studi']);

        $user = User::query()->create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('beranda')->with('sukses', 'Akun berhasil dibuat. Selamat datang di PINTAR!');
    }
}
