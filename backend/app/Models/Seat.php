<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $table = 'seats';
    
    protected $fillable = [
        'auditorium_id',
        'number',
        'status'
    ];

    // Relación con auditorio
    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    // Relación con reservas
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    // Atributos computados para mostrar si un asiento es VIP
    protected $appends = ['is_vip'];
    
    // Verificar si el asiento es VIP (fila F)
    public function isVip()
    {
        // Obtener la fila (primer carácter del número de asiento)
        $row = strtoupper(substr($this->number, 0, 1));
        
        // La fila F es VIP
        return $row === 'F';
    }
    
    // Accessor para el atributo is_vip
    public function getIsVipAttribute()
    {
        return $this->isVip();
    }
}
