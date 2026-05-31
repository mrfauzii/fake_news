<?php

namespace App\Http\Controllers;

use App\Models\Feedbacks;
use App\Models\Images;
use App\Models\UserInteractions;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    // =========================================================================
    // 1. KIRIM LINK VERIFIKASI KE EMAIL
    // =========================================================================
    public function linkEmail($email)
    {
        // Validasi input email
        $emailTarget = trim($email);
        $userId = Auth::id();
        $token = Str::random(40);

        // Simpan token ke Cache (Batas waktu 10 Menit)
        Cache::put("email_verification_{$token}", [
            'user_id' => $userId,
            'email' => $emailTarget
        ], now()->addMinutes(10));

        // Buat Link Verifikasi
        $verificationLink = url('/verify-email/' . $token);

        // Susun isi pesan Email
        $emailMessage = "Halo,\n\n";
        $emailMessage .= "Kami menerima permintaan untuk menghubungkan email ini dengan akun Lensa Hoax Anda.\n\n";
        $emailMessage .= "Klik link di bawah ini untuk menyetujui:\n";
        $emailMessage .= $verificationLink . "\n\n";
        $emailMessage .= "Link ini hanya berlaku selama 10 menit. Abaikan pesan ini jika Anda tidak merasa melakukan permintaan.";

        try {
            Mail::raw($emailMessage, function ($message) use ($emailTarget) {
                $message->to($emailTarget)
                        ->subject('Verifikasi Akun Lensa Hoax');
            });

            return back()->with('success', 'Link verifikasi telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.');

        } catch (\Exception $e) {
            Log::error('Gagal mengirim email', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengirim email. Pastikan settingan SMTP sudah benar: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. PROSES SAAT LINK DIKLIK
    // =========================================================================
    public function verifyEmailLink($token)
    {
        // Cek apakah token ada dan belum expired
        $cacheData = Cache::get("email_verification_{$token}");

        if (!$cacheData) {
            return redirect()->route('beranda')->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        $userId = $cacheData['user_id'];
        $emailTarget = $cacheData['email'];

        $currentUser = Users::find($userId);

        if (!$currentUser) {
             return redirect()->route('beranda')->with('error', 'Akun tidak ditemukan.');
        }

        // Cek apakah email ini udah dipakai sama akun lain di DB
        $existingEmailUser = Users::where('email', $emailTarget)->first();

        DB::beginTransaction();
        try {
            // SKENARIO MERGE: Kalau Email udah dipake di akun lain
            if ($existingEmailUser && $existingEmailUser->id !== $currentUser->id) {
                
                UserInteractions::where('user_id', $existingEmailUser->id)
                    ->update(['user_id' => $currentUser->id]);

                Images::where('uploaded_by', $existingEmailUser->id)
                    ->update(['uploaded_by' => $currentUser->id]);

                Feedbacks::where('user_id', $existingEmailUser->id)
                    ->update(['user_id' => $currentUser->id]);

                // Hapus akun lama
                $existingEmailUser->delete();
            }

            // Update Email di akun utama
            $currentUser->email = $emailTarget;
            $currentUser->save();

            DB::commit();

            // Hapus Token dari Cache
            Cache::forget("email_verification_{$token}");

            return redirect()->route('beranda')->with('success', 'Email berhasil dihubungkan ke akun Anda!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('beranda')->with('error', 'Gagal memverifikasi akun: ' . $e->getMessage());
        }
    }
}