<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    protected $table = 'auditoriums';
    
    protected $fillable = [
        'name',
        'capacity',
        'type'
    ];

    // Relación con asientos
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    // Relación con sesiones
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
