<?php

namespace App\Models;

use Dom\Text;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $fillable = [
        'input_text',
        'clean_text',
        'image_id',
        'final_label',
        'final_confidence',
        'status'
    ];

    public function image()
    {
        return $this->belongsTo(Images::class);
    }

    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class);
    }

    public function stage2Results()
    {
        return $this->hasMany(Stage2Result::class);
    }

    public function imageSearchResults()
    {
        return $this->hasMany(ImageSearchResults::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class);
    }

    public function interactions()
    {
        return $this->hasMany(UserInteractions::class);
    }
}
