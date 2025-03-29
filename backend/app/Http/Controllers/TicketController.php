<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\PdfService;
use App\Services\MailService;

class TicketController extends Controller
{
    /**
     * @var PdfService
     */
    protected $pdfService;
    
    /**
     * @var MailService
     */
    protected $mailService;
    
    /**
     * Constructor
     */
    public function __construct(PdfService $pdfService, MailService $mailService)
    {
        $this->pdfService = $pdfService;
        $this->mailService = $mailService;
    }
    
    /**
     * Reserva asientos para una sesión
     */
    public function reserveSeats(Request $request)
    {
        try {
            // Validación de datos de entrada
            $validator = Validator::make($request->all(), [
                'screening_id' => 'required|exists:screenings,id',
                'seat_ids' => 'required|array',
                'seat_ids.*' => 'exists:seats,id'
            ]);
            
            if ($validator->fails()) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            
            $userId = Auth::id();
            $screeningId = $request->screening_id;
            $seatIds = $request->seat_ids;
            
            // Obtener la sesión
            $screening = Screening::findOrFail($screeningId);
            
            // VALIDACIÓN 1: Verificar que el usuario no tenga más de 10 entradas para esta sesión
            if (count($seatIds) > 10) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'No se pueden reservar más de 10 asientos por sesión'
                    ], 400);
                } else {
                    return redirect()->back()->with('error', 'No se pueden reservar más de 10 asientos por sesión');
                }
            }
            
            // VALIDACIÓN 2: Verificar que el usuario no tenga entradas para otra sesión futura
            $hasFutureTickets = $this->userHasFutureTickets($userId, $screeningId);
            if ($hasFutureTickets) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Ya tienes entradas para otra sesión futura. No puedes reservar entradas para múltiples sesiones futuras a la vez.'
                    ], 400);
                } else {
                    return redirect()->back()->with('error', 'Ya tienes entradas para otra sesión futura. No puedes reservar entradas para múltiples sesiones futuras a la vez.');
                }
            }
            
            // Verificar disponibilidad de asientos
            $unavailableSeats = Seat::whereIn('id', $seatIds)
                ->where('status', 'busy')
                ->get();
                
            if ($unavailableSeats->count() > 0) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Algunos asientos seleccionados no están disponibles',
                        'unavailable_seats' => $unavailableSeats->pluck('number')
                    ], 400);
                } else {
                    return redirect()->back()->with('error', 'Algunos asientos seleccionados no están disponibles');
                }
            }
            
            DB::beginTransaction();
            
            $totalPrice = 0;
            $tickets = [];
            
            // Crear tickets para cada asiento y calcular el precio total
            foreach ($seatIds as $seatId) {
                $seat = Seat::findOrFail($seatId);
                
                // Calcular precio según si el asiento es VIP y si es día del espectador
                $price = $screening->calculatePrice($seat->number);
                $totalPrice += $price;
                
                // Crear ticket individual para este asiento
                $ticket = Ticket::create([
                    'user_id' => $userId,
                    'screening_id' => $screeningId,
                    'seat_id' => $seatId,
                    'quantity' => 1,
                    'total_pay' => $price,
                    'purchase_date' => now()
                ]);
                
                // Actualizar estado del asiento
                $seat->update(['status' => 'busy']);
                
                $tickets[] = $ticket;
            }
            
            DB::commit();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Reserva realizada con éxito',
                    'tickets' => $tickets,
                    'total_price' => $totalPrice
                ], 201);
            } else {
                return redirect()->route('tickets.index')
                    ->with('success', 'Reserva realizada con éxito');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al realizar la reserva',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Error al realizar la reserva: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Verifica si el usuario tiene tickets futuros para otras sesiones
     */
    private function userHasFutureTickets($userId, $currentScreeningId)
    {
        $now = Carbon::now();
        
        // Obtener los tickets futuros del usuario que no sean para la sesión actual
        $futureTickets = Ticket::where('user_id', $userId)
            ->whereHas('screening', function($query) use ($now, $currentScreeningId) {
                $query->where('date_time', '>', $now)
                      ->where('id', '!=', $currentScreeningId);
            })
            ->count();
            
        return $futureTickets > 0;
    }
    
    /**
     * Confirma el pago y finaliza la reserva
     */
    public function confirmTickets(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_ids' => 'required|array',
                'ticket_ids.*' => 'exists:tickets,id'
            ]);
            
            if ($validator->fails()) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            
            $userId = Auth::id();
            $ticketIds = $request->ticket_ids;
            
            // Verificar que los tickets pertenecen al usuario
            $tickets = Ticket::whereIn('id', $ticketIds)
                ->where('user_id', $userId)
                ->get();
                
            if ($tickets->count() != count($ticketIds)) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Algunos tickets no pertenecen al usuario o ya han sido confirmados'
                    ], 400);
                } else {
                    return redirect()->back()->with('error', 'Algunos tickets no pertenecen al usuario o ya han sido confirmados');
                }
            }
            
            DB::beginTransaction();
            
            // No necesitamos actualizar el estado de los tickets
            // ya que no tenemos un campo status
            
            DB::commit();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Compra confirmada con éxito',
                    'tickets' => $tickets
                ], 200);
            } else {
                return redirect()->route('tickets.index')
                    ->with('success', 'Compra confirmada con éxito');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al confirmar la compra',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Error al confirmar la compra: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Cancela tickets reservados
     */
    public function cancelTickets(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_ids' => 'required|array',
                'ticket_ids.*' => 'exists:tickets,id'
            ]);
            
            if ($validator->fails()) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            
            $userId = Auth::id();
            $ticketIds = $request->ticket_ids;
            
            // Verificar que los tickets pertenecen al usuario
            $tickets = Ticket::whereIn('id', $ticketIds)
                ->where('user_id', $userId)
                ->get();
                
            if ($tickets->count() != count($ticketIds)) {
                if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                    return response()->json([
                        'message' => 'Algunos tickets no pertenecen al usuario o no pueden ser cancelados'
                    ], 400);
                } else {
                    return redirect()->back()->with('error', 'Algunos tickets no pertenecen al usuario o no pueden ser cancelados');
                }
            }
            
            DB::beginTransaction();
            
            // Liberar asientos y eliminar tickets
            foreach ($tickets as $ticket) {
                // Liberar asiento
                $seat = $ticket->seat;
                if ($seat) {
                    $seat->update(['status' => 'available']);
                }
                
                // Eliminar ticket
                $ticket->delete();
            }
            
            DB::commit();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Tickets cancelados con éxito'
                ], 200);
            } else {
                return redirect()->route('tickets.index')
                    ->with('success', 'Tickets cancelados con éxito');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Error al cancelar los tickets',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Error al cancelar los tickets: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Obtiene los tickets del usuario actual
     */
    public function getUserTickets(Request $request)
    {
        $userId = Auth::id();
        
        $tickets = Ticket::with(['screening.movie', 'screening.auditorium', 'seat'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($tickets);
        }
        
        // Si es una solicitud web, devolver vista
        return view('tickets.index', compact('tickets'));
    }
    
    /**
     * Obtiene detalles de un ticket específico
     */
    public function getTicketDetails(Request $request, $id)
    {
        $userId = Auth::id();
        
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($ticket);
        }
        
        // Si es una solicitud web, devolver vista
        return view('tickets.show', compact('ticket'));
    }
    
    /**
     * Genera un boleto imprimible con todos los detalles
     */
    public function generateTicketPdf(Request $request, $id)
    {
        $userId = Auth::id();
        
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereHas('user', function($q) {
                          $q->whereHas('role', function($r) {
                              $r->where('name', 'guest');
                          });
                      });
            })
            ->firstOrFail();
        
        // Usar PdfService para generar el PDF
        $pdfPath = $this->pdfService->generateTicketPdf($ticket);
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Ticket generado con éxito',
                'pdf_url' => url('storage/tickets/ticket_' . $ticket->id . '.pdf')
            ]);
        }
        
        // Si es una solicitud web, devolver el PDF directamente
        return response()->file($pdfPath);
    }
    
    /**
     * Permite a usuarios invitados acceder a sus tickets mediante un token temporal
     */
    public function getGuestTicket(Request $request, $id, $token)
    {
        // Buscar el ticket y su usuario asociado
        $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
            ->where('id', $id)
            ->whereHas('user', function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'guest');
                });
            })
            ->firstOrFail();
            
        // Validar que el token sea válido
        $expectedToken = md5($ticket->user->email . $ticket->id . config('app.key'));
        
        if ($token !== $expectedToken) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'Token de acceso inválido'
                ], 403);
            } else {
                return redirect()->route('home')->with('error', 'Token de acceso inválido');
            }
        }
        
        // Generar los mismos datos que en generateTicketPdf
        $ticketData = [
            'ticket_id' => $ticket->id,
            'purchase_date' => $ticket->purchase_date,
            'movie' => [
                'title' => $ticket->screening->movie->title,
                'duration' => $ticket->screening->movie->duration,
                'classification' => $ticket->screening->movie->classification
            ],
            'screening' => [
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name
            ],
            'seat' => [
                'number' => $ticket->seat->number,
                'is_vip' => $ticket->seat->isVip()
            ],
            'user' => [
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'snack' => $ticket->snack ? [
                'name' => $ticket->snack->name,
                'quantity' => $ticket->snack_quantity,
                'price' => $ticket->snack->price * $ticket->snack_quantity
            ] : null,
            'total_price' => $ticket->total_pay
        ];
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Ticket encontrado',
                'ticket_data' => $ticketData
            ]);
        }
        
        // Si es una solicitud web, devolver vista para impresión
        return view('tickets.guest_print', compact('ticketData'));
    }
    
    /**
     * Procesa la compra directa de tickets
     */
    public function purchase(Request $request)
    {
        try {
            // Validación de datos de entrada
            $validator = Validator::make($request->all(), [
                'screening_id' => 'required|exists:screenings,id',
                'seats' => 'required|array',
                'seats.*.number' => 'required|string'
                // Eliminamos la validación del confirmation_code
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $userId = Auth::id();
            $screeningId = $request->screening_id;
            $seats = $request->seats;
            
            // Obtener la sesión
            $screening = Screening::findOrFail($screeningId);
            
            DB::beginTransaction();
            
            $tickets = [];
            $totalAmount = 0;
            
            // Procesar cada asiento
            foreach ($seats as $seatData) {
                // Verificar si el asiento existe en la base de datos
                $seat = Seat::where('number', $seatData['number'])
                    ->where('auditorium_id', $screening->auditorium_id)
                    ->first();
                
                if (!$seat) {
                    // Si el asiento no existe, lo creamos
                    $seat = new Seat();
                    $seat->auditorium_id = $screening->auditorium_id;
                    $seat->number = $seatData['number']; // Usamos directamente el número proporcionado
                    $seat->status = 'busy'; // Lo marcamos como ocupado
                    $seat->save();
                } else {
                    // Si ya existe, verificamos que esté disponible
                    if ($seat->status === 'busy') {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'El asiento ' . $seat->number . ' ya está ocupado'
                        ], 400);
                    }
                    
                    // Marcar como ocupado
                    $seat->status = 'busy';
                    $seat->save();
                }
                
                // Calcular precio del asiento
                $price = $screening->calculatePrice($seat->number);
                $totalAmount += $price;
                
                // Crear el ticket
                $ticket = new Ticket();
                $ticket->user_id = $userId;
                $ticket->screening_id = $screeningId;
                $ticket->seat_id = $seat->id;
                // No usamos status, solo nos interesa que el asiento esté ocupado
                $ticket->quantity = 1; // Agregamos 1 como valor por defecto para quantity
                $ticket->total_pay = $price;
                $ticket->purchase_date = now();
                // Eliminamos la asignación del confirmation_code
                $ticket->save();
                
                $tickets[] = $ticket;
                
                // Generar PDF para este ticket y enviar correo electrónico
                // Separamos la generación del PDF y el envío de correo para mejor manejo de errores
                
                // Paso 1: Generar el PDF
                try {
                    // Cargamos completamente el ticket con todas sus relaciones para evitar problemas
                    $ticket->load(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack']);
                    
                    // Paso 1: Generar el PDF
                    $pdfPath = $this->pdfService->generateTicketPdf($ticket);
                    \Log::info('PDF generado correctamente para el ticket ID: ' . $ticket->id);
                    \Log::info('Ruta del PDF: ' . $pdfPath);
                    
                    // Verificar que el PDF realmente existe
                    if (file_exists($pdfPath)) {
                        \Log::info('El archivo PDF existe en la ruta esperada: ' . $pdfPath);
                        
                        // Paso 2: Enviar el correo
                        $emailSent = $this->mailService->sendTicketEmail($ticket, $pdfPath);
                        
                        if ($emailSent) {
                            \Log::info('Correo enviado correctamente para el ticket ID: ' . $ticket->id);
                        } else {
                            \Log::warning('No se pudo enviar el correo para el ticket ID: ' . $ticket->id);
                        }
                    } else {
                        \Log::error('El archivo PDF NO existe en la ruta: ' . $pdfPath);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al enviar correo o generar PDF: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Compra procesada con éxito',
                'tickets' => $tickets,
                'total_amount' => $totalAmount,
                'email_sent' => true
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al procesar la compra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Valida un ticket mediante su ID
     * Este endpoint es útil para validar tickets mediante escaneo de QR
     */
    public function validateTicket(Request $request, $id)
    {
        try {
            // Buscar el ticket con todas sus relaciones
            $ticket = Ticket::with(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack'])
                ->where('id', $id)
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Ticket no encontrado'
                ], 404);
            }
            
            // Verificar si el ticket es para una proyección futura (es válido)
            $screening = $ticket->screening;
            $now = Carbon::now();
            $screeningTime = Carbon::parse($screening->date_time);
            
            // Margen de tiempo para permitir la entrada (15 minutos antes y hasta 30 minutos después)
            $validFrom = $screeningTime->copy()->subMinutes(15);
            $validUntil = $screeningTime->copy()->addMinutes(30);
            
            if ($now->between($validFrom, $validUntil)) {
                $timeStatus = 'valid';
                $timeMessage = 'El ticket es válido para la función actual';
            } elseif ($now->lt($validFrom)) {
                $timeStatus = 'early';
                $timeMessage = 'Es demasiado pronto para este ticket. Válido a partir de ' . $validFrom->format('H:i');
            } else {
                $timeStatus = 'expired';
                $timeMessage = 'Este ticket ha expirado. Era válido hasta ' . $validUntil->format('H:i');
            }
            
            // Devolver información detallada del ticket
            return response()->json([
                'valid' => $timeStatus === 'valid',
                'status' => $timeStatus,
                'message' => $timeMessage,
                'ticket' => [
                    'id' => $ticket->id,
                    'movie' => [
                        'title' => $screening->movie->title,
                        'duration' => $screening->movie->duration,
                        'classification' => $screening->movie->classification
                    ],
                    'screening' => [
                        'date_time' => $screening->date_time,
                        'auditorium' => $screening->auditorium->name
                    ],
                    'seat' => [
                        'number' => $ticket->seat->number,
                        'is_vip' => $ticket->seat->isVip()
                    ],
                    'user' => [
                        'name' => $ticket->user->name,
                        'email' => $ticket->user->email
                    ],
                    'purchase_date' => $ticket->purchase_date,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error al validar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}