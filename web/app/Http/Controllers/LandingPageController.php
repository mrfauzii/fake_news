<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Requests;
use App\Models\KnowledgeBase;

class LandingPageController extends Controller
{
    public function index()
    {
        // Informasi Terverifikasi
        $totalVerified = Requests::whereNotNull('final_label')
            ->count();


        // Hoax Terdeteksi %
        $totalFake = Requests::where('final_label', 'fake')
            ->count();

        $hoaxPercentage = $totalVerified > 0
            ? round(($totalFake / $totalVerified) * 100)
            : 0;


        // Data Hoax Knowledge Base
        $totalHoax = KnowledgeBase::count();


        // User Terdaftar
        $totalUsers = Users::count();


        return view('landing_page.landing', compact(
            'totalVerified',
            'hoaxPercentage',
            'totalHoax',
            'totalUsers'
        ));
    }
}