<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ImageDetectionController;
use App\Http\Controllers\TextDetectionController;
use Illuminate\Support\Facades\Log;

class DetectionController extends Controller
{
    /**
     * Handle incoming detection requests and route them appropriately.
     */
    public function detect(Request $request)
    {
        Log::info($request->all());

        // 1. Cek apakah user mengupload file dengan nama input 'image'
        if ($request->hasFile('image')) {
            // Lempar request ke ImageDetectionController
            return app(ImageDetectionController::class)->detect($request);
        }

        // 2. Cek apakah user mengirim teks dengan nama input 'input_text'
        if ($request->filled('query')) {
            Log::info("Mendeteksi teks: " . $request->input('query'));
            // Lempar request ke TextDetectionController
            return app(TextDetectionController::class)->detectText($request);
        }

        // 3. Jika tidak ada gambar maupun teks yang dikirim, kembalikan error
        return response()->json([
            'status' => 'error',
            'message' => 'Input tidak valid. Harap masukkan teks (input_text) atau unggah gambar (image).'
        ], 400);
    }
}
