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
            return response()->json($auditoriums);
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
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'auditorium' => $auditorium,
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
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|array',
            'rows.*' => 'required|string|size:1',
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
            // Crear el auditorio
            $auditorium = Auditorium::create([
                'name' => $request->name,
                'capacity' => $request->capacity
            ]);
            
            // Crear los asientos
            $seats = [];
            foreach ($request->rows as $row) {
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
                    'message' => 'Auditorio creado correctamente',
                    'auditorium' => $auditorium
                ], 201);
            } else {
                return redirect()->route('auditoriums.index')
                    ->with('success', 'Auditorio creado correctamente');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al crear el auditorio',
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
                'message' => 'Auditorio actualizado correctamente',
                'auditorium' => $auditorium
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
                    'message' => 'Auditorio eliminado correctamente'
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