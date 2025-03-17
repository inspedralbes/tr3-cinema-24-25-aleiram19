<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Screening;
use App\Models\Auditorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    /**
     * Muestra un listado de todos los asientos.
     */
    public function index(Request $request)
    {
        $seats = Seat::with('auditorium')->get();
        
        // Forzar respuesta JSON para rutas de API
        if (strpos($request->path(), 'api/') === 0) {
            return response()->json($seats, 200, [
                'Content-Type' => 'application/json'
            ]);
        }
        
        // Si el cliente espera JSON también respondemos con JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($seats);
        }
        
        return view('seats.index', compact('seats'));
    }

    /**
     * Muestra el formulario para crear un nuevo asiento.
     */
    public function create()
    {
        $auditoriums = Auditorium::all();
        return view('seats.create', compact('auditoriums'));
    }

    /**
     * Almacena un nuevo asiento.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auditorium_id' => 'required|exists:auditoriums,id',
            'number' => 'required|string|max:10',
            'status' => 'required|in:available,unavailable,maintenance',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        // Verificar que el número de asiento no exista ya en el mismo auditorio
        $existingSeat = Seat::where('auditorium_id', $request->auditorium_id)
            ->where('number', $request->number)
            ->first();
            
        if ($existingSeat) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Ya existe un asiento con ese número en este auditorio'
                ], 422);
            } else {
                return redirect()->back()
                    ->with('error', 'Ya existe un asiento con ese número en este auditorio')
                    ->withInput();
            }
        }

        $seat = Seat::create($request->all());
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Asiento creado correctamente',
                'seat' => $seat
            ], 201);
        } else {
            return redirect()->route('seats.index')
                ->with('success', 'Asiento creado correctamente');
        }
    }

    /**
     * Muestra un asiento específico.
     */
    public function show(Request $request, $id)
    {
        $seat = Seat::with('auditorium', 'bookings.screening.movie', 'bookings.user')->findOrFail($id);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($seat);
        }
        
        return view('seats.show', compact('seat'));
    }

    /**
     * Muestra el formulario para editar un asiento.
     */
    public function edit($id)
    {
        $seat = Seat::findOrFail($id);
        $auditoriums = Auditorium::all();
        return view('seats.edit', compact('seat', 'auditoriums'));
    }

    /**
     * Actualiza un asiento existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'auditorium_id' => 'exists:auditoriums,id',
            'number' => 'string|max:10',
            'status' => 'in:available,unavailable,maintenance',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $seat = Seat::findOrFail($id);
        
        // Verificar que el número de asiento no exista ya en el mismo auditorio
        if ($request->has('number') && $request->has('auditorium_id') && 
            ($request->number != $seat->number || $request->auditorium_id != $seat->auditorium_id)) {
            
            $existingSeat = Seat::where('auditorium_id', $request->auditorium_id)
                ->where('number', $request->number)
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingSeat) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Ya existe un asiento con ese número en este auditorio'
                    ], 422);
                } else {
                    return redirect()->back()
                        ->with('error', 'Ya existe un asiento con ese número en este auditorio')
                        ->withInput();
                }
            }
        }

        $seat->update($request->all());
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Asiento actualizado correctamente',
                'seat' => $seat
            ]);
        } else {
            return redirect()->route('seats.index')
                ->with('success', 'Asiento actualizado correctamente');
        }
    }

    /**
     * Elimina un asiento.
     */
    public function destroy(Request $request, $id)
    {
        $seat = Seat::findOrFail($id);
        
        // Verificar si el asiento tiene reservas
        if ($seat->bookings()->count() > 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No se puede eliminar el asiento porque tiene reservas asociadas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el asiento porque tiene reservas asociadas');
            }
        }
        
        $seat->delete();
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Asiento eliminado correctamente'
            ]);
        } else {
            return redirect()->route('seats.index')
                ->with('success', 'Asiento eliminado correctamente');
        }
    }

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
