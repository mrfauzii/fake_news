<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopulerHistoryController extends Controller
{
    public function index()
    {
        // 1. Ambil dan hitung (grouping) pencarian yang sama berdasarkan teks dan bulan
        $histories = DB::table('history_view')
            ->select(
                'input_text',
                'final_label',
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('final_label') // Hanya ambil yang sudah selesai dideteksi
            ->groupBy('input_text', 'final_label', 'year', 'month')
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

            $popularItems[] = [
                'category'  => $category,
                'period'    => $period,
                'badge'     => $badge,
                'count'     => $row->count,
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
