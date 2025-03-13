<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    
    protected $fillable = [
        'user_id',
        'screening_id',
        'seat_id',
        'status'
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con sesión
    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    // Relación con asiento
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
