<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageSearchResults extends Model
{
    protected $fillable = [
        'request_id',
        'source_url',
        'similarity_score',
        'mean_date_score'
    ];

    protected $casts = [
        'source_url' => 'array'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }
}
