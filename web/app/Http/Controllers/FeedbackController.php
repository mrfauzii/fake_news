<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Feedbacks;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Menerima umpan balik', [
            'user_id' => Auth::id(),
            'request_id' => $request->input('request_id'),
            'feedback' => $request->input('feedback')
        ]);
        $validated = $request->validate([
            'feedback'   => 'required|string|max:2000',
            'request_id' => 'required|integer', // Tangkap ID-nya
        ]);

        try {
            $userId = Auth::check() ? Auth::id() : 2;

            // Pastikan hanya 1 umpan balik per user untuk tiap request
            $existing = Feedbacks::where('user_id', $userId)
                ->where('request_id', $validated['request_id'])
                ->first();

            if ($existing) {
                // Jika sudah ada, kembalikan data yang ada (tidak membuat duplikat)
                return response()->json([
                    'success' => true,
                    'message' => 'Anda sudah mengirim umpan balik untuk pencarian ini.',
                    'data' => [
                        'feedback' => $existing->feedback,
                        'created_at' => $existing->created_at,
                    ]
                ]);
            }

            $fb = Feedbacks::create([
                'user_id'    => $userId,
                'feedback'   => $validated['feedback'],
                'request_id' => $validated['request_id'] // Simpan ke database
            ]);

            // Kembalikan juga isi umpan balik agar frontend dapat langsung menampilkannya
            return response()->json([
                'success' => true,
                'message' => 'Umpan balik berhasil disimpan',
                'data' => [
                    'feedback' => $fb->feedback,
                    'created_at' => $fb->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            // Jika gagal (misal database error), catat di log
            Log::error('Gagal menyimpan feedback: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim umpan balik, terjadi kesalahan di server.'
            ], 500);
        }
    }

    /**
     * Hapus umpan balik milik user untuk request tertentu.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|integer'
        ]);

        try {
            $userId = Auth::check() ? Auth::id() : 2;

            $deleted = Feedbacks::where('user_id', $userId)
                ->where('request_id', $validated['request_id'])
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Umpan balik berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Umpan balik tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus umpan balik, terjadi kesalahan di server.'
            ], 500);
        }
    }
}
