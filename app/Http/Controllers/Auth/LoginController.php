<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], ['password' => 'kata sandi']);

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        $request->session()->regenerate();
        $tujuan = $request->user()->isAdmin() ? route('admin.kegiatan.index') : route('beranda');

        return redirect()->intended($tujuan)->with('sukses', 'Selamat datang, '.$request->user()->name.'.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('sukses', 'Kamu sudah keluar.');
    }
}
