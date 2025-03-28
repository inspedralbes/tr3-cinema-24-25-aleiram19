<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTicketsController extends Controller
{
    /**
     * Obtiene todas las entradas (tickets) de un usuario
     */
    public function index()
    {
        $userId = Auth::id();
        
        $tickets = Ticket::with(['screening.movie', 'screening.auditorium', 'seat'])
            ->where('user_id', $userId)
            ->orderBy('purchase_date', 'desc')
            ->get();
            
        return response()->json($tickets);
    }
    
    /**
     * Obtiene los detalles de una entrada específica
     */
    public function show($id)
    {
        $userId = Auth::id();
        
        $ticket = Ticket::with([
            'screening.movie', 
            'screening.auditorium',
            'seat',
            'user'
        ])
        ->where('id', $id)
        ->where('user_id', $userId)
        ->firstOrFail();
        
        return response()->json($ticket);
    }
    
    /**
     * Verifica si un usuario puede comprar entradas para una sesión
     */
    public function canBuyTickets($screeningId)
    {
        $userId = Auth::id();
        
        // Verificar si el usuario ya tiene entradas para otras sesiones futuras
        $hasFutureTickets = $this->userHasFutureTicketsExcept($userId, $screeningId);
        
        // Contar cuántas entradas tiene el usuario para esta sesión
        $currentTicketsCount = Ticket::where('user_id', $userId)
            ->where('screening_id', $screeningId)
            ->count();
            
        return response()->json([
            'can_buy' => !$hasFutureTickets && $currentTicketsCount < 10,
            'has_future_tickets' => $hasFutureTickets,
            'current_tickets_count' => $currentTicketsCount,
            'max_allowed' => 10,
            'remaining' => max(0, 10 - $currentTicketsCount)
        ]);
    }
    
    /**
     * Verifica si el usuario tiene entradas para otras sesiones futuras
     */
    private function userHasFutureTicketsExcept($userId, $currentScreeningId)
    {
        $futureTickets = Ticket::where('user_id', $userId)
            ->where('screening_id', '!=', $currentScreeningId)
            ->whereHas('screening', function($query) {
                $query->where('date_time', '>', now());
            })
            ->count();
            
        return $futureTickets > 0;
    }
}