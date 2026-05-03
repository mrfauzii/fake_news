<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedbacks extends Model
{
    protected $fillable = [
        'user_id',
        'request_id',
        'feedback'
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
