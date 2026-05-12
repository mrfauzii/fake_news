<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view;

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
}