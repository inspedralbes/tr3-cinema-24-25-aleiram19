<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\Auditorium;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ScreeningController extends Controller
{
    /**
     * Obtiene todas las sesiones disponibles
     */
    public function index(Request $request)
    {
        $screenings = Screening::with(['movie', 'auditorium'])
            ->where('date_time', '>', Carbon::now())
            ->orderBy('date_time')
            ->get();
            
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($screenings);
        }
        
        // Si es una solicitud web, devolver vista
        return view('screenings.index', compact('screenings'));
    }
    
    /**
     * Muestra el formulario para crear una nueva sesión
     */
    public function create()
    {
        $movies = Movie::all();
        $auditoriums = Auditorium::all();
        return view('screenings.create', compact('movies', 'auditoriums'));
    }

    /**
     * Obtiene los detalles de una sesión
     */
    public function show(Request $request, $id)
    {
        $screening = Screening::with(['movie', 'auditorium'])
            ->findOrFail($id);
            
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            // Asegurarse de que los datos de la película están cargados completamente
            if ($screening->movie) {
                $screening->movie->load('movieGenre');
            }
            
            return response()->json($screening);
        }
        
        // Si es una solicitud web, devolver vista
        return view('screenings.show', compact('screening'));
    }
    
    /**
     * Obtiene los asientos disponibles para una sesión
     */
    public function getAvailableSeats(Request $request, $screeningId)
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
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'screening' => $screening,
                'auditorium' => $auditorium,
                'seats_by_row' => $seatsByRow,
                'price_info' => $screening->getPriceMatrix()
            ]);
        }
        
        // Si es una solicitud web, devolver vista
        return view('screenings.seats', compact('screening', 'auditorium', 'seatsByRow'));
    }
    
    /**
     * Crea una nueva sesión
     * Este método solo estaría disponible para administradores
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'auditorium_id' => 'required|exists:auditoriums,id',
            'date' => 'required|date|after:today',
            'time' => 'required|in:16:00,18:00,20:00', // Solo horarios permitidos
            'is_special' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        // Construir fecha y hora completa
        $dateTime = Carbon::parse($request->date . ' ' . $request->time);
        
        // Verificar que no haya más de 2 sesiones a la misma hora
        $sessionsCount = Screening::where('date_time', $dateTime)->count();
        if ($sessionsCount >= 2) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Ya existen 2 sesiones programadas para esta fecha y hora'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'Ya existen 2 sesiones programadas para esta fecha y hora');
            }
        }
        
        // Verificar que la película no esté ya programada a esta hora
        $existingMovieScreening = Screening::where('date_time', $dateTime)
            ->where('movie_id', $request->movie_id)
            ->exists();
        if ($existingMovieScreening) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Esta película ya está programada para esta fecha y hora'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'Esta película ya está programada para esta fecha y hora');
            }
        }
        
        // Crear la sesión
        $screening = Screening::create([
            'movie_id' => $request->movie_id,
            'auditorium_id' => $request->auditorium_id,
            'date_time' => $dateTime,
            'price' => $request->is_special ? 4 : 6, // Precio base (normal)
            'is_special' => $request->is_special ?? false
        ]);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Sesión creada correctamente',
                'screening' => $screening
            ], 201);
        } else {
            return redirect()->route('screenings.index')
                ->with('success', 'Sesión creada correctamente');
        }
    }
    
    /**
     * Muestra el formulario para editar una sesión existente
     */
    public function edit($id)
    {
        $screening = Screening::findOrFail($id);
        $movies = Movie::all();
        $auditoriums = Auditorium::all();
        return view('screenings.edit', compact('screening', 'movies', 'auditoriums'));
    }

    /**
     * Actualiza una sesión existente
     * Este método solo estaría disponible para administradores
     */
    public function update(Request $request, $id)
    {
        $screening = Screening::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'movie_id' => 'exists:movies,id',
            'auditorium_id' => 'exists:auditoriums,id',
            'date' => 'date|after:today',
            'time' => 'in:16:00,18:00,20:00', // Solo horarios permitidos
            'is_special' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        // Si se cambia la fecha/hora, verificar que no haya conflictos
        if (($request->date || $request->time) && $screening->hasTickets()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede cambiar la fecha/hora de una sesión que ya tiene entradas vendidas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede cambiar la fecha/hora de una sesión que ya tiene entradas vendidas');
            }
        }
        
        if ($request->date && $request->time) {
            // Construir fecha y hora completa
            $dateTime = Carbon::parse($request->date . ' ' . $request->time);
            
            // Verificar que no haya más de 2 sesiones a la misma hora (excluyendo la actual)
            $sessionsCount = Screening::where('date_time', $dateTime)
                ->where('id', '!=', $id)
                ->count();
                
            if ($sessionsCount >= 2) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Ya existen 2 sesiones programadas para esta fecha y hora'
                    ], 400);
                } else {
                    return redirect()->back()
                        ->with('error', 'Ya existen 2 sesiones programadas para esta fecha y hora');
                }
            }
            
            // Verificar que la película no esté ya programada a esta hora (excluyendo la actual)
            if ($request->has('movie_id')) {
                $existingMovieScreening = Screening::where('date_time', $dateTime)
                    ->where('id', '!=', $id)
                    ->where('movie_id', $request->movie_id)
                    ->exists();
                    
                if ($existingMovieScreening) {
                    if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                        return response()->json([
                            'message' => 'Esta película ya está programada para esta fecha y hora'
                        ], 400);
                    } else {
                        return redirect()->back()
                            ->with('error', 'Esta película ya está programada para esta fecha y hora');
                    }
                }
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
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Sesión actualizada correctamente',
                'screening' => $screening
            ]);
        } else {
            return redirect()->route('screenings.index')
                ->with('success', 'Sesión actualizada correctamente');
        }
    }
    
    /**
     * Elimina una sesión
     * Este método solo estaría disponible para administradores
     */
    public function destroy(Request $request, $id)
    {
        $screening = Screening::findOrFail($id);
        
        // Verificar que no haya entradas para esta sesión
        if ($screening->hasTickets()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede eliminar una sesión que ya tiene entradas vendidas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar una sesión que ya tiene entradas vendidas');
            }
        }
        
        $screening->delete();
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Sesión eliminada correctamente'
            ]);
        } else {
            return redirect()->route('screenings.index')
                ->with('success', 'Sesión eliminada correctamente');
        }
    }
    
    /**
     * Obtiene los screenings disponibles para una fecha específica
     */
    public function getScreeningsByDate(Request $request)
    {
        try {
            \Log::info('Iniciando getScreeningsByDate');
            
            $validator = Validator::make($request->all(), [
                'date' => 'required|date'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validación fallida', ['errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            // Obtener la fecha del request
            $date = $request->date;
            
            // Construir el rango de fechas para buscar screenings (todo el día)
            $startDate = Carbon::parse($date)->startOfDay();
            $endDate = Carbon::parse($date)->endOfDay();
            
            // Para depuración - imprimir los parámetros
            \Log::debug('Buscando screenings para fecha:', [
                'fecha_solicitada' => $date,
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString(),
                'ahora' => Carbon::now()->toDateTimeString()
            ]);
            
            try {
                // Obtener screenings para la fecha especificada sin filtro de fecha futura
                // para propósitos de depuración
                $screenings = Screening::with(['movie', 'auditorium'])
                    ->whereBetween('date_time', [$startDate, $endDate])
                    ->orderBy('date_time')
                    ->get();
                    
                // Registrar los resultados
                \Log::debug('Screenings encontrados:', [
                    'total' => $screenings->count(),
                    'detalles' => $screenings->map(function($s) {
                        return [
                            'id' => $s->id,
                            'movie_id' => $s->movie_id,
                            'movie_title' => $s->movie ? $s->movie->title : 'Sin película',
                            'date_time' => $s->date_time,
                            'is_future' => Carbon::parse($s->date_time)->gt(Carbon::now())
                        ];
                    })
                ]);
            } catch (\Exception $e) {
                \Log::error('Error al obtener screenings existentes', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString() 
                ]);
                $screenings = collect([]);
            }
            
            // Si no hay screenings, intentemos crear algunos para propósitos de prueba
            if ($screenings->count() === 0) {
                \Log::debug('No se encontraron screenings para la fecha. Revisando si hay películas disponibles para crear screenings de prueba.');
                
                try {
                    $movies = Movie::take(2)->get();
                    \Log::debug('Películas disponibles para screenings:', ['count' => $movies->count()]);
                    
                    $auditorium = Auditorium::first();
                    \Log::debug('Auditorio disponible:', ['id' => $auditorium ? $auditorium->id : 'ninguno']);
                    
                    if ($movies->count() > 0 && $auditorium) {
                        \Log::debug('Creando screenings de prueba', [
                            'películas' => $movies->pluck('title'),
                            'auditorio' => $auditorium->name
                        ]);
                        
                        // Crear algunos screenings de prueba para hoy
                        $testHours = ['16:00', '18:00', '20:00'];
                        
                        foreach ($movies as $index => $movie) {
                            foreach ($testHours as $hour) {
                                $testDateTime = Carbon::parse($date . ' ' . $hour);
                                
                                // Solo crear si el horario es futuro o al menos hoy
                                if ($testDateTime->startOfDay()->gte(Carbon::now()->startOfDay())) {
                                    try {
                                        $screening = Screening::create([
                                            'movie_id' => $movie->id,
                                            'auditorium_id' => $auditorium->id,
                                            'date_time' => $testDateTime,
                                            'price' => 6, // Precio normal
                                            'is_special' => false
                                        ]);
                                        
                                        \Log::debug('Screening de prueba creado con éxito', [
                                            'id' => $screening->id,
                                            'película' => $movie->title,
                                            'horario' => $testDateTime->toDateTimeString()
                                        ]);
                                    } catch (\Exception $e) {
                                        \Log::error('Error al crear screening de prueba', [
                                            'error' => $e->getMessage(),
                                            'película' => $movie->title,
                                            'horario' => $testDateTime->toDateTimeString(),
                                            'trace' => $e->getTraceAsString()
                                        ]);
                                    }
                                } else {
                                    \Log::debug('No se creó screening porque ya pasó la hora', [
                                        'fecha_prueba' => $testDateTime->toDateTimeString(),
                                        'ahora' => Carbon::now()->toDateTimeString()
                                    ]);
                                }
                            }
                        }
                        
                        try {
                            // Volver a obtener los screenings ahora que hemos creado algunos
                            $screenings = Screening::with(['movie', 'auditorium'])
                                ->whereBetween('date_time', [$startDate, $endDate])
                                ->orderBy('date_time')
                                ->get();
                                
                            \Log::debug('Screenings después de crear de prueba:', [
                                'total' => $screenings->count()
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error al obtener screenings después de crear', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    } else {
                        \Log::warning('No hay películas o auditorios disponibles para crear screenings de prueba');
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al crear screenings de prueba', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            // Filtrar solo los screenings futuros
            $futureScreenings = $screenings->filter(function($screening) {
                return Carbon::parse($screening->date_time)->gte(Carbon::now()->startOfDay());
            })->values();
            
            // Agrupar screenings por película para asegurar solo 2 películas por día
            // Pero asegurarse de que haya un balance entre las horas
            // Primero obtener todos los movie_ids únicos
            $uniqueMovieIds = $futureScreenings->pluck('movie_id')->unique()->values()->toArray();
            
            // Ordenar screenings por hora para encontrar las películas que tienen horarios de 18:00 y 20:00
            $screeningsByTime = [];
            foreach ($futureScreenings as $screening) {
                $hour = Carbon::parse($screening->date_time)->format('H');
                $screeningsByTime[$hour][] = $screening->movie_id;
            }
            
            // Movies con horario de 18:00
            $moviesAt18 = $screeningsByTime['18'] ?? [];
            // Movies con horario de 20:00
            $moviesAt20 = $screeningsByTime['20'] ?? [];
            
            // Intentar encontrar 2 películas que tengan horarios a las 18:00 y 20:00
            $selectedMovieIds = [];
            
            // Primero, buscar películas que tengan ambos horarios
            foreach ($uniqueMovieIds as $movieId) {
                if (in_array($movieId, $moviesAt18) && in_array($movieId, $moviesAt20)) {
                    $selectedMovieIds[] = $movieId;
                    if (count($selectedMovieIds) >= 2) break;
                }
            }
            
            // Si no tenemos suficientes, agregar películas que tengan al menos un horario
            if (count($selectedMovieIds) < 2) {
                foreach ($uniqueMovieIds as $movieId) {
                    if (!in_array($movieId, $selectedMovieIds) && 
                        (in_array($movieId, $moviesAt18) || in_array($movieId, $moviesAt20))) {
                        $selectedMovieIds[] = $movieId;
                        if (count($selectedMovieIds) >= 2) break;
                    }
                }
            }
            
            // Si aún no tenemos 2, simplemente tomar las primeras dos películas disponibles
            if (count($selectedMovieIds) < 2 && count($uniqueMovieIds) > 0) {
                $remainingMovies = array_diff($uniqueMovieIds, $selectedMovieIds);
                while (count($selectedMovieIds) < 2 && count($remainingMovies) > 0) {
                    $selectedMovieIds[] = array_shift($remainingMovies);
                }
            }
            
            // Filtrar screenings para incluir solo las películas seleccionadas
            $limitedScreenings = $futureScreenings->filter(function($screening) use ($selectedMovieIds) {
                return in_array($screening->movie_id, $selectedMovieIds);
            })->values();
            
            \Log::info('Finalizando getScreeningsByDate con éxito', [
                'screenings_totales' => $futureScreenings->count(),
                'screenings_limitados' => $limitedScreenings->count(),
                'películas_seleccionadas' => $selectedMovieIds
            ]);
            
            return response()->json($limitedScreenings);
        } catch (\Exception $e) {
            \Log::error('Error general en getScreeningsByDate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}
