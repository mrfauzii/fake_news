<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Riwayat extends Model
{
    protected $table = 'riwayats'; // nama tabel di database

    protected $fillable = [
        'judul',
        'deskripsi',
        'persentase_hoax',
        'gambar'
    ];
}