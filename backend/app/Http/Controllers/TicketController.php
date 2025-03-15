<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TicketController extends Controller
{
    /**
     * Reserva asientos para una sesión
     */
    public function reserveSeats(Request $request)
    {
        try {
            // Validación de datos de entrada
            $request->validate([
                'screening_id' => 'required|exists:screenings,id',
                'seat_ids' => 'required|array',
                'seat_ids.*' => 'exists:seats,id'
            ]);
            
            $userId = Auth::id();
            $screeningId = $request->screening_id;
            $seatIds = $request->seat_ids;
            
            // Obtener la sesión
            $screening = Screening::findOrFail($screeningId);
            
            // VALIDACIÓN 1: Verificar que el usuario no tenga más de 10 entradas para esta sesión
            if (count($seatIds) > 10) {
                return response()->json([
                    'message' => 'No se pueden reservar más de 10 asientos por sesión'
                ], 400);
            }
            
            // VALIDACIÓN 2: Verificar que el usuario no tenga entradas para otra sesión futura
            $hasFutureTickets = $this->userHasFutureTickets($userId, $screeningId);
            if ($hasFutureTickets) {
                return response()->json([
                    'message' => 'Ya tienes entradas para otra sesión futura. No puedes reservar entradas para múltiples sesiones futuras a la vez.'
                ], 400);
            }
            
            // Verificar disponibilidad de asientos
            $unavailableSeats = Seat::whereIn('id', $seatIds)
                ->where('status', 'busy')
                ->get();
                
            if ($unavailableSeats->count() > 0) {
                return response()->json([
                    'message' => 'Algunos asientos seleccionados no están disponibles',
                    'unavailable_seats' => $unavailableSeats->pluck('number')
                ], 400);
            }
            
            DB::beginTransaction();
            
            $totalPrice = 0;
            $tickets = [];
            
            // Crear tickets para cada asiento y calcular el precio total
            foreach ($seatIds as $seatId) {
                $seat = Seat::findOrFail($seatId);
                
                // Calcular precio según si el asiento es VIP y si es día del espectador
                $price = $screening->calculatePrice($seat->number);
                $totalPrice += $price;
                
                // Crear ticket individual para este asiento
                $ticket = Ticket::create([
                    'user_id' => $userId,
                    'screening_id' => $screeningId,
                    'seat_id' => $seatId,
                    'status' => 'reserved',
                    'total_pay' => $price,
                    'purchase_date' => now()
                ]);
                
                // Actualizar estado del asiento
                $seat->update(['status' => 'busy']);
                
                $tickets[] = $ticket;
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Reserva realizada con éxito',
                'tickets' => $tickets,
                'total_price' => $totalPrice
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al realizar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verifica si el usuario tiene tickets futuros para otras sesiones
     */
    private function userHasFutureTickets($userId, $currentScreeningId)
    {
        $now = Carbon::now();
        
        // Obtener los tickets futuros del usuario que no sean para la sesión actual
        $futureTickets = Ticket::where('user_id', $userId)
            ->where('status', 'reserved')
            ->whereHas('screening', function($query) use ($now, $currentScreeningId) {
                $query->where('date_time', '>', $now)
                      ->where('id', '!=', $currentScreeningId);
            })
            ->count();
            
        return $futureTickets > 0;
    }
    
    /**
     * Confirma el pago y finaliza la reserva
     */
    public function confirmTickets(Request $request)
    {
        try {
            $request->validate([
                'ticket_ids' => 'required|array',
                'ticket_ids.*' => 'exists:tickets,id'
            ]);
            
            $userId = Auth::id();
            $ticketIds = $request->ticket_ids;
            
            // Verificar que los tickets pertenecen al usuario
            $tickets = Ticket::whereIn('id', $ticketIds)
                ->where('user_id', $userId)
                ->where('status', 'reserved')
                ->get();
                
            if ($tickets->count() != count($ticketIds)) {
                return response()->json([
                    'message' => 'Algunos tickets no pertenecen al usuario o ya han sido confirmados'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Actualizar estado de los tickets
            foreach ($tickets as $ticket) {
                $ticket->update(['status' => 'purchased']);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Compra confirmada con éxito',
                'tickets' => $tickets
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al confirmar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancela tickets reservados
     */
    public function cancelTickets(Request $request)
    {
        try {
            $request->validate([
                'ticket_ids' => 'required|array',
                'ticket_ids.*' => 'exists:tickets,id'
            ]);
            
            $userId = Auth::id();
            $ticketIds = $request->ticket_ids;
            
            // Verificar que los tickets pertenecen al usuario
            $tickets = Ticket::whereIn('id', $ticketIds)
                ->where('user_id', $userId)
                ->where('status', 'reserved')
                ->get();
                
            if ($tickets->count() != count($ticketIds)) {
                return response()->json([
                    'message' => 'Algunos tickets no pertenecen al usuario o no pueden ser cancelados'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Liberar asientos y eliminar tickets
            foreach ($tickets as $ticket) {
                // Liberar asiento
                $seat = $ticket->seat;
                if ($seat) {
                    $seat->update(['status' => 'available']);
                }
                
                // Eliminar ticket
                $ticket->delete();
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Tickets cancelados con éxito'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cancelar los tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtiene los tickets del usuario actual
     */
    public function getUserTickets()
    {
        $userId = Auth::id();
        
        $tickets = Ticket::with(['screening.movie', 'screening.auditorium', 'seat'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($tickets);
    }
    
    /**
     * Obtiene detalles de un ticket específico
     */
    public function getTicketDetails($id)
    {
        $userId = Auth::id();
        
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
            
        return response()->json($ticket);
    }
    
    /**
     * Genera un boleto imprimible con todos los detalles
     */
    public function generateTicketPdf($id)
    {
        $userId = Auth::id();
        
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereHas('user', function($q) {
                          $q->whereHas('role', function($r) {
                              $r->where('name', 'guest');
                          });
                      });
            })
            ->firstOrFail();
        
        // En este punto se podría generar un PDF real, pero para este proyecto
        // simplemente devolveremos los datos formateados para que el frontend
        // pueda mostrarlos en formato de ticket
        
        $ticketData = [
            'ticket_id' => $ticket->id,
            'purchase_date' => $ticket->purchase_date,
            'movie' => [
                'title' => $ticket->screening->movie->title,
                'duration' => $ticket->screening->movie->duration,
                'classification' => $ticket->screening->movie->classification
            ],
            'screening' => [
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name
            ],
            'seat' => [
                'number' => $ticket->seat->number,
                'is_vip' => $ticket->seat->isVip()
            ],
            'user' => [
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'snack' => $ticket->snack ? [
                'name' => $ticket->snack->name,
                'quantity' => $ticket->snack_quantity,
                'price' => $ticket->snack->price * $ticket->snack_quantity
            ] : null,
            'total_price' => $ticket->total_pay
        ];
        
        return response()->json([
            'message' => 'Ticket generado con éxito',
            'ticket_data' => $ticketData
        ]);
    }
    
    /**
     * Permite a usuarios invitados acceder a sus tickets mediante un token temporal
     */
    public function getGuestTicket($id, $token)
    {
        // Buscar el ticket y su usuario asociado
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->whereHas('user', function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'guest');
                });
            })
            ->firstOrFail();
            
        // Validar que el token sea válido
        $expectedToken = md5($ticket->user->email . $ticket->id . config('app.key'));
        
        if ($token !== $expectedToken) {
            return response()->json([
                'message' => 'Token de acceso inválido'
            ], 403);
        }
        
        // Generar los mismos datos que en generateTicketPdf
        $ticketData = [
            'ticket_id' => $ticket->id,
            'purchase_date' => $ticket->purchase_date,
            'movie' => [
                'title' => $ticket->screening->movie->title,
                'duration' => $ticket->screening->movie->duration,
                'classification' => $ticket->screening->movie->classification
            ],
            'screening' => [
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name
            ],
            'seat' => [
                'number' => $ticket->seat->number,
                'is_vip' => $ticket->seat->isVip()
            ],
            'user' => [
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'snack' => $ticket->snack ? [
                'name' => $ticket->snack->name,
                'quantity' => $ticket->snack_quantity,
                'price' => $ticket->snack->price * $ticket->snack_quantity
            ] : null,
            'total_price' => $ticket->total_pay
        ];
        
        return response()->json([
            'message' => 'Ticket encontrado',
            'ticket_data' => $ticketData
        ]);
    }
}