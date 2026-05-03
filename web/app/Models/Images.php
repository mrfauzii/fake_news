<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $fillable = [
        'file_path',
        'original_filename',
        'uploaded_by'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'uploaded_by');
    }

    public function requests()
    {
        return $this->hasMany(Requests::class);
    }
}
