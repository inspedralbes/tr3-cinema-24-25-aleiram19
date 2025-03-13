<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ScreeningController;
use App\Http\Controllers\Api\UserTicketsController;

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

// Rutas públicas
Route::prefix('screenings')->group(function() {
    Route::get('/', [ScreeningController::class, 'index']);
    Route::get('/{id}', [ScreeningController::class, 'show']);
    Route::get('/{id}/seats', [ScreeningController::class, 'getAvailableSeats']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function() {
    // Perfil de usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rutas para gestión de reservas
    Route::prefix('bookings')->group(function() {
        Route::post('/reserve', [BookingController::class, 'reserveSeats']);
        Route::post('/confirm', [BookingController::class, 'confirmBooking']);
        Route::post('/cancel', [BookingController::class, 'cancelBooking']);
    });
    
    // Rutas para consultar tickets del usuario
    Route::prefix('tickets')->group(function() {
        Route::get('/', [UserTicketsController::class, 'index']);
        Route::get('/{id}', [UserTicketsController::class, 'show']);
        Route::get('/screening/{id}/can-buy', [UserTicketsController::class, 'canBuyTickets']);
    });
    
    // Rutas para administradores
    Route::middleware('role:admin')->group(function() {
        Route::post('/screenings', [ScreeningController::class, 'store']);
        Route::put('/screenings/{id}', [ScreeningController::class, 'update']);
        Route::delete('/screenings/{id}', [ScreeningController::class, 'destroy']);
    });
});
