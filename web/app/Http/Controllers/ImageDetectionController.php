<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;
use App\Models\ImageSearchResults;
use App\Models\Requests;
use App\Models\UserInteractions;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class ImageDetectionController extends Controller
{
    public function detect(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            // 1. Upload ke Cloudinary
            $file = $request->file('gambar');

        Log::info('Menerima file: ' . $file->getClientOriginalName());

        // 🔥 Setup Cloudinary
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        // 🔥 Upload langsung (NO MOVE)
        $upload = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => 'fake_news_system'
        ]);

        $url = $upload['secure_url'];

        Log::info('Upload sukses: ' . $url);
            Log::info('Gambar berhasil diupload ke Cloudinary: ' . $url);
            // 2. Simpan ke tabel 'images'
            $imgRecord = Images::create([
                'file_path' => $url,    
                'original_filename' => $file->getClientOriginalName(),
                'uploaded_by' => auth()->id() ?? 2
            ]);

            // 3. Inisialisasi awal di tabel 'requests'
            $newReq = Requests::create([
                'image_id' => $imgRecord->id,
                'status' => 'processing'
            ]);
            UserInteractions::create([
                'user_id'    => auth()->id() ?? 2,
                'request_id' => $newReq->id,
            ]);
            log::info('Request baru dibuat dengan ID: ' . $url);
            // 4. Panggil API Python
            Log::info('SEBELUM PYTHON');
            $response = Http::timeout(300)
        ->post('http://localhost:8004/image-detection', [
            'image_url' => $url
        ]);
            Log::info($response->body());
            if ($response->successful()) {
                $res = $response->json();
                $links = collect($res['data'])
    ->pluck('link')
    ->toArray();
    Log::info('SESUDAH PYTHON');
                // 5. Simpan ke tabel 'image_search_results'        
                // 6. Update hasil akhir di tabel 'requests'
                $isHoax = $res['prediction'] == 1;
                $finalLabel = $isHoax == 1 ? 'fake' : 'real';
                $confidence = round($res['confidence'] * 100);
                if ($isHoax == 1) {
                    $hoaxPercentage = $confidence;
                    $factPercentage = 100 - $hoaxPercentage;
                } else {
                    $factPercentage = $confidence;
                    $hoaxPercentage  = 100 - $factPercentage;
                }
                $finalLabel = $isHoax == 1 ? 'FAKE' : 'FAKTA';
                $summary = 'Analisis gambar menunjukkan indikasi ' . $finalLabel . ' dengan tingkat kepercayaan ' . $confidence . '%.';
                
                ImageSearchResults::create([
                    'request_id' => $newReq->id,
                    'source_url' => $links,
                    'similarity_score' => $res['similarity_score'],
                    'mean_date_score' => $res['avg_date_scaled'],
                    'summary' => $summary ?? '-',
                ]);
                
                $newReq->update([
                    'final_label' => $finalLabel,
                    'final_confidence' => $res['confidence'],
                    'status' => 'completed'
                ]);
                

                // 7. RETURN SESUAI FIGMA
                return response()->json([
                    'status' => 'success',
                    'verdict' => $finalLabel,
                    'confidence' => $hoaxPercentage,
                    'summary' => $summary ?? '-',
                    'sources' => $links,
                    'raw_data' => [
                        'request_id' => $newReq->id,
                    ],
                    'data' => [
                        'indication' => $finalLabel,
                        'confidence_score' => [
                            'fake' => $hoaxPercentage,
                            'real' => $factPercentage
                        ],
                        'image_preview' => $url
                    ]
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'API Python Gagal'], 500);

        } catch (\Exception $e) {
            Log::error('Cloudinary ERROR: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}