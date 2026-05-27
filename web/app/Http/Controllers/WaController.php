<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MessageCache;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TextDetectionController;

class WaController extends Controller
{
    public function webhook(Request $request)
    {
        try {

            $sender = (string) $request->input('sender');
            $message = trim(strtolower($request->input('message')));
            $name = $request->input('name');

            Users::firstOrCreate(
                ['phone_number' => $sender],
                ['name' => $name ?? 'User WA']
            );

            // 🔥 2. JIKA BUKAN COMMAND (#)
            if (!str_contains($message, '#')) {

                MessageCache::create([
                    'sender_number' => $sender,
                    'latest_message' => $message
                ]);

                return response()->json(['status' => 'cached']);
            }

            // 🔥 3. COMMAND: #detect
            if (str_starts_with($message, '#detect')) {

                $lastMessage = MessageCache::where('sender_number', $sender)
                    ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                    ->latest()
                    ->first();

                if ($lastMessage) {
                    // Ambil teks murni dari pesan terakhir
                    $text = $lastMessage->latest_message;

                    // 1. Panggil secara Non-Static (Gunakan 'new')
                    $detection = new TextDetectionController();
                    $reply = $detection->detect($text);

                    // 2. Bongkar response JsonResponse bawaan Laravel menjadi array PHP biasa
                    $result = json_decode($reply->getContent(), true);

                    // 3. IF-ELSE untuk mengecek apakah proses AI sukses atau error
                    if (isset($result['status']) && $result['status'] !== 'error') {

                        // Ambil data matang yang SUDAH DIFORMAT oleh TextDetectionController
                        $data = $result['data'];

                        // Tentukan emoji & judul kesimpulan berdasarkan 'verdict' yang sudah jadi (fake/valid)
                        $verdict = strtolower($data['verdict'] ?? '');
                        if ($verdict === 'fake') {
                            $statusTeks = "🚨 *HOAKS* 🚨";
                        } else {
                            $statusTeks = "✅ *FAKTA* ✅";
                        }

                        // 4. Susun struktur pesan WA menggunakan data siap pakai
                        $waReply = "🔍 *HASIL CEK FAKTA AI* 🔍\n";
                        $waReply .= "━━━━━━━━━━━━━━━━━━━\n\n";
                        $waReply .= "📝 *Klaim Berita:*\n";
                        $waReply .= "\"_" . $text . "_\"\n\n";
                        $waReply .= "📊 *Kesimpulan:* " . $statusTeks . "\n";
                        $waReply .= "🎯 *Keyakinan:* " . $data['confidence'] . "%\n\n";
                        $waReply .= "📖 *Ringkasan Analisis:*\n";
                        $waReply .= $data['summary'] . "\n\n"; // Kalimat "Hasil penelusuran..." otomatis muncul di sini

                        // 5. Tampilkan Sumber Referensi Berita
                        if (!empty($data['sources'])) {
                            $waReply .= "🌐 *Sumber Referensi Berita:* \n";
                            foreach ($data['sources'] as $index => $source) {
                                // Stage 1 berbentuk array, Stage 2 berbentuk string murni URL
                                $url = is_array($source) ? ($source['url'] ?: 'Database Sistem') : $source;
                                if (!empty($url)) {
                                    $waReply .= ($index + 1) . ". " . $url . "\n";
                                }
                            }
                        }

                        $waReply .= "━━━━━━━━━━━━━━━━━━━\n";
                        $waReply .= "💡 _Gunakan informasi secara bijak sebelum membagikannya._";
                    } else {
                        // Jika statusnya 'error' (misal API Python mati / timeout)
                        $errorMessage = $result['message'] ?? 'Terjadi kesalahan pada sistem internal.';
                        $waReply = "❌ *Gagal Memproses Cek Fakta* ❌\n\nKeterangan: " . $errorMessage;
                    }

                    // 6. Kirim variabel $waReply ini ke fungsi pengirim WA Anda
                    // Contoh: $this->sendWhatsApp($sender, $waReply);
                    Log::info("WhatsApp Reply Sent: " . $waReply);
                }
            }

            // Log untuk tracking lokal di Laravel
            Log::info("WhatsApp Reply Sent to " . $sender);

            // 🔥 4. KIRIM KE FONNTE
            Http::timeout(5)->withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => $sender,
                'message' => $waReply
            ]);

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

    public function linkWhatsApp(Request $request)
    {
        // 1. Validasi inputan form dari web
        $request->validate([
            'wa_number' => 'required'
        ]);

        /** @var \App\Models\Users $currentUser */
        $currentUser = Auth::user();
        $waNumber = trim($request->wa_number);

        // 2. Cek apakah ada akun bot di DB yang udah pake nomor WA ini
        $existingWaUser = Users::where('phone_number', $waNumber)->first();

        // 3. Kalau nomor belum terdaftar di database, kembalikan error
        if (!$existingWaUser) {
            return back()->with('error', 'Nomor ini belum terdaftar. Silakan cek via WhatsApp pada menu Dapatkan Melalui WhatsApp.');
        }

        // 4. Kalau nomornya ada dan itu bukan akun yang lagi login -> merge akun
        if ($existingWaUser->id !== $currentUser->id) {
            DB::beginTransaction();
            try {
                // A. Pindahkan history ke akun utama (Gmail)
                \App\Models\UserInteractions::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);

                \App\Models\Images::where('uploaded_by', $existingWaUser->id)
                    ->update(['uploaded_by' => $currentUser->id]);

                \App\Models\Feedbacks::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);

                // B. Update nomor HP di akun web
                $currentUser->phone_number = $waNumber;
                $currentUser->save();

                // C. Hapus akun WA lama biar nggak duplikat
                $existingWaUser->delete();

                DB::commit();

                return back()->with('success', 'Akun WhatsApp berhasil dihubungkan dan riwayat telah digabungkan otomatis.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menggabungkan akun: ' . $e->getMessage());
            }
        }

        // 5. Kalau kebetulan ID-nya sama (emang udah nyambung)
        return back()->with('success', 'Nomor WhatsApp ini sudah terhubung dengan akun Anda.');
    }
}
