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
        // Obtenemos estadísticas reales de la base de datos
        $stats = [
            'active_movies' => \App\Models\Movie::count(),
            'scheduled_screenings' => \App\Models\Screening::whereDate('date_time', '>=', now())->count(),
            'registered_users' => \App\Models\User::count(),
            'total_tickets' => \App\Models\Ticket::count(),
            'weekly_sales' => \App\Models\Ticket::whereDate('created_at', '>=', now()->subDays(7))->sum('total_pay'),
            'monthly_sales' => \App\Models\Ticket::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_pay'),
            'week_tickets' => \App\Models\Ticket::whereDate('created_at', '>=', now()->subDays(7))->count(),
            'avg_rating' => 4.8, // Valor de ejemplo, ajustar según corresponda
        ];
        
        // Obtener datos de ventas diarias para la última semana
        $dailySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailySales[] = [
                'day' => now()->subDays($i)->format('D'),
                'date' => $date,
                'amount' => \App\Models\Ticket::whereDate('created_at', $date)->sum('total_pay') ?: 0,
                'tickets' => \App\Models\Ticket::whereDate('created_at', $date)->count() ?: 0,
            ];
        }
        
        return view('admin.dashboard', compact('stats', 'dailySales'));
    })->name('admin.dashboard');
    
    // Rutas para informes
    Route::prefix('admin/reports')->group(function () {
        Route::get('/', function() {
            $stats = [
                'monthly_revenue' => \App\Models\Ticket::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_pay'),
                'tickets_sold' => \App\Models\Ticket::count(),
                'avg_occupancy' => 75, // Valor de ejemplo
                'new_users' => \App\Models\User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'weekly_sales' => \App\Models\Ticket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->sum('total_pay'),
            ];
            
            // Datos de ventas diarias para la última semana (para el gráfico SVG)
            $lastWeekSales = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $lastWeekSales[] = [
                    'amount' => \App\Models\Ticket::whereDate('created_at', $date)->sum('total_pay') ?: 0
                ];
            }
            
            return view('admin.reports.index', compact('stats', 'lastWeekSales'));
        })->name('admin.reports.index');
        
        Route::get('/monthly', [App\Http\Controllers\ReportController::class, 'monthlyReport'])->name('admin.reports.monthly');
        Route::get('/movies', [App\Http\Controllers\ReportController::class, 'movieReport'])->name('admin.reports.movies');
        Route::get('/users', [App\Http\Controllers\ReportController::class, 'userReport'])->name('admin.reports.users');
        Route::get('/daily', [App\Http\Controllers\ReportController::class, 'dailyReport'])->name('admin.reports.daily');
    });
    
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
    Route::put('/{id}/toggle-active', [ScreeningController::class, 'toggleActive'])->name('screenings.toggle-active');
  });
});