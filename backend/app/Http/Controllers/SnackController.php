<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SnackController extends Controller
{
    /**
     * Obtiene todos los snacks disponibles
     */
    public function index()
    {
        $snacks = Snack::all();
        return response()->json($snacks);
    }

    /**
     * Obtiene detalles de un snack específico
     */
    public function show($id)
    {
        $snack = Snack::findOrFail($id);
        return response()->json($snack);
    }

    /**
     * Añade snack a ticket reservado
     */
    public function addSnackToTicket(Request $request)
    {
        try {
            // Validación de datos de entrada
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'snack_id' => 'required|exists:snacks,id',
                'quantity' => 'required|integer|min:1'
            ]);

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

            return response()->json([
                'message' => 'Snack añadido correctamente',
                'ticket' => $updatedTicket,
                'snack_price' => $snackPrice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al añadir snack',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina snack de un ticket reservado
     */
    public function removeSnackFromTicket(Request $request)
    {
        try {
            // Validación de datos de entrada
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id'
            ]);

            $ticketId = $request->ticket_id;

            // Verificar que el ticket existe y está en estado reservado
            $ticket = Ticket::where('id', $ticketId)
                ->where('status', 'reserved')
                ->firstOrFail();

            // Verificar que el ticket tiene un snack asignado
            if (!$ticket->snack_id) {
                return response()->json([
                    'message' => 'Este ticket no tiene snack asignado'
                ], 400);
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

            return response()->json([
                'message' => 'Snack eliminado correctamente',
                'ticket' => $updatedTicket
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar snack',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}