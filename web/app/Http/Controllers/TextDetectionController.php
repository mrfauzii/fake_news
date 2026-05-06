<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Requests;
use App\Models\KnowledgeBase;
use App\Models\Stage1Results;
use App\Models\Stage2Result;

class TextDetectionController extends Controller
{
    public function detectText(Request $request)
    {
        $request->validate([
            'input_text' => 'required|string'
        ]);

        // ==========================================
        // INPUT USER
        // ==========================================
        $inputText = $request->input('input_text');

        // ==========================================
        // SIMPAN REQUEST AWAL (Di luar transaksi)
        // ==========================================
        // Tetap tersimpan sebagai 'error' jika di tengah jalan proses gagal
        $requestData = Requests::create([
            'input_text' => $inputText,
            'status' => 'error'
        ]);

        $requestId = $requestData->id;

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // ==========================================
            // HIT API PYTHON
            // ==========================================
            $response = Http::timeout(120)->post(
                'http://127.0.0.1:8000/text-detection',
                [
                    'query' => $inputText,
                    'id_request' => $requestId
                ]
            );

            if (!$response->successful()) {
                throw new \Exception('Gagal terhubung ke AI API');
            }

            $aiApiResponse = $response->json();

            // ==========================================
            // VALIDASI RESPONSE
            // ==========================================
            if (!isset($aiApiResponse['status'])) {
                throw new \Exception('Response AI tidak memiliki status');
            }

            if ($aiApiResponse['status'] !== 'success') {
                throw new \Exception('Response AI gagal');
            }

            // ==========================================
            // CEK STAGE
            // ==========================================
            $stage = $aiApiResponse['stage'] ?? 'stage_1';

            // =====================================================
            // ===================== STAGE 1 ========================
            // =====================================================
            if ($stage === 'stage_1') {

                if (
                    !isset($aiApiResponse['data']) ||
                    empty($aiApiResponse['data'])
                ) {
                    throw new \Exception('Data stage 1 kosong');
                }

                $aiData = $aiApiResponse['data'][0];

                // ==========================================
                // LABEL & CONFIDENCE (STAGE 1)
                // ==========================================
                $finalLabel = strtolower($aiData['category'] ?? '') === 'hoaks' ? 'fake' : 'real';

                // Ambil nilai asli (distance) dari AI
                $similarityScore = $aiData['score'] ?? 1;

                // final_confidence = 1 - similarity_score
                $finalConfidence = 1 - $similarityScore;

                // Pastikan nilainya tidak keluar batas (0 sampai 1)
                if ($finalConfidence < 0) $finalConfidence = 0;
                if ($finalConfidence > 1) $finalConfidence = 1;

                // Persentase untuk ditampilkan ke frontend (misal: 91.23)
                $confidencePercentage = round($finalConfidence * 100, 2);

                // ==========================================
                // UPDATE REQUEST
                // ==========================================
                $requestData->update([
                    'final_label' => $finalLabel,
                    'final_confidence' => $finalConfidence, // Disimpan format desimal
                    'status' => 'stage1'
                ]);

                // ==========================================
                // KNOWLEDGE BASE & STAGE 1 RESULTS
                // ==========================================
                $knowledgeId = $aiData['id'] ?? null;

                if ($knowledgeId) {
                    $knowledge = KnowledgeBase::find($knowledgeId);

                    if (!$knowledge) {
                        KnowledgeBase::create([
                            'id' => $knowledgeId,
                            'title' => $aiData['title'] ?? '-',
                            'hoax_text' => $aiData['hoax_text'] ?? null,
                            'fact_text' => $aiData['fact_text'] ?? null,
                            'category' => $aiData['category'] ?? null,
                        ]);
                    }

                    Stage1Results::create([
                        'request_id' => $requestId,
                        'knowledge_id' => $knowledgeId,
                        'similarity_score' => $similarityScore, // Simpan score asli dari AI
                        'nli_score' => $aiData['nli_score'] ?? 0,
                        'is_stop' => true
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => 'stage1',
                    'data' => [
                        'request_id' => $requestId,
                        'label' => $finalLabel,
                        'confidence' => $confidencePercentage, // Return persentase
                        'title' => $aiData['title'] ?? null,
                        'category' => $aiData['category'] ?? null,
                        'hoax_text' => $aiData['hoax_text'] ?? null,
                        'fact_text' => $aiData['fact_text'] ?? null,
                        'nli_score' => $aiData['nli_score'] ?? null,
                        'similarity_score' => $similarityScore
                    ]
                ]);
            }

            // =====================================================
            // ===================== STAGE 2 ========================
            // =====================================================
            if ($stage === 'stage_2') {

                $prediction = $aiApiResponse['prediction'] ?? 0;
                $finalLabel = $prediction == 1 ? 'fake' : 'real';

                // ==========================================
                // LABEL & CONFIDENCE (STAGE 2)
                // ==========================================
                $rawConfidence = $aiApiResponse['confidence'] ?? 0;
                $finalConfidence = $rawConfidence;

                // Pastikan nilainya tidak keluar batas (0 sampai 1)
                if ($finalConfidence < 0) $finalConfidence = 0;
                if ($finalConfidence > 1) $finalConfidence = 1;

                // Persentase untuk ditampilkan ke frontend
                $confidencePercentage = round($finalConfidence * 100, 2);

                $featureVector = $aiApiResponse['feature_vector'] ?? [];

                // ==========================================
                // UPDATE REQUEST
                // ==========================================
                $requestData->update([
                    'final_label' => $finalLabel,
                    'final_confidence' => $finalConfidence, // Disimpan format desimal agar konsisten dengan tabel
                    'status' => 'stage2'
                ]);

                // ==========================================
                // SIMPAN STAGE 2
                // ==========================================
                Stage2Result::create([
                    'request_id' => $requestId,
                    'time_credibility' => $featureVector['time_consistency_score'] ?? null,
                    'title_credibility' => $featureVector['message_similarity_score'] ?? null,
                    'mean_entailment' => $featureVector['mean_entailment'] ?? null,
                    'mean_contradiction' => $featureVector['mean_contradiction'] ?? null,
                    'std_contradiction' => $featureVector['std_contradiction'] ?? null,
                    'url' => $aiApiResponse['urls'] ?? []
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'stage2',
                    'data' => [
                        'request_id' => $requestId,
                        'label' => $finalLabel,
                        'confidence' => $confidencePercentage, // Return persentase
                        'urls' => $aiApiResponse['urls'] ?? [],
                        'feature_vector' => $featureVector
                    ]
                ]);
            }

            throw new \Exception('Stage tidak dikenali');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deteksi teks: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
