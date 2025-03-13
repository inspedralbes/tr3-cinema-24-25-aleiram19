<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
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
            $hasFutureBooking = $this->userHasFutureBookings($userId, $screeningId);
            if ($hasFutureBooking) {
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
            $bookings = [];
            
            // Crear reservas para cada asiento y calcular el precio total
            foreach ($seatIds as $seatId) {
                $seat = Seat::findOrFail($seatId);
                
                // Calcular precio según si el asiento es VIP y si es día del espectador
                $price = $screening->calculatePrice($seat->number);
                $totalPrice += $price;
                
                // Crear reserva
                $booking = Booking::create([
                    'user_id' => $userId,
                    'screening_id' => $screeningId,
                    'seat_id' => $seatId,
                    'status' => 'reserved'
                ]);
                
                // Actualizar estado del asiento
                $seat->update(['status' => 'busy']);
                
                $bookings[] = $booking;
            }
            
            // Crear ticket
            $ticket = Ticket::create([
                'user_id' => $userId,
                'screening_id' => $screeningId,
                'quantity' => count($seatIds),
                'total_pay' => $totalPrice,
                'purchase_date' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Reserva realizada con éxito',
                'ticket' => $ticket,
                'bookings' => $bookings,
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
     * Verifica si el usuario tiene reservas futuras para otras sesiones
     */
    private function userHasFutureBookings($userId, $currentScreeningId)
    {
        $now = Carbon::now();
        
        // Obtener las reservas futuras del usuario que no sean para la sesión actual
        $futureBookings = Booking::where('user_id', $userId)
            ->where('status', 'reserved')
            ->whereHas('screening', function($query) use ($now, $currentScreeningId) {
                $query->where('date_time', '>', $now)
                      ->where('id', '!=', $currentScreeningId);
            })
            ->count();
            
        return $futureBookings > 0;
    }
    
    /**
     * Confirma el pago y finaliza la reserva
     */
    public function confirmBooking(Request $request)
    {
        try {
            $request->validate([
                'booking_ids' => 'required|array',
                'booking_ids.*' => 'exists:bookings,id'
            ]);
            
            $userId = Auth::id();
            $bookingIds = $request->booking_ids;
            
            // Verificar que las reservas pertenecen al usuario
            $bookings = Booking::whereIn('id', $bookingIds)
                ->where('user_id', $userId)
                ->where('status', 'reserved')
                ->get();
                
            if ($bookings->count() != count($bookingIds)) {
                return response()->json([
                    'message' => 'Algunas reservas no pertenecen al usuario o ya han sido confirmadas'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Actualizar estado de las reservas
            foreach ($bookings as $booking) {
                $booking->update(['status' => 'purchased']);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Reserva confirmada con éxito',
                'bookings' => $bookings
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al confirmar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancela una reserva
     */
    public function cancelBooking(Request $request)
    {
        try {
            $request->validate([
                'booking_ids' => 'required|array',
                'booking_ids.*' => 'exists:bookings,id'
            ]);
            
            $userId = Auth::id();
            $bookingIds = $request->booking_ids;
            
            // Verificar que las reservas pertenecen al usuario
            $bookings = Booking::whereIn('id', $bookingIds)
                ->where('user_id', $userId)
                ->where('status', 'reserved')
                ->get();
                
            if ($bookings->count() != count($bookingIds)) {
                return response()->json([
                    'message' => 'Algunas reservas no pertenecen al usuario o no pueden ser canceladas'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Liberar asientos y eliminar reservas
            foreach ($bookings as $booking) {
                // Liberar asiento
                $seat = $booking->seat;
                $seat->update(['status' => 'available']);
                
                // Eliminar reserva
                $booking->delete();
            }
            
            // Buscar y eliminar el ticket relacionado si todas las reservas para esa sesión se cancelaron
            $screening = $bookings->first()->screening;
            $remainingBookings = Booking::where('user_id', $userId)
                ->where('screening_id', $screening->id)
                ->count();
                
            if ($remainingBookings == 0) {
                Ticket::where('user_id', $userId)
                    ->where('screening_id', $screening->id)
                    ->delete();
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Reserva cancelada con éxito'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cancelar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
