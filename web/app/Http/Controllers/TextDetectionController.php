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
    /**
     * ========================================================
     * 1. FUNGSI UTAMA & PEMANGGILAN API
     * ========================================================
     */
    public function detectText(Request $request)
    {
        $request->validate([
            'input_text' => 'required|string'
        ]);

        $inputText = $request->input('input_text');

        // Simpan request awal di luar transaksi agar tetap terekam jika error
        $requestData = Requests::create([
            'input_text' => $inputText,
            'status' => 'error'
        ]);

        $requestId = $requestData->id;

        DB::beginTransaction();

        try {
            // --- HIT API PYTHON ---
            $response = Http::timeout(120)->post('http://127.0.0.1:8000/text-detection', [
                'query' => $inputText,
                'id_request' => $requestId
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal terhubung ke AI API');
            }

            $aiApiResponse = $response->json();

            if (!isset($aiApiResponse['status']) || $aiApiResponse['status'] !== 'success') {
                throw new \Exception('Response AI gagal atau tidak valid');
            }
            // -----------------------

            $stage = $aiApiResponse['stage'] ?? 'stage_1';
            $responseData = [];

            // Arahkan ke fungsi spesifik berdasarkan Stage
            if ($stage === 'stage_1') {
                $responseData = $this->processStage1($aiApiResponse, $requestData);
            } elseif ($stage === 'stage_2') {
                $responseData = $this->processStage2($aiApiResponse, $requestData);
            } else {
                throw new \Exception('Stage tidak dikenali');
            }

            // Jika semua lancar, Commit DB dan kembalikan JSON
            DB::commit();

            return response()->json([
                'status' => $stage === 'stage_1' ? 'stage1' : 'stage2',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deteksi teks: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================
     * 2. FUNGSI KHUSUS: STAGE 1
     * ========================================================
     */
    private function processStage1(array $aiApiResponse, Requests $requestData): array
    {
        if (empty($aiApiResponse['data'])) {
            throw new \Exception('Data stage 1 kosong');
        }

        $aiData = $aiApiResponse['data'][0];
        $requestId = $requestData->id;

        // Label & Confidence
        $finalLabel = strtolower($aiData['category'] ?? '') === 'hoaks' ? 'fake' : 'real';
        $similarityScore = $aiData['score'] ?? 1;

        $finalConfidence = max(0, min(1, 1 - $similarityScore)); // Pastikan antara 0 dan 1
        $confidencePercentage = round($finalConfidence * 100, 2);

        // Update Tabel Request
        $requestData->update([
            'final_label' => $finalLabel,
            'final_confidence' => $finalConfidence,
            'status' => 'stage1'
        ]);

        // Simpan Knowledge Base & Stage 1 Result
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
                'similarity_score' => $similarityScore,
                'nli_score' => $aiData['nli_score'] ?? 0,
                'is_stop' => true
            ]);
        }

        return [
            'request_id' => $requestId,
            'label' => $finalLabel,
            'confidence' => $confidencePercentage,
            'title' => $aiData['title'] ?? null,
            'category' => $aiData['category'] ?? null,
            'hoax_text' => $aiData['hoax_text'] ?? null,
            'fact_text' => $aiData['fact_text'] ?? null,
            'nli_score' => $aiData['nli_score'] ?? null,
            'similarity_score' => $similarityScore
        ];
    }

    /**
     * ========================================================
     * 3. FUNGSI KHUSUS: STAGE 2
     * ========================================================
     */
    private function processStage2(array $aiApiResponse, Requests $requestData): array
    {
        $requestId = $requestData->id;
        $prediction = $aiApiResponse['prediction'] ?? 0;
        $finalLabel = $prediction == 1 ? 'fake' : 'real';

        // Label & Confidence
        $rawConfidence = $aiApiResponse['confidence'] ?? 0;
        $finalConfidence = max(0, min(1, $rawConfidence)); // Pastikan antara 0 dan 1
        $confidencePercentage = round($finalConfidence * 100, 2);

        $featureVector = $aiApiResponse['feature_vector'] ?? [];

        // Update Tabel Request
        $requestData->update([
            'final_label' => $finalLabel,
            'final_confidence' => $finalConfidence,
            'status' => 'stage2'
        ]);

        // Simpan Stage 2 Result
        Stage2Result::create([
            'request_id' => $requestId,
            'time_credibility' => $featureVector['time_consistency_score'] ?? null,
            'title_credibility' => $featureVector['message_similarity_score'] ?? null,
            'mean_entailment' => $featureVector['mean_entailment'] ?? null,
            'mean_contradiction' => $featureVector['mean_contradiction'] ?? null,
            'std_contradiction' => $featureVector['std_contradiction'] ?? null,
            'url' => $aiApiResponse['urls'] ?? []
        ]);

        return [
            'request_id' => $requestId,
            'label' => $finalLabel,
            'confidence' => $confidencePercentage,
            'urls' => $aiApiResponse['urls'] ?? [],
            'feature_vector' => $featureVector
        ];
    }
}
