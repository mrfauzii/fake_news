<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInteractions extends Model
{
    use SoftDeletes;
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
