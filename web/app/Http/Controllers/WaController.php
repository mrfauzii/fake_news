<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MessageCache;
use App\Models\Users;
use App\Models\UserInteractions;
use App\Models\Feedbacks;
use App\Models\Images;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TextDetectionController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WaController extends Controller
{
    public function webhook(Request $request)
    {
        try {
            $senderRaw = (string) $request->input('sender'); // Ambil data asli Fonnte (62...)
            $message = trim(strtolower($request->input('message')));
            $name = $request->input('name');

            // 🔥 NORMALISASI DI SINI SEJAK AWAL
            $sender = $senderRaw;
            if (str_starts_with($senderRaw, '62')) {
                $sender = '0' . substr($senderRaw, 2); // Sekarang variabel $sender isinya pasti berawalan '0'
            }

            // Sekarang semua fungsi di bawahnya (firstOrCreate, #detect, #history)
            // akan aman menggunakan variabel $sender yang sudah berawalan '0'
            $user = Users::firstOrCreate(
                ['phone_number' => $sender],
                ['name' => $name ?? 'User WA'],
                ['role' => 'user'] // Pastikan role diisi dengan 'user' untuk membedakan dari akun Gmail
            );

            // 🔥 2. JIKA BUKAN COMMAND (#) -> SIMPAN KE CACHE
            if (!str_contains($message, '#')) {
                MessageCache::create([
                    'sender_number' => $sender,
                    'latest_message' => $request->input('message') // Simpan teks asli (bukan strtolower)
                ]);

                return response()->json(['status' => 'cached']);
            }

            // Variabel untuk menampung balasan WhatsApp
            $waReply = "";

            // 🔥 3. PROSES COMMAND BERDASARKAN KEYWORD (#)
            switch (true) {

                // ==========================================
                // COMMAND: #detect
                // ==========================================
                case str_starts_with($message, '#detect'):
                    $lastMessage = MessageCache::where('sender_number', $sender)
                        ->where('created_at', '>=', \Carbon\Carbon::now()->subMinutes(5))
                        ->latest()
                        ->first();

                    if ($lastMessage) {
                        $text = trim($lastMessage->latest_message);

                        // 🔥 VALIDASI: Cek apakah panjang teks kurang dari 10 karakter
                        if (strlen($text) < 10) {
                            $waReply = "⚠️ *Pesan Terlalu Pendek* ⚠️\n\n";
                            $waReply .= "Pesan yang Anda kirimkan sebelumnya hanya berisi *" . strlen($text) . " karakter*.\n\n";
                            $waReply .= "Sistem Lensa Hoax AI membutuhkan klaim/berita minimal *10 karakter* agar dapat dianalisis secara akurat. Silakan kirim ulang berita yang lebih lengkap, lalu ketik `#detect`.";
                            break; // Berhentikan proses di sini, jangan tembak AI
                        }

                        // 1. Instansiasi Controller secara Non-Static
                        $detection = new TextDetectionController();
                        Log::info("Processing #detect for user_id: " . $user->id . " with text: " . $text);

                        /**
                         * 2. Panggil fungsi detect()
                         */
                        $reply = $detection->detect($text, 0, 1, $user->id);
                        $result = json_decode($reply->getContent(), true);

                        // 3. Pengecekan status keberhasilan dari controller
                        if (isset($result['status']) && $result['status'] !== 'error') {

                            $data = $result['data'];
                            $verdict = strtolower($data['verdict'] ?? '');

                            if ($verdict === 'fake') {
                                $statusTeks = "🚨 *HOAKS* 🚨";
                            } else {
                                $statusTeks = "✅ *FAKTA* ✅";
                            }

                            // 4. Susun struktur response WhatsApp Lensa Hoax
                            $waReply = "🔍 *HASIL CEK FAKTA AI* 🔍\n";
                            $waReply .= "━━━━━━━━━━━━━━━━━━━\n\n";
                            $waReply .= "📝 *Klaim Berita:*\n";
                            $waReply .= "\"_" . $text . "_\"\n\n";
                            $waReply .= "📊 *Kesimpulan:* " . $statusTeks . "\n";
                            $waReply .= "🎯 *Keyakinan:* " . $data['confidence'] . "%\n\n";
                            $waReply .= "📖 *Ringkasan Analisis:*\n";

                            $waReply .= ($data['summary'] ?: 'Tidak ada ringkasan teks yang tersedia.') . "\n\n";

                            // 5. Tampilkan Sumber Referensi Berita
                            if (!empty($data['sources'])) {
                                $waReply .= "🌐 *Sumber Referensi Berita:* \n";
                                $sourceCounter = 1;

                                foreach ($data['sources'] as $source) {
                                    $url = is_array($source) ? ($source['url'] ?: '') : $source;

                                    if (is_array($source) && empty($url)) {
                                        $url = "Database Knowledge Base Anti-Hoax";
                                    }

                                    if (!empty($url)) {
                                        $waReply .= $sourceCounter . ". " . $url . "\n";
                                        $sourceCounter++;
                                    }
                                }
                                $waReply .= "\n";
                            }

                            $waReply .= "━━━━━━━━━━━━━━━━━━━\n";
                            $waReply .= "💡 _Gunakan informasi secara bijak sebelum membagikannya._";
                        } else {
                            $errorMessage = $result['message'] ?? 'Terjadi kesalahan pada sistem internal.';
                            $waReply = "❌ *Gagal Memproses Cek Fakta* ❌\n\nKeterangan: " . $errorMessage;
                        }
                    } else {
                        $waReply = "⚠️ *Pesan Tidak Ditemukan*\n\nMaaf, sistem tidak menemukan pesan yang Anda kirimkan dalam 5 menit terakhir untuk dideteksi. Silakan kirim beritanya terlebih dahulu, lalu ketik `#detect`.";
                    }
                    break;

                // ==========================================
                // COMMAND: #info
                // ==========================================
                case str_starts_with($message, '#info'):
                    // Generate Link Beranda Website menggunakan Route Name Laravel
                    $linkWebsite = route('beranda');

                    $waReply = "🤖 *MENGENAL LENSA HOAX* 🤖\n";
                    $waReply .= "━━━━━━━━━━━━━━━━━━━\n\n";
                    $waReply .= "*Lensa Hoax* adalah sistem yang dirancang khusus untuk *mendeteksi keaslian berita* atau klaim secara cepat dan akurat.\n\n";
                    $waReply .= "⚙️ *Fitur Utama via WhatsApp:*\n";
                    $waReply .= "1. `#detect` - Periksa keaslian pesan terakhir yang Anda kirim.\n";
                    $waReply .= "2. `#info` - Lihat informasi tentang sistem Lensa Hoax.\n";
                    $waReply .= "3. `#trending` - Lihat daftar tren hoaks terpopuler.\n";
                    $waReply .= "4. `#history` - Lihat riwayat pencarian terakhir Anda.\n\n";
                    $waReply .= "🌐 *Versi Website:*\n";
                    $waReply .= "Nikmati visualisasi data dan laporan analisis hoaks yang lebih mendalam melalui website resmi kami.\n\n";
                    $waReply .= "🔗 *Kunjungi Sekarang:*\n";
                    $waReply .= "👉 " . $linkWebsite . "\n\n";
                    $waReply .= "━━━━━━━━━━━━━━━━━━━\n";
                    $waReply .= "💡 _Mari bersama-sama putus mata rantai hoaks!_";
                    break;
                // ==========================================
                // COMMAND: #trending
                // ==========================================
                case str_starts_with($message, '#trending'):
                    // 1. Ambil data dari view database yang sama dengan website (Hanya yang HOAKS/FAKE)
                    $trendingHoaxes = DB::table('user_interactions as ui')
                        ->join('requests as r', 'ui.request_id', '=', 'r.id')
                        ->leftJoin('stage1_results as s1', 'r.id', '=', 's1.request_id')
                        ->leftJoin('knowledge_base as kb', 's1.knowledge_id', '=', 'kb.id')
                        ->leftJoin('stage2_results as s2', 'r.id', '=', 's2.request_id')
                        ->whereNull('r.deleted_at')
                        ->where('r.final_label', 'fake')
                        ->whereYear('ui.created_at', Carbon::now()->year)
                        ->whereMonth('ui.created_at', Carbon::now()->month)
                        ->select(
                            'r.input_text',
                            'r.final_label',
                            'r.final_confidence',
                            'kb.fact_text as fact_text',
                            's2.summary_text as summary_text',
                            DB::raw('COUNT(*) as count')
                        )
                        ->groupBy(
                            'r.input_text',
                            'r.final_label',
                            'r.final_confidence',
                            'kb.fact_text',
                            's2.summary_text'
                        )
                        ->orderByDesc('count')
                        ->take(3)
                        ->get();

                    // 2. Kamus Nama Bulan untuk mempercantik info periode saat ini
                    $bulanIndo = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    $periodeSekarang = $bulanIndo[date('n')] . ' ' . date('Y');

                    // 3. Susun struktur pesan WhatsApp
                    $waReply = "🔥 *PENCARIAN HOAX TERPOPULER* 🔥\n";
                    $waReply .= "📊 *Periode:* " . $periodeSekarang . "\n";
                    $waReply .= "━━━━━━━━━━━━━━━━━━━\n";
                    $waReply .= "Berikut adalah klaim hoaks yang paling banyak ditanyakan oleh pengguna minggu ini:\n\n";

                    if ($trendingHoaxes->isNotEmpty()) {
                        foreach ($trendingHoaxes as $index => $hoax) {
                            $waReply .= ($index + 1) . ". *\"" . $hoax->input_text . "\"*\n";
                            $waReply .= "📈 _Dicari sebanyak: " . $hoax->count . " kali_\n\n";
                        }

                        // Link dinamis ke halaman pencarian terpopuler di website Anda (jika ada route name-nya)
                        // Misal nama routenya 'populer', jika tidak ada bisa tetap pakai route('beranda')
                        $linkPopuler = route('beranda');

                        $waReply .= "🌐 *Lihat Statistik Lengkap di Web:*\n";
                        $waReply .= "👉 " . $linkPopuler . "\n\n";
                        $waReply .= "💡 _Jangan mudah terprovokasi dan langsung menyebarkan berita di atas ya!_ \n";
                    } else {
                        $waReply .= "Belum ada data tren hoaks yang cukup untuk bulan ini. Sistem masih terus memantau ruang digital.\n";
                    }
                    $waReply .= "━━━━━━━━━━━━━━━━━━━";
                    break;

                // ==========================================
                // COMMAND: #history
                // ==========================================
                case str_starts_with($message, '#history'):
                    // 1. NORMALISASI: Ubah awalan '62' dari Fonnte menjadi '0' agar cocok dengan DB Anda
                    $formattedNumber = $sender;
                    if (str_starts_with($sender, '62')) {
                        $formattedNumber = '0' . substr($sender, 2); // Mengubah 62857... menjadi 0857...
                    }

                    // 2. Cari user berdasarkan nomor yang SUDAH DINORMALISASI
                    $user = \App\Models\Users::where('phone_number', $formattedNumber)->first();

                    // 3. Ambil ID-nya. Jika user belum terdaftar di DB, default ke ID 2 seperti logic deteksi
                    $userId = $user ? $user->id : 2;

                    // 4. Hitung total pencarian berdasarkan userId yang akurat
                    $totalPencarian = \App\Models\UserInteractions::where('user_id', $userId)->count();

                    $linkWebsite = route('beranda');

                    // 5. Susun struktur pesan WhatsApp
                    $waReply = "📜 *RIWAYAT PENCARIAN ANDA* 📜\n";
                    $waReply .= "━━━━━━━━━━━━━━━━━━━\n\n";
                    $waReply .= "Halo *" . ($name ?? 'Pengguna Lensa Hoax') . "*,\n";

                    if ($totalPencarian > 0) {
                        $waReply .= "Sistem mencatat Anda telah melakukan *" . $totalPencarian . " kali cek fakta* melalui WhatsApp.\n\n";
                        $waReply .= "Untuk melihat daftar riwayat lengkap, grafik analitik data, dan detail sumber referensi, silakan kunjungi Dashboard Website kami.\n\n";
                    } else {
                        $waReply .= "Anda belum memiliki riwayat cek fakta di sistem kami.\n\n";
                        $waReply .= "Yuk, jelajahi fitur lengkap dan mulai pelacakan berita melalui platform web kami.\n\n";
                    }

                    $waReply .= "🔐 *Akses Instan Dashboard Web:*\n";
                    $waReply .= "👉 " . $linkWebsite . "\n\n";
                    $waReply .= "💡 _Cukup login menggunakan nomor WhatsApp Anda untuk masuk ke akun Anda secara otomatis._\n";
                    $waReply .= "━━━━━━━━━━━━━━━━━━━\n";
                    $waReply .= "🌐 _Lensa Hoax - Bersama Lawan Misinformasi_";
                    break;

                // Jika command tidak dikenali (Misal user asal ketik #halo)
                default:
                    $waReply = "🤖 *Command Tidak Dikenali*\n\nKetik `#info` untuk melihat daftar perintah resmi yang tersedia di Lensa Hoax AI.";
                    break;
            }

            // 🔥 4. KIRIM KE FONNTE (Hanya jika variabel balasan terisi)
            if (!empty($waReply)) {
                \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => env('FONNTE_TOKEN')
                ])->post('https://api.fonnte.com/send', [
                    'target' => $sender,
                    'message' => $waReply
                ]);

                Log::info("WhatsApp Reply Sent to " . $sender);
            }

            return response()->json(['status' => 'replied']);
        } catch (\Exception $e) {
            Log::error('ERROR WA', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function linkWhatsApp($wa_number)
    {

        $waNumberRaw = trim($wa_number);

        $waNumberFonnte = $waNumberRaw;
        if (str_starts_with($waNumberRaw, '0')) {
            $waNumberFonnte = '62' . substr($waNumberRaw, 1);
        }

        // Generate Token Random
        $token = Str::random(40);
        $userId = Auth::id();

        // Simpan token ke Cache Laravel (Batas waktu 10 Menit)
        // Format Key: wa_verification_token_{token}
        Cache::put("wa_verification_{$token}", [
            'user_id' => $userId,
            'phone_number' => $waNumberRaw // Simpan dengan awalan 0 buat di DB
        ], now()->addMinutes(10));

        // Buat Link Verifikasi
        $verificationLink = url('/verify-wa/' . $token);

        // Pesan WhatsApp
        $waMessage = "🔐 *VERIFIKASI AKUN LENSA HOAX* 🔐\n\n";
        $waMessage .= "Halo, kami menerima permintaan untuk menghubungkan nomor ini dengan akun di website Lensa Hoax.\n\n";
        $waMessage .= "Klik link di bawah ini untuk menyetujui:\n";
        $waMessage .= "👉 " . $verificationLink . "\n\n";
        $waMessage .= "_Link ini hanya berlaku selama 10 menit. Abaikan pesan ini jika Anda tidak merasa melakukan permintaan._";

        // Tembak API Fonnte buat kirim Link
        try {
            $response = Http::timeout(5)->withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => $waNumberFonnte,
                'message' => $waMessage
            ]);
        } catch (\Exception $e) {
            Log::info('Merge ERROR: ' . $e->getMessage());
        }
    }

    public function showVerifyPage($token)
    {
        // Cek apakah token valid

        return view('user.verify_wa', ['token' => $token]);
    }
    public function verifyWaLink(Request $request)
    {
        $token = $request->input('token');
        // Cek apakah token ada dan belum expired
        Log::info("Attempting WhatsApp verification with token: " . $token);
        $cacheData = Cache::get("wa_verification_{$token}");

        if (!$cacheData) {
            // Kalau udah klik tapi ternyata token nggak valid / kadaluarsa
            // lgsg bisa redirect ke halaman profil atau beranda dengan pesan error
            return redirect()->route('beranda')->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        $userId = $cacheData['user_id'];
        $waNumber = $cacheData['phone_number'];

        // Ambil data User yang lagi login (berdasarkan ID di cache, buat jaga-jaga kalo bukanya dari HP beda browser)
        $currentUser = Users::find($userId);

        if (!$currentUser) {
            return redirect()->route('beranda')->with('error', 'Akun tidak ditemukan.');
        }

        // Cek apakah ada akun bot (atau akun lain) di DB yang udah pake nomor WA ini
        $existingWaUser = Users::where('phone_number', $waNumber)->first();

        DB::beginTransaction();
        try {
            // SKENARIO 1: Nomor udah dipake di akun lain (MERGE DATA)
            if ($existingWaUser && $existingWaUser->id !== $currentUser->id) {

                // Pindahkan history ke akun utama (Gmail)
                UserInteractions::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);

                Images::where('uploaded_by', $existingWaUser->id)
                    ->update(['uploaded_by' => $currentUser->id]);

                Feedbacks::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);

                // Hapus akun lama biar nggak duplikat
                $existingWaUser->delete();
            }

            // SKENARIO 2: Nomor bener-bener baru / Skenario 1 udah selesai
            // Update nomor HP di akun utama web
            $currentUser->phone_number = $waNumber;
            $currentUser->save();

            DB::commit();

            // Hapus Token dari Cache biar gabisa diklik 2x
            Cache::forget("wa_verification_{$token}");

            return redirect()->route('beranda')->with('success', 'Nomor WhatsApp berhasil dihubungkan ke akun Anda!');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->route('beranda')->with('error', 'Gagal memverifikasi akun: ' . $e->getMessage());
        }
    }
}
