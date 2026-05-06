<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $data = [

            [
                'judul' => '[KABAR PENTING]',
                'deskripsi' => 'Pemerintah membagikan Bantuan Sosial Ramadan sebesar Rp1,5 juta bagi warga yang memiliki BPJS Kesehatan. Daftar sekarang melalui link Telegram ini: bit.ly/bansos-ramadhan2026 agar dana segera cair.',
                'gambar' => null,
                'hoax' => 70,
                'benar' => 30,
            ],

            [
                'judul' => 'Informasi PDAM',
                'deskripsi' => null,
                'gambar' => 'img/contoh-berita.png',
                'hoax' => 20,
                'benar' => 80,
            ],

            [
                'judul' => '[HOAX TERBARU]',
                'deskripsi' => 'Beredar informasi bahwa pemerintah akan memberikan bantuan pulsa gratis melalui WhatsApp. Informasi tersebut tidak benar.',
                'gambar' => null,
                'hoax' => 85,
                'benar' => 15,
            ],

        ];

        return view('admin.riwayat', compact('data'));
    }
}