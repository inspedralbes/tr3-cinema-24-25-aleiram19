<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';
    
    protected $fillable = [
        'title',
        'director',
        'actors',
        'description',
        'trailer',
        'duration',
        'movie_genre',
        'release_date',
        'image'
    ];

    // Relación con sesiones
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
