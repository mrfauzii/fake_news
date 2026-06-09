<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // Fungsi buat ngelempar user ke halaman Login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Fungsi pas Google ngebalikin data usernya ke web kita
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah email dari Google udah ada di database kita?
            $user = Users::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Kalo belum ada, kita buat user baru
                $user = Users::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user'
                ]);
            }
            // Langsung login in usernya
            Auth::login($user);

            if ($user->role === 'admin') {
                return redirect(route('admin.dashboard'))->with('success', 'Login berhasil pakai Google!');

            }
            
            return redirect('pencarian')->with('success', 'Login berhasil pakai Google!');


        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Gagal login pakai Google!');
        }
    }
}
