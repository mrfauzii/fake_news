<?php

namespace App\Http\Controllers;

use App\Models\history_view;
use App\Models\Requests;
use App\Models\UserInteractions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $search = request('search');

        $histories = history_view::with('request.image')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")->orWhere('input_text', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
        $data = $histories->through(function ($history) {
            $isImageSearch = $history->request && $history->request->image_id != null;
            $isHoax = strtolower($history->final_label) === 'fake';
            $confidence = ($history->final_confidence ?? 0) * 100;

            $persenHoax = $isHoax ? $confidence : 100 - $confidence;
            $persenBenar = $isHoax ? 100 - $confidence : $confidence;

            return [
                'request_id' => $history->request_id,
                'deleted_at' => $history->deleted_at ?? null,
                'is_deleted' => $history->deleted_at !== null,
                'judul' => $isImageSearch ? '[GAMBAR] Pencarian oleh: ' . $history->username : '[TEKS] Pencarian oleh: ' . $history->username,
                'penjelasan' => $isHoax ? 'Hasil verifikasi menunjukkan bahwa sebagian besar informasi ini, yakni sekitar ' . round($persenHoax) . '%, mengandung unsur hoaks atau ketidaksesuaian fakta. Mohon untuk memvalidasi kembali sumber informasi sebelum menyebarkannya.' : 'Hasil verifikasi menunjukkan bahwa informasi ini memiliki tingkat kebenaran sekitar ' . round($persenBenar) . '% dan termasuk informasi yang valid berdasarkan hasil analisis sistem.',
                'user' => $history->username,
                'date' => $history->created_at ? Carbon::parse($history->created_at)->translatedFormat('l, j F Y') : '-',
                'deskripsi' => $isImageSearch ? null : $history->input_text,
                'gambar' => $isImageSearch && $history->request->image ? $history->request->image->file_path : null,
                'hoax' => round($persenHoax),
                'benar' => round($persenBenar),
            ];
        });
        return view('admin.riwayat', compact('data'));
    }

    public function filterRiwayat(Request $request)
    {
        // 1. Tangkap input dari frontend
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $label = $request->input('label');

        // 2. Mulai bikin query filter ke history_view
        $query = history_view::query();

        if (!empty($bulan)) {
            $query->whereMonth('created_at', $bulan);
        }

        if (!empty($tahun)) {
            $query->whereYear('created_at', $tahun);
        }

        if (!empty($label)) {
            $dbLabel = strtolower($label) === 'fake' ? 'hoax' : 'valid';
            $query->where('final_label', $dbLabel);
        }

        // 3. Eksekusi pencarian
        $histories = $query->get();

        // 4. Olah datanya
        $data = $histories->map(function ($history) {
            $userCount = UserInteractions::where('request_id', $history->request_id)->count();

            return [
                'input_text' => $history->input_text,
                'user_count' => $userCount,
                'status' => $history->status,
            ];
        });

        // 5. Kembalikan berupa JSON
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $request = Requests::findOrFail($id);

        $request->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function restore($id)
    {
        $request = Requests::withTrashed()->findOrFail($id);

        $request->restore();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * FUNGSI UNTUK HALAMAN RIWAYAT ROLE: USER
     */
    public function riwayatUser()
    {
        Carbon::setLocale('id');

        $userId = Auth::check() ? Auth::id() : 2;

        $histories = UserInteractions::with(['request.image', 'request.stage1Results', 'request.stage2Results', 'request.feedbacks'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $histories
            ->map(function ($history) {
                if (!$history->request) {
                    return null;
                }

                $request = $history->request;

                // URL gambar Cloudinary
                $imageUrl = $request->image?->file_path;

                // Deteksi pencarian gambar
                $isImageSearch = !empty($request->image_id);

                /*
        |--------------------------------------------------------------------------
        | Label & Confidence
        |--------------------------------------------------------------------------
        | Jika field final_label dan final_confidence ada di requests
        | gunakan $request->xxx.
        |
        | Jika ternyata masih ada di UserInteractions,
        | ganti kembali ke $history->xxx.
        */
                $finalLabel = strtolower($request->final_label ?? '');
                $confidence = ($request->final_confidence ?? 0) * 100;

                $isHoax = in_array($finalLabel, ['hoax', 'fake']);

                if ($isHoax) {
                    $statusStr = 'hoax';
                    $description = 'Investigasi mendalam menunjukkan bahwa informasi ini mengandung elemen yang telah dilebih-lebihkan atau tidak sesuai dengan fakta sebenarnya. Berdasarkan analisis, klaim ini diklasifikasikan sebagai informasi yang menyesatkan dengan tingkat hoaks ' . round($confidence) . '%.';
                } else {
                    // JANGAN diubah jadi "fakta" kalau frontend masih cek "benar"
                    $statusStr = 'benar';

                    $description = 'Hasil verifikasi menunjukkan bahwa informasi ini memiliki tingkat kebenaran sekitar ' . round($confidence) . '% dan termasuk informasi yang valid berdasarkan hasil analisis sistem.';
                }

                // Stage 2 URL Referensi
                if ($request->stage2Results->isNotEmpty()) {
                    $urls = $request->stage2Results->first()->url;

                    if (!empty($urls)) {
                        $urlArr = is_string($urls) ? json_decode($urls, true) : $urls;

                        if (is_array($urlArr) && count($urlArr)) {
                            $description .= ' Sumber referensi: ' . implode(' | ', $urlArr);
                        }
                    }
                }

                // Stage 1 Knowledge Base
                if ($request->stage1Results->isNotEmpty()) {
                    $kbId = $request->stage1Results->first()->knowledge_id;

                    $kb = \App\Models\KnowledgeBase::find($kbId);

                    if ($kb && !empty($kb->fact_text)) {
                        $description = ltrim($kb->fact_text, ', ');
                    }
                }

                // Feedback User
                $feedback = null;

                if (Auth::check()) {
                    $fb = $request->feedbacks->where('user_id', Auth::id())->first();

                    if ($fb) {
                        $feedback = [
                            'id' => $fb->id,
                            'feedback' => $fb->feedback,
                            'created_at' => $fb->created_at,
                        ];
                    }
                }

                return [
                    // ID interaksi
                    'id' => $history->id,

                    // ID request
                    'request_id' => $history->request_id,

                    'query' => $isImageSearch ? '[Pencarian Berupa Gambar]' : $request->input_text ?? '-',

                    'status' => $statusStr,

                    'description' => $description,

                    'date' => $history->created_at,

                    'confidence' => round($confidence),

                    'image_url' => $imageUrl,

                    'feedback' => $feedback,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        return view('user.riwayat', compact('data'));
    }

    /**
     * FUNGSI UNTUK MENGHAPUS (SOFT DELETE) RIWAYAT USER
     */
    public function destroyUserHistory($id)
    {
        $interaction = UserInteractions::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $interaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat berhasil dihapus.',
        ]);
    }

    /**
     * FUNGSI UNTUK MENGHAPUS SEMUA RIWAYAT USER SEKALIGUS (SOFT DELETE)
     */
    public function destroyAllUserHistory()
    {
        $userId = Auth::check() ? Auth::id() : 2;

        // Hapus semua catatan interaksi milik user ini saja
        UserInteractions::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semua riwayat pencarian Anda berhasil dibersihkan.',
        ]);
    }
}
