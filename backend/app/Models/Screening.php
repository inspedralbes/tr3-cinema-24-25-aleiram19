<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    use HasFactory;

    protected $table = 'screenings';
    
    protected $fillable = [
        'movie_id',
        'auditorium_id',
        'date_time',
        'price',
        'is_special'
    ];

    // Relación con película
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

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

    // Relación con tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    /**
     * Método para calcular precio según tipo de asiento y si es día especial
     * 
     * Existen 4 precios diferentes:
     * - Precio normal: 6€
     * - Precio VIP: 8€ (fila F)
     * - Precio día del espectador normal: 4€ (cuando is_special=true)
     * - Precio día del espectador VIP: 6€ (fila F y is_special=true)
     *
     * @param string $seatRow Número o identificador del asiento (ej. A1, F5, etc)
     * @return float Precio calculado del asiento
     */
    public function calculatePrice($seatRow)
    {
        // Convertir a mayúscula y tomar el primer carácter (por ejemplo, "F1" -> "F")
        $row = strtoupper(substr($seatRow, 0, 1));
        
        // Verificar si es fila VIP (fila F)
        $isVipSeat = ($row === 'F');
        
        // Precios base según las especificaciones de negocio
        $normalPrice = 6; // Precio normal
        $vipPrice = 8;    // Precio VIP
        $specialNormalPrice = 4; // Precio día del espectador normal
        $specialVipPrice = 6;    // Precio día del espectador VIP
        
        // Determinar el precio según el tipo de asiento y si es sesión especial
        if ($this->is_special) {
            return $isVipSeat ? $specialVipPrice : $specialNormalPrice;
        } else {
            return $isVipSeat ? $vipPrice : $normalPrice;
        }
    }
    
    /**
     * Obtener la matriz de precios para mostrar en la interfaz
     * 
     * @return array Matriz con los 4 tipos de precios
     */
    public function getPriceMatrix()
    {
        return [
            'normal' => $this->is_special ? 4 : 6,
            'vip' => $this->is_special ? 6 : 8,
            'is_special_day' => $this->is_special
        ];
    }
    
    // Verificar disponibilidad de asientos reservados por usuario
    public function countUserSeats($userId)
    {
        return $this->bookings()
            ->where('user_id', $userId)
            ->where('status', 'reserved')
            ->count();
    }
}
