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

        $histories = history_view::with(['request.image', 'request.stage1Results', 'request.stage2Results'])
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
            $penjelasan = null;

            // Prioritas Stage 2
            if ($history->request && $history->request->stage2Results->isNotEmpty()) {
                $penjelasan = $history->request->stage2Results->first()->summary_text;
            }
            // Kalau tidak ada Stage 2, pakai Stage 1
            elseif ($history->request && $history->request->stage1Results->isNotEmpty()) {
                $kbId = $history->request->stage1Results->first()->knowledge_id;

                $kb = \App\Models\KnowledgeBase::find($kbId);

                $penjelasan = $kb?->fact_text;
            }

            return [
                'request_id' => $history->request_id,
                'deleted_at' => $history->deleted_at ?? null,
                'is_deleted' => $history->deleted_at !== null,
                'judul' => $isImageSearch ? '[GAMBAR] Pencarian oleh: ' . $history->username : '[TEKS] Pencarian oleh: ' . $history->username,
                'penjelasan' => $penjelasan,
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
                } else {
                    $statusStr = 'benar';
                }

                // Stage 2 URL Referensi
                if ($request->stage2Results->isNotEmpty()) {
                    $stage2 = $request->stage2Results->first();

                    if (!empty($stage2->summary_text)) {
                        $description = $stage2->summary_text; // FULL REPLACE
                    }

                    if (!empty($stage2->url)) {
                        $urlArr = is_string($stage2->url)
                            ? json_decode($stage2->url, true)
                            : $stage2->url;

                        if (is_array($urlArr) && count($urlArr)) {
                            $description .= "\n\nSumber Referensi: \n" . implode("\n", $urlArr);
                        }
                    }
                }elseif ($request->stage1Results->isNotEmpty()) {
                    $kbId = $request->stage1Results->first()->knowledge_id;

                    $kb = \App\Models\KnowledgeBase::find($kbId);

                    if ($kb && !empty($kb->fact_text)) {
                        $description = $kb->fact_text; // FULL REPLACE
                    }

                    if (!empty($kb->link_counter)) {
                        $urlArr = is_string($kb->link_counter)
                            ? json_decode($kb->link_counter, true)
                            : $kb->link_counter;

                        if (is_array($urlArr) && count($urlArr)) {
                            $description .= "\n\nSumber Referensi: \n" . implode("\n", $urlArr);
                        }
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
