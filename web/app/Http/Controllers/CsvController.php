<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view; // Sesuaiin sama nama model lu ya
use Illuminate\Support\Facades\Response;

class CsvController extends Controller
{
    public function unduhCsv()
    {
        // 1. Tarik semua data dari history_view + bawa data request buat ngambil clean_text
        $histories = history_view::with('request')->orderBy('created_at', 'desc')->get();

        // 2. Bikin nama file otomatis sesuai tanggal & jam
        $fileName = 'riwayat_lensa_hoax_' . date('Y-m-d_His') . '.csv';

        // 3. Pake streamDownload biar aman di server
        return response()->streamDownload(function () use ($histories) {
            $file = fopen('php://output', 'w');

            // Bikin judul baris pertama (Header CSV)
            fputcsv($file, [
                'Nama', 
                'Input Text', 
                'Clean Text', 
                'Final Label', 
                'Final Confidence', 
                'Status', 
                'Created At'
            ]);

            // Masukin datanya baris per baris
            foreach ($histories as $history) {
                // Ambil clean_text dari relasi request (kalau ada)
                $cleanText = $history->request ? $history->request->clean_text : '';

                fputcsv($file, [
                    $history->username,
                    $history->input_text,
                    $cleanText,
                    $history->final_label,
                    $history->final_confidence,
                    $history->status,
                    $history->created_at
                ]);
            }

            fclose($file);
        }, $fileName);
    }
}