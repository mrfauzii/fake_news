<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view;
use App\Models\UserInteractions;
use Illuminate\Support\Facades\Response;

class RiwayatController extends Controller
{
    public function index()
    {
        $histories = history_view::with('request.image')->orderBy('created_at', 'desc')->get();
        $data = $histories->map(function ($history) {
            
            $isImageSearch = $history->request && $history->request->image_id != null;
            $isHoax = strtolower($history->final_label) === 'hoax';
            $confidence = $history->final_confidence ?? 0;
            
            $persenHoax = $isHoax ? $confidence : (100 - $confidence);
            $persenBenar = $isHoax ? (100 - $confidence) : $confidence;

            return [
                'judul'     => $isImageSearch ? '[GAMBAR] Pencarian oleh: ' . $history->username : '[TEKS] Pencarian oleh: ' . $history->username,
                'deskripsi' => $isImageSearch ? null : $history->input_text,
                'gambar'    => $isImageSearch && $history->request->image ? $history->request->image->file_path : null,
                'hoax'      => round($persenHoax),
                'benar'     => round($persenBenar),
            ];
            
        })->toArray(); 

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
                'status'     => $history->status,
            ];
        });

        // 5. Kembalikan berupa JSON
        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }
}