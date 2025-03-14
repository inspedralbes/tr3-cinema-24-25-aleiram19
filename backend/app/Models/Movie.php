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
        'movie_genre_id',
        'release_date',
        'image'
    ];

    // Relación con sesiones
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }

    // Relación con el género de película
    public function movieGenre()
    {
        return $this->belongsTo(MovieGenre::class, 'movie_genre_id');
    }
}
