<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\UserTicketsController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieGenreController;
use App\Http\Controllers\AuditoriumController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta de prueba para verificar la conexión con el backend
Route::get('/test', function() {
    return response()->json(['message' => 'Backend API is working!', 'timestamp' => now()->toDateTimeString()]);
});

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::get('/user-profile', [AuthController::class, 'user'])->middleware('auth:sanctum'); // Ruta adicional para compatibilidad

// Ruta de diagnóstico CORS
Route::options('/test-cors', function() {
    return response()->json(['message' => 'CORS preflight request successful']);
});

Route::get('/test-cors', function() {
    return response()->json(['message' => 'CORS GET request successful', 'timestamp' => now()->toDateTimeString()]);
});

// Rutas públicas
Route::prefix('screening')->group(function() {
    Route::get('/', [ScreeningController::class, 'index']);
    Route::get('/{id}', [ScreeningController::class, 'show']);
    Route::get('/{id}/seats', [ScreeningController::class, 'getAvailableSeats']);
    Route::post('/', [ScreeningController::class, 'store']);
    Route::put('/{id}', [ScreeningController::class, 'update']);
    Route::delete('/{id}', [ScreeningController::class, 'destroy']);
    Route::put('/{id}/toggle-active', [ScreeningController::class, 'toggleActive']);
});

// Ruta para obtener screenings por fecha
Route::get('/screenings', [ScreeningController::class, 'getScreeningsByDate']);

// Rutas públicas para películas
Route::prefix('movie')->group(function() {
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/current', [MovieController::class, 'getCurrentMovies']);
    Route::get('/getMovies', [MovieController::class, 'getMovies']);
    Route::get('/{id}/screenings', [MovieController::class, 'getScreenings']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

// Rutas públicas para géneros
Route::prefix('genre')->group(function() {
    Route::get('/', [MovieGenreController::class, 'index']);
    Route::get('/{id}', [MovieGenreController::class, 'show']);
    Route::get('/{id}/movies', [MovieGenreController::class, 'getMovies']);
});

// Rutas públicas para snacks
Route::prefix('snack')->group(function() {
    Route::get('/', [SnackController::class, 'index']);
    Route::get('/{id}', [SnackController::class, 'show']);
});

// Rutas públicas para asientos
Route::prefix('seats')->group(function() {
    Route::get('/', [SeatController::class, 'index']);
    Route::put('/{id}/status', [SeatController::class, 'updateStatus']);
    Route::post('/reset', [SeatController::class, 'resetSeats']);
    Route::get('/{id}', [SeatController::class, 'show']);
});

// Rutas públicas para auditorios
Route::prefix('auditorium')->group(function() {
    Route::get('/', [AuditoriumController::class, 'index']);
    Route::get('/{id}', [AuditoriumController::class, 'show']);
});

// Las rutas para el recurso "seat" se han eliminado para evitar duplicidad 
// Se mantiene solo la versión plural "seats" que sigue la convención RESTful

// La ruta '/seats-json' ha sido eliminada por ser redundante con '/api/seats'

// Rutas para invitados
Route::prefix('guest')->group(function() {
    // Procesar compra como invitado
    Route::post('/purchase', [GuestController::class, 'processGuestPurchase']);
    
    // Ver ticket comprado como invitado (usando un token temporal)
    Route::get('/ticket/{id}/{token}', [TicketController::class, 'getGuestTicket']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function() {
    // Ya existe una ruta para /user en las rutas de autenticación
    
    // Rutas para gestión de tickets y asientos
    Route::prefix('tickets')->group(function() {
        // Reserva y gestión de tickets
        Route::post('/reserve', [TicketController::class, 'reserveSeats']);
        Route::post('/confirm', [TicketController::class, 'confirmTickets']);
        Route::post('/cancel', [TicketController::class, 'cancelTickets']);
        
        // Gestión de snack para tickets - se excluye según indicación
        
        // Consulta de tickets del usuario
        Route::get('/', [TicketController::class, 'getUserTickets']);
        Route::get('/{id}/generate-pdf', [TicketController::class, 'generateTicketPdf']);
        Route::get('/{id}', [TicketController::class, 'getTicketDetails']);
        Route::get('/screening/{id}/can-buy', [UserTicketsController::class, 'canBuyTickets']);
        Route::post('/purchase', [TicketController::class, 'purchase']);
    });
    
    // Rutas para administradores
    Route::middleware('role:admin')->group(function() {
        // Gestión de sesiones (movida a rutas públicas)
        
        // Gestión de películas
        Route::post('/movie', [MovieController::class, 'store']);
        Route::put('/movie/{id}', [MovieController::class, 'update']);
        Route::delete('/movie/{id}', [MovieController::class, 'destroy']);
        
        // Gestión de géneros
        Route::post('/genre', [MovieGenreController::class, 'store']);
        Route::put('/genre/{id}', [MovieGenreController::class, 'update']);
        Route::delete('/genre/{id}', [MovieGenreController::class, 'destroy']);
        
        // Gestión de auditorios
        Route::post('/auditorium', [AuditoriumController::class, 'store']);
        Route::put('/auditorium/{id}', [AuditoriumController::class, 'update']);
        Route::delete('/auditorium/{id}', [AuditoriumController::class, 'destroy']);
        
        // Gestión de asientos (movida a rutas públicas)
    });
});
