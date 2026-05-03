<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage1Results extends Model
{
    protected $fillable = [
        'request_id',
        'knowledge_id',
        'similarity_score',
        'nli_score',
        'is_stop'
    ];

    protected $casts = [
        'is_stop' => 'boolean'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }

    public function knowledge()
    {
        return $this->belongsTo(KnowledgeBase::class, 'knowledge_id');
    }
}
