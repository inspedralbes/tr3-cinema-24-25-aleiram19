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
    return response()->json(['message' => 'Backend API is working!']);
});

// Rutas públicas
Route::prefix('screening')->group(function() {
    Route::get('/', [ScreeningController::class, 'index']);
    Route::get('/{id}', [ScreeningController::class, 'show']);
    Route::get('/{id}/seats', [ScreeningController::class, 'getAvailableSeats']);
});

// Rutas públicas para películas
Route::prefix('movie')->group(function() {
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/current', [MovieController::class, 'getCurrentMovies']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

// Rutas públicas para géneros
Route::prefix('genre')->group(function() {
    Route::get('/', [MovieGenreController::class, 'index']);
    Route::get('/{id}', [MovieGenreController::class, 'show']);
    Route::get('/{id}/movies', [MovieGenreController::class, 'getMovies']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function() {
    // Perfil de usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rutas para gestión de tickets y asientos
    Route::prefix('tickets')->group(function() {
        // Reserva y gestión de tickets
        Route::post('/reserve', [TicketController::class, 'reserveSeats']);
        Route::post('/confirm', [TicketController::class, 'confirmTickets']);
        Route::post('/cancel', [TicketController::class, 'cancelTickets']);
        
        // Consulta de tickets del usuario
        Route::get('/', [TicketController::class, 'getUserTickets']);
        Route::get('/{id}', [TicketController::class, 'getTicketDetails']);
        Route::get('/screening/{id}/can-buy', [UserTicketsController::class, 'canBuyTickets']);
    });
    
    // Rutas para administradores
    Route::middleware('role:admin')->group(function() {
        // Gestión de sesiones
        Route::post('/screening', [ScreeningController::class, 'store']);
        Route::put('/screening/{id}', [ScreeningController::class, 'update']);
        Route::delete('/screening/{id}', [ScreeningController::class, 'destroy']);
        
        // Gestión de películas
        Route::post('/movie', [MovieController::class, 'store']);
        Route::put('/movie/{id}', [MovieController::class, 'update']);
        Route::delete('/movie/{id}', [MovieController::class, 'destroy']);
        
        // Gestión de géneros
        Route::post('/genre', [MovieGenreController::class, 'store']);
        Route::put('/genre/{id}', [MovieGenreController::class, 'update']);
        Route::delete('/genre/{id}', [MovieGenreController::class, 'destroy']);
        
        // Gestión de auditorios
        Route::get('/auditoriums', [AuditoriumController::class, 'index']);
        Route::get('/auditoriums/{id}', [AuditoriumController::class, 'show']);
        Route::post('/auditoriums', [AuditoriumController::class, 'store']);
        Route::put('/auditoriums/{id}', [AuditoriumController::class, 'update']);
        Route::delete('/auditoriums/{id}', [AuditoriumController::class, 'destroy']);
        
        // Gestión de asientos
        Route::put('/seats/{id}/status', [SeatController::class, 'updateStatus']);
        Route::post('/seats/reset', [SeatController::class, 'resetSeats']);
    });
});
