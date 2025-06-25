<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{

    protected $table = 'galleries';


    protected $fillable = [
        'Authorization',
        'lokasi',
        'tanggal',
        'deskripsi',
        'gambar',
    ];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->gambar);
    }
}