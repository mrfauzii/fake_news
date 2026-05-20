<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapeSchedule extends Model
{
    protected $fillable = [
        'scheduled_at',
        'status',
    ];
}
