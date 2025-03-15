<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\Auditorium;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    /**
     * Obtiene todas las sesiones disponibles
     */
    public function index()
    {
        $screenings = Screening::with(['movie', 'auditorium'])
            ->where('date_time', '>', Carbon::now())
            ->orderBy('date_time')
            ->get();
            
        return response()->json($screenings);
    }
    
    /**
     * Obtiene los detalles de una sesión
     */
    public function show($id)
    {
        $screening = Screening::with(['movie', 'auditorium'])
            ->findOrFail($id);
            
        return response()->json($screening);
    }
    
    /**
     * Obtiene los asientos disponibles para una sesión
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
            // Determinar si es asiento VIP (fila F)
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
     * Crea una nueva sesión
     * Este método solo estaría disponible para administradores
     */
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'auditorium_id' => 'required|exists:auditoriums,id',
            'date' => 'required|date|after:today',
            'time' => 'required|in:16:00,18:00,20:00', // Solo horarios permitidos
            'is_special' => 'boolean'
        ]);
        
        // Construir fecha y hora completa
        $dateTime = Carbon::parse($request->date . ' ' . $request->time);
        
        // Verificar que no haya otra sesión a la misma hora
        $existingScreening = Screening::where('date_time', $dateTime)->exists();
        if ($existingScreening) {
            return response()->json([
                'message' => 'Ya existe una sesión programada para esta fecha y hora'
            ], 400);
        }
        
        // Crear la sesión
        $screening = Screening::create([
            'movie_id' => $request->movie_id,
            'auditorium_id' => $request->auditorium_id,
            'date_time' => $dateTime,
            'price' => $request->is_special ? 4 : 6, // Precio base (normal)
            'is_special' => $request->is_special ?? false
        ]);
        
        return response()->json([
            'message' => 'Sesión creada correctamente',
            'screening' => $screening
        ], 201);
    }
    
    /**
     * Actualiza una sesión existente
     * Este método solo estaría disponible para administradores
     */
    public function update(Request $request, $id)
    {
        $screening = Screening::findOrFail($id);
        
        $request->validate([
            'movie_id' => 'exists:movies,id',
            'auditorium_id' => 'exists:auditoriums,id',
            'date' => 'date|after:today',
            'time' => 'in:16:00,18:00,20:00', // Solo horarios permitidos
            'is_special' => 'boolean'
        ]);
        
        // Si se cambia la fecha/hora, verificar que no haya conflictos
        if (($request->date || $request->time) && $screening->hasTickets()) {
            return response()->json([
                'message' => 'No se puede cambiar la fecha/hora de una sesión que ya tiene entradas vendidas'
            ], 400);
        }
        
        if ($request->date && $request->time) {
            // Construir fecha y hora completa
            $dateTime = Carbon::parse($request->date . ' ' . $request->time);
            
            // Verificar que no haya otra sesión a la misma hora
            $existingScreening = Screening::where('date_time', $dateTime)
                ->where('id', '!=', $id)
                ->exists();
                
            if ($existingScreening) {
                return response()->json([
                    'message' => 'Ya existe una sesión programada para esta fecha y hora'
                ], 400);
            }
            
            $screening->date_time = $dateTime;
        } else if ($request->date) {
            // Mantener la hora actual pero cambiar la fecha
            $currentTime = Carbon::parse($screening->date_time)->format('H:i');
            $dateTime = Carbon::parse($request->date . ' ' . $currentTime);
            $screening->date_time = $dateTime;
        } else if ($request->time) {
            // Mantener la fecha actual pero cambiar la hora
            $currentDate = Carbon::parse($screening->date_time)->format('Y-m-d');
            $dateTime = Carbon::parse($currentDate . ' ' . $request->time);
            $screening->date_time = $dateTime;
        }
        
        // Actualizar otros campos
        if ($request->has('movie_id')) {
            $screening->movie_id = $request->movie_id;
        }
        
        if ($request->has('auditorium_id')) {
            $screening->auditorium_id = $request->auditorium_id;
        }
        
        if ($request->has('is_special')) {
            $screening->is_special = $request->is_special;
            // Actualizar precio base según si es día especial
            $screening->price = $request->is_special ? 4 : 6;
        }
        
        $screening->save();
        
        return response()->json([
            'message' => 'Sesión actualizada correctamente',
            'screening' => $screening
        ]);
    }
    
    /**
     * Elimina una sesión
     * Este método solo estaría disponible para administradores
     */
    public function destroy($id)
    {
        $screening = Screening::findOrFail($id);
        
        // Verificar que no haya entradas para esta sesión
        if ($screening->hasTickets()) {
            return response()->json([
                'message' => 'No se puede eliminar una sesión que ya tiene entradas vendidas'
            ], 400);
        }
        
        $screening->delete();
        
        return response()->json([
            'message' => 'Sesión eliminada correctamente'
        ]);
    }
}