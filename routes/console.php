<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pintar:buat-admin {email} {--nama=Admin PINTAR}', function (string $email) {
    $password = $this->secret('Kata sandi admin (minimal 8 karakter)');
    if (strlen((string) $password) < 8) {
        $this->error('Kata sandi minimal 8 karakter.');

        return 1;
    }
    $user = User::query()->updateOrCreate(['email' => $email], ['name' => $this->option('nama'), 'password' => $password]);
    $user->forceFill(['role' => 'admin'])->save();
    $this->info("Akun admin {$email} siap dipakai.");
})->purpose('Membuat atau memperbarui akun admin/pengelola PINTAR');
