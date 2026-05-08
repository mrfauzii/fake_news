<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageResults extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'link_img',
        'title',
        'created_at'
    ];

    public function stage1Results()
    {
        return $this->hasMany(Stage1Results::class, 'image_results_id');
    }
}