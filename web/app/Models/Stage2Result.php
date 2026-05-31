<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage2Result extends Model
{
    protected $fillable = [
        'request_id',
        'time_credibility',
        'title_credibility',
        'mean_entailment',
        'mean_neutral',        
        'mean_contradiction',
        'std_entailment',
        'std_neutral',
        'std_contradiction',
        'len_results',
        'summary_text',
        'url'
    ];

    protected $casts = [
        'url' => 'array'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }
}
