<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInteractions extends Model
{
    protected $fillable = [
        'user_id',
        'request_id',
        'source_channel',
        'interaction_type'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }
}
