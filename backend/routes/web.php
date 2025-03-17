<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    return view('welcome');
});

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
