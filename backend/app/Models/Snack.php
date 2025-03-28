<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    use HasFactory;

    protected $table = 'snacks';
    
    protected $fillable = [
        'name',
        'price'
    ];

    // Relación con tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
