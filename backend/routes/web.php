<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScreeningController;   

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas de autenticación
Route::get('/', [App\Http\Controllers\AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthWebController::class, 'login'])->name('admin.login');
Route::post('/logout', [App\Http\Controllers\AuthWebController::class, 'logout'])->name('admin.logout');

// Redirigir cualquier intento de acceder a /welcome a la página de login
Route::get('/welcome', function() {
    return redirect()->route('login');
});

// Rutas protegidas por middleware de autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Rutas CRUD para Usuarios
    Route::prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Rutas CRUD para Películas
Route::prefix('admin/movies')->group(function () {
    Route::get('/', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/create', [MovieController::class, 'create'])->name('movies.create');
    Route::post('/', [MovieController::class, 'store'])->name('movies.store');
    Route::get('/{id}', [MovieController::class, 'show'])->name('movies.show');
    Route::get('/{id}/edit', [MovieController::class, 'edit'])->name('movies.edit');
    Route::put('/{id}', [MovieController::class, 'update'])->name('movies.update');
    Route::delete('/{id}', [MovieController::class, 'destroy'])->name('movies.destroy');
});

// Rutas CRUD para Asientos
Route::prefix('admin/seats')->group(function () {
    Route::get('/', [SeatController::class, 'index'])->name('seats.index');
    Route::get('/create', [SeatController::class, 'create'])->name('seats.create');
    Route::post('/', [SeatController::class, 'store'])->name('seats.store');
    Route::get('/{id}', [SeatController::class, 'show'])->name('seats.show');
    Route::get('/{id}/edit', [SeatController::class, 'edit'])->name('seats.edit');
    Route::put('/{id}', [SeatController::class, 'update'])->name('seats.update');
    Route::delete('/{id}', [SeatController::class, 'destroy'])->name('seats.destroy');
});

Route::prefix('admin/screening')->group(function () {
    Route::get('/', [ScreeningController::class, 'index'])->name('screenings.index');
    Route::get('/create', [ScreeningController::class, 'create'])->name('screenings.create');
    Route::post('/', [ScreeningController::class, 'store'])->name('screenings.store');
    Route::get('/{id}', [ScreeningController::class, 'show'])->name('screenings.show');
    Route::get('/{id}/edit', [ScreeningController::class, 'edit'])->name('screenings.edit');
    Route::put('/{id}', [ScreeningController::class, 'update'])->name('screenings.update');
    Route::delete('/{id}', [ScreeningController::class, 'destroy'])->name('screenings.destroy');
  });
});