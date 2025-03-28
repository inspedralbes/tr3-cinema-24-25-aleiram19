<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SnackController extends Controller
{
    /**
     * Obtiene todos los snacks disponibles
     */
    public function index(Request $request)
    {
        $snacks = Snack::all();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($snacks);
        }
        
        // Si es una solicitud web, devolver vista
        return view('snacks.index', compact('snacks'));
    }

    /**
     * Obtiene detalles de un snack específico
     */
    public function show(Request $request, $id)
    {
        $snack = Snack::findOrFail($id);
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($snack);
        }
        
        // Si es una solicitud web, devolver vista
        return view('snacks.show', compact('snack'));
    }

    /**
     * Añade snack a ticket reservado
     */
    public function addSnackToTicket(Request $request)
    {
        try {
            // Validación de datos de entrada
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|exists:tickets,id',
                'snack_id' => 'required|exists:snacks,id',
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            $ticketId = $request->ticket_id;
            $snackId = $request->snack_id;
            $quantity = $request->quantity;

            // Verificar que el ticket existe y está en estado reservado
            $ticket = Ticket::where('id', $ticketId)
                ->where('status', 'reserved')
                ->firstOrFail();

            // Obtener el snack y calcular precio adicional
            $snack = Snack::findOrFail($snackId);
            $snackPrice = $snack->price * $quantity;

            DB::beginTransaction();

            // Actualizar el ticket con el snack y su cantidad
            $ticket->update([
                'snack_id' => $snackId,
                'snack_quantity' => $quantity,
                'total_pay' => $ticket->total_pay + $snackPrice
            ]);

            DB::commit();

            // Obtener ticket actualizado
            $updatedTicket = Ticket::with('snack')
                ->where('id', $ticketId)
                ->first();

            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Snack añadido correctamente',
                    'ticket' => $updatedTicket,
                    'snack_price' => $snackPrice
                ]);
            } else {
                return redirect()->route('tickets.show', $ticketId)
                    ->with('success', 'Snack añadido correctamente');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al añadir snack',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Error al añadir snack: ' . $e->getMessage());
            }
        }
    }

    /**
     * Elimina snack de un ticket reservado
     */
    public function removeSnackFromTicket(Request $request)
    {
        try {
            // Validación de datos de entrada
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|exists:tickets,id'
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            $ticketId = $request->ticket_id;

            // Verificar que el ticket existe y está en estado reservado
            $ticket = Ticket::where('id', $ticketId)
                ->where('status', 'reserved')
                ->firstOrFail();

            // Verificar que el ticket tiene un snack asignado
            if (!$ticket->snack_id) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Este ticket no tiene snack asignado'
                    ], 400);
                } else {
                    return redirect()->back()
                        ->with('error', 'Este ticket no tiene snack asignado');
                }
            }

            DB::beginTransaction();

            // Calcular el precio a restar
            $snack = Snack::find($ticket->snack_id);
            $snackPrice = $snack ? $snack->price * $ticket->snack_quantity : 0;

            // Eliminar el snack del ticket
            $ticket->update([
                'snack_id' => null,
                'snack_quantity' => 0,
                'total_pay' => $ticket->total_pay - $snackPrice
            ]);

            DB::commit();

            // Obtener ticket actualizado
            $updatedTicket = Ticket::find($ticketId);

            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Snack eliminado correctamente',
                    'ticket' => $updatedTicket
                ]);
            } else {
                return redirect()->route('tickets.show', $ticketId)
                    ->with('success', 'Snack eliminado correctamente');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al eliminar snack',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Error al eliminar snack: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Muestra el formulario para crear un nuevo snack
     */
    public function create()
    {
        return view('snacks.create');
    }
    
    /**
     * Almacena un nuevo snack
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        $data = $request->all();
        
        // Manejar la subida de la imagen si existe
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('snacks', 'public');
            $data['image'] = $imagePath;
        }
        
        $snack = Snack::create($data);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Snack creado correctamente',
                'snack' => $snack
            ], 201);
        } else {
            return redirect()->route('snacks.index')
                ->with('success', 'Snack creado correctamente');
        }
    }
    
    /**
     * Muestra el formulario para editar un snack
     */
    public function edit($id)
    {
        $snack = Snack::findOrFail($id);
        return view('snacks.edit', compact('snack'));
    }
    
    /**
     * Actualiza un snack existente
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        
        $snack = Snack::findOrFail($id);
        $data = $request->except('image');
        
        // Manejar la subida de la imagen si existe
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($snack->image && \Storage::disk('public')->exists($snack->image)) {
                \Storage::disk('public')->delete($snack->image);
            }
            
            $imagePath = $request->file('image')->store('snacks', 'public');
            $data['image'] = $imagePath;
        }
        
        $snack->update($data);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Snack actualizado correctamente',
                'snack' => $snack
            ]);
        } else {
            return redirect()->route('snacks.index')
                ->with('success', 'Snack actualizado correctamente');
        }
    }
    
    /**
     * Elimina un snack
     */
    public function destroy(Request $request, $id)
    {
        $snack = Snack::findOrFail($id);
        
        // Verificar si hay tickets con este snack
        $ticketsWithSnack = Ticket::where('snack_id', $id)->count();
        
        if ($ticketsWithSnack > 0) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el snack porque está asociado a tickets existentes'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el snack porque está asociado a tickets existentes');
            }
        }
        
        // Eliminar la imagen si existe
        if ($snack->image && \Storage::disk('public')->exists($snack->image)) {
            \Storage::disk('public')->delete($snack->image);
        }
        
        $snack->delete();
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Snack eliminado correctamente'
            ]);
        } else {
            return redirect()->route('snacks.index')
                ->with('success', 'Snack eliminado correctamente');
        }
    }
}
