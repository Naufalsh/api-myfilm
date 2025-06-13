<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'judul_film',
        'rating',
        'komentar',
        'poster_path',
    ];

    public $timestamps = false;
}


