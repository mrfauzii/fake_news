<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopulerHistoryController extends Controller
{
    public function index()
    {
        // 1. Ambil dan hitung (grouping) pencarian yang sama berdasarkan teks dan bulan
        $histories = DB::table('user_interactions as ui')
            ->join('requests as r', 'ui.request_id', '=', 'r.id')
            ->leftJoin('stage1_results as s1', 'r.id', '=', 's1.request_id')
            ->leftJoin('knowledge_base as kb', 's1.knowledge_id', '=', 'kb.id')
            ->leftJoin('stage2_results as s2', 'r.id', '=', 's2.request_id')
            ->whereNull('r.deleted_at')
            ->select(
                    'r.input_text',
                    'r.final_label',
                    'r.final_confidence',
                    'kb.fact_text as fact_text',
                    's2.summary_text as summary_text',
                    DB::raw('YEAR(r.created_at) as year'),
                    DB::raw('MONTH(r.created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
            ->whereNotNull('r.final_label')
            ->groupBy(
                'r.input_text',
                'r.final_label',
                'r.final_confidence',
                'kb.fact_text',
                's2.summary_text',
                'year',
                'month'
            )
            ->orderByDesc('count')
            ->get();

        // 2. Kamus Nama Bulan
        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $popularItems = [];
    
        // 3. Format data agar SAMA PERSIS dengan contentBlueprints di file JS kamu
        foreach ($histories as $idx => $row) {
            $category = strtolower($row->final_label) === 'fake' ? 'hoax' : 'fakta';
            $badge = strtoupper($category);
            $period = $bulanIndo[$row->month] . ' ' . $row->year;

            $summary = '-';
            if ($row->summary_text) {
                $summary = $row->summary_text;
            } elseif ($row->fact_text) {
                $summary = $row->fact_text;
            }

            $popularItems[] = [
                'category'  => $category,
                'period'    => $period,
                'badge'     => $badge,
                'confidence' => $row->final_confidence,
                'count'     => $row->count,
                'summary' => $summary,
                'headline'  => Str::limit($row->input_text, 60), // Potong teks untuk judul
                'excerpt'   => $row->input_text, // Teks utuh untuk penjelasan
                'query'     => $row->input_text, // Teks yang akan di-parsing ke URL ?informasi=...
                'sortOrder' => $idx + 1
            ];
        }

        // 4. Set default periode pencarian ke bulan dan tahun saat ini (Mei 2026)
        $defaultPeriod = $bulanIndo[date('n')] . ' ' . date('Y');

        // Pastikan nama view ini disesuaikan dengan lokasimu (misal: 'user.pencarian-terpopuler')
        return view('user.pencarian-terpopuler', compact('popularItems', 'defaultPeriod'));
    }
}
