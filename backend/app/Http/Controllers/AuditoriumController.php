<?php

namespace App\Http\Controllers;



use App\Models\Auditorium;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuditoriumController extends Controller
{
    /**
     * Muestra un listado de todos los auditorios.
     */
    public function index(Request $request)
    {
        $auditoriums = Auditorium::withCount('seats')->get();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'data' => $auditoriums
            ]);
        }
        
        // Si es una solicitud web, devolver vista
        return view('auditoriums.index', compact('auditoriums'));
    }

    /**
     * Muestra un auditorio específico con sus asientos.
     */
    public function show(Request $request, $id)
    {
        $auditorium = Auditorium::with('seats')->findOrFail($id);
        
        // Organizar los asientos por filas para facilitar el renderizado en frontend
        $seatsByRow = $auditorium->seats->groupBy(function($seat) {
            return substr($seat->number, 0, 1); // Primer carácter del número de asiento (la fila)
        });
        
        // Preparar los asientos con información de fila para los tests
        $seatsWithRowInfo = $auditorium->seats->map(function($seat) {
            $seatData = $seat->toArray();
            $seatData['row'] = substr($seat->number, 0, 1); // Extraer la fila del número de asiento
            $seatData['seat_number'] = substr($seat->number, 1); // Extraer el número del asiento
            return $seatData;
        });
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'data' => [
                    'id' => $auditorium->id,
                    'name' => $auditorium->name,
                    'capacity' => $auditorium->capacity,
                    'seats' => $seatsWithRowInfo
                ],
                'seats_by_row' => $seatsByRow
            ]);
        }
        
        // Si es una solicitud web, devolver vista
        return view('auditoriums.show', compact('auditorium', 'seatsByRow'));
    }

    /**
     * Almacena un nuevo auditorio.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'rows' => 'required|integer|min:1',
            'seats_per_row' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        DB::beginTransaction();
        
        try {
            // Calcular la capacidad
            $capacity = $request->rows * $request->seats_per_row;
            
            // Crear el auditorio
            $auditorium = Auditorium::create([
                'name' => $request->name,
                'capacity' => $capacity
            ]);
            
            // Crear los asientos
            $seats = [];
            $rowLetters = range('A', chr(ord('A') + $request->rows - 1));
            
            foreach ($rowLetters as $row) {
                for ($i = 1; $i <= $request->seats_per_row; $i++) {
                    $seatNumber = $row . $i;
                    $seats[] = [
                        'auditorium_id' => $auditorium->id,
                        'number' => $seatNumber,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            
            Seat::insert($seats);
            
            DB::commit();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Auditorium created successfully',
                    'data' => $auditorium
                ], 201);
            } else {
                return redirect()->route('auditoriums.index')
                    ->with('success', 'Auditorio creado correctamente');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error creating auditorium',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Error al crear el auditorio: ' . $e->getMessage());
            }
        }
    }

    /**
     * Actualiza un auditorio existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $auditorium = Auditorium::findOrFail($id);
        
        // Verificar si tiene sesiones programadas
        if ($auditorium->screenings()->count() > 0) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede modificar el auditorio porque tiene sesiones programadas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede modificar el auditorio porque tiene sesiones programadas');
            }
        }
        
        $auditorium->update($request->only('name'));
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Auditorium updated successfully',
                'data' => $auditorium
            ]);
        } else {
            return redirect()->route('auditoriums.index')
                ->with('success', 'Auditorio actualizado correctamente');
        }
    }

    /**
     * Elimina un auditorio.
     */
    public function destroy(Request $request, $id)
    {
        $auditorium = Auditorium::findOrFail($id);
        
        // Verificar si tiene sesiones programadas
        if ($auditorium->screenings()->count() > 0) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el auditorio porque tiene sesiones programadas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el auditorio porque tiene sesiones programadas');
            }
        }
        
        DB::beginTransaction();
        
        try {
            // Eliminar primero los asientos asociados
            Seat::where('auditorium_id', $id)->delete();
            
            // Eliminar el auditorio
            $auditorium->delete();
            
            DB::commit();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Auditorium deleted successfully'
                ]);
            } else {
                return redirect()->route('auditoriums.index')
                    ->with('success', 'Auditorio eliminado correctamente');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al eliminar el auditorio',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Error al eliminar el auditorio: ' . $e->getMessage());
            }
        }
    }
}