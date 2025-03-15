<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    /**
     * Procesa la compra de tickets como invitado
     */
    public function processGuestPurchase(Request $request)
    {
        try {
            // Validación de datos de entrada
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'ticket_ids' => 'required|array',
                'ticket_ids.*' => 'exists:tickets,id',
                'snacks' => 'array',
                'snacks.*.id' => 'exists:snacks,id',
                'snacks.*.quantity' => 'integer|min:1'
            ]);

            $ticketIds = $request->ticket_ids;
            $snacksData = $request->snacks ?? [];

            // Verificar que los tickets existen y están disponibles
            $tickets = Ticket::whereIn('id', $ticketIds)
                ->where('status', 'reserved')
                ->get();

            if ($tickets->count() != count($ticketIds)) {
                return response()->json([
                    'message' => 'Algunos tickets no existen o ya han sido confirmados'
                ], 400);
            }

            DB::beginTransaction();

            // Obtener el rol de invitado
            $guestRole = \App\Models\Role::where('name', 'guest')->first();
            
            if (!$guestRole) {
                return response()->json([
                    'message' => 'Error: Rol de invitado no encontrado en el sistema'
                ], 500);
            }
            
            // Crear un usuario invitado temporal
            $temporaryPassword = Str::random(12);
            $guestUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($temporaryPassword),
                'role_id' => $guestRole->id
            ]);

            // Asignar los tickets al usuario invitado
            foreach ($tickets as $ticket) {
                $ticket->update([
                    'user_id' => $guestUser->id,
                    'status' => 'purchased'
                ]);
            }

            // Añadir snack si se ha seleccionado
            if (!empty($snacksData) && count($snacksData) > 0) {
                // Tomamos el primer snack para simplificar (ya que ahora solo se permite uno por ticket)
                $snackData = $snacksData[0];
                $snack = \App\Models\Snack::findOrFail($snackData['id']);
                $quantity = $snackData['quantity'];
                $snackPrice = $snack->price * $quantity;
                
                // Si hay más de un ticket, asignamos el snack solo al primer ticket
                if (count($tickets) > 0) {
                    $ticket = $tickets[0];
                    $ticket->update([
                        'snack_id' => $snack->id,
                        'snack_quantity' => $quantity,
                        'total_pay' => $ticket->total_pay + $snackPrice
                    ]);
                }
            }

            DB::commit();

            // Obtener tickets actualizados para el resumen de la compra
            $purchasedTickets = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'snacks'])
                ->whereIn('id', $ticketIds)
                ->get();

            // Generar token de acceso para cada ticket
            $ticketsWithToken = [];
            foreach ($purchasedTickets as $ticket) {
                // Generar un token simple basado en el email y ID del ticket
                // En una implementación real, debería ser más seguro
                $token = md5($guestUser->email . $ticket->id . config('app.key'));
                
                $ticketsWithToken[] = [
                    'ticket' => $ticket,
                    'access_token' => $token,
                    'access_url' => url("/api/guest/ticket/{$ticket->id}/{$token}")
                ];
            }
            
            return response()->json([
                'message' => 'Compra completada con éxito',
                'user' => [
                    'name' => $guestUser->name,
                    'email' => $guestUser->email
                ],
                'tickets' => $ticketsWithToken
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
