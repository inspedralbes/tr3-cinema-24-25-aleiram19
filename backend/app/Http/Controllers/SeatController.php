<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Screening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    /**
     * Muestra los asientos disponibles para una sesión específica.
     */
    public function getAvailableSeats($screeningId)
    {
        $screening = Screening::findOrFail($screeningId);
        $auditorium = $screening->auditorium;
        
        // Obtener todos los asientos del auditorio
        $seats = Seat::where('auditorium_id', $auditorium->id)->get();
        
        // Preparar respuesta con información de asientos y precios
        $seatData = [];
        
        foreach ($seats as $seat) {
            // Determinar si es asiento VIP
            $isVip = $seat->isVip();
            
            // Calcular precio según el tipo de asiento y si es día especial
            $price = $screening->calculatePrice($seat->number);
            
            $seatData[] = [
                'id' => $seat->id,
                'number' => $seat->number,
                'status' => $seat->status,
                'is_vip' => $isVip,
                'price' => $price,
                'row' => substr($seat->number, 0, 1),
                'column' => substr($seat->number, 1)
            ];
        }
        
        // Agrupar por filas para facilitar el renderizado en el frontend
        $seatsByRow = collect($seatData)->groupBy('row')->toArray();
        
        return response()->json([
            'screening' => $screening,
            'auditorium' => $auditorium,
            'seats_by_row' => $seatsByRow,
            'price_info' => $screening->getPriceMatrix()
        ]);
    }

    /**
     * Actualiza el estado de un asiento.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,maintenance'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seat = Seat::findOrFail($id);
        
        // Si el asiento está ocupado por una reserva, no permitir cambiarlo a disponible
        if ($seat->status === 'busy' && $request->status === 'available') {
            // Verificar si está asociado a tickets activos
            $hasActiveTickets = $seat->tickets()
                ->whereIn('status', ['reserved', 'purchased'])
                ->exists();
                
            if ($hasActiveTickets) {
                return response()->json([
                    'message' => 'No se puede cambiar el estado del asiento porque tiene entradas asociadas'
                ], 400);
            }
        }
        
        $seat->update([
            'status' => $request->status
        ]);
        
        return response()->json([
            'message' => 'Estado del asiento actualizado correctamente',
            'seat' => $seat
        ]);
    }
    
    /**
     * Cambia todos los asientos de un auditorio a disponibles.
     */
    public function resetSeats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auditorium_id' => 'required|exists:auditoriums,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar si hay sesiones futuras para este auditorio
        $hasFutureScreenings = Screening::where('auditorium_id', $request->auditorium_id)
            ->where('date_time', '>', now())
            ->exists();
            
        if ($hasFutureScreenings) {
            return response()->json([
                'message' => 'No se pueden resetear los asientos porque hay sesiones programadas para este auditorio'
            ], 400);
        }
        
        // Cambiar todos los asientos a disponibles
        Seat::where('auditorium_id', $request->auditorium_id)
            ->update(['status' => 'available']);
            
        return response()->json([
            'message' => 'Asientos reseteados correctamente'
        ]);
    }
}