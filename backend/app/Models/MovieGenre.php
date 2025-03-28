<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model
{
    use HasFactory;

    protected $table = 'movie_genres';
    
    protected $fillable = [
        'name'
    ];

    // Relación con películas
    public function movies()
    {
        return $this->hasMany(Movie::class, 'movie_genre_id');
    }
}
