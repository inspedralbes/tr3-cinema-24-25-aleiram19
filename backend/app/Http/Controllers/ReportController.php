<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Muestra la página de informes con opciones
     */
    public function index()
    {
        // Obtener estadísticas rápidas para mostrar en el dashboard
        $stats = [
            'monthly_revenue' => Ticket::whereMonth('tickets.created_at', now()->month)
                ->whereYear('tickets.created_at', now()->year)
                ->sum('total_pay'),
            'tickets_sold' => Ticket::whereMonth('tickets.created_at', now()->month)
                ->whereYear('tickets.created_at', now()->year)
                ->count(),
            'avg_occupancy' => 75, // Simplificado para este ejemplo
            'new_users' => User::whereMonth('users.created_at', now()->month)
                ->whereYear('users.created_at', now()->year)
                ->count(),
        ];
        
        // Datos para el gráfico de los últimos 7 días
        $lastWeekSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $amount = Ticket::whereDate('tickets.created_at', $date)->sum('total_pay');
            $lastWeekSales[] = [
                'date' => $date,
                'amount' => $amount ?? 0
            ];
        }
        
        return view('admin.reports.index', compact('stats', 'lastWeekSales'));
    }

    /**
     * Genera el informe mensual
     */
    public function monthlyReport(Request $request)
    {
        // Obtener el mes y año del formulario, o usar el mes actual si no se especifica
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $date = Carbon::createFromDate($year, $month, 1);
        $monthName = $date->translatedFormat('F');
        $startDate = $date->startOfMonth()->format('Y-m-d');
        $endDate = $date->endOfMonth()->format('Y-m-d');

        // Obtener estadísticas de películas vendidas
        $movieStats = Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
            ->join('screenings', 'tickets.screening_id', '=', 'screenings.id')
            ->join('movies', 'screenings.movie_id', '=', 'movies.id')
            ->select('movies.title', DB::raw('COUNT(*) as tickets_sold'))
            ->groupBy('movies.id', 'movies.title')
            ->orderBy('tickets_sold', 'desc')
            ->get();

        $totalMoviesSold = $movieStats->sum('tickets_sold');

        // Estadísticas de asientos
        $tickets = Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
            ->with('seat')
            ->get();
            
        $vipSeats = 0;
        $regularSeats = 0;
        
        foreach ($tickets as $ticket) {
            if ($ticket->seat->is_vip) {
                $vipSeats++;
            } else {
                $regularSeats++;
            }
        }
        
        $seatStats = (object)[
            'vip_seats' => $vipSeats,
            'regular_seats' => $regularSeats
        ];

        // Estadísticas de horarios
        $timeStats = Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
            ->join('screenings', 'tickets.screening_id', '=', 'screenings.id')
            ->select(
                DB::raw("CASE 
                    WHEN TIME(screenings.date_time) BETWEEN '06:00:00' AND '12:00:00' THEN 'Mañana' 
                    WHEN TIME(screenings.date_time) BETWEEN '12:00:01' AND '18:00:00' THEN 'Tarde' 
                    ELSE 'Noche' 
                END as time_period"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('time_period')
            ->get();

        // Usuarios registrados durante el mes
        $newUsers = User::whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate])
            ->count();

        // Total de usuarios activos (que han comprado tickets en el mes)
        $activeUsers = Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
            ->select('user_id')
            ->distinct()
            ->count();

        // Total de usuarios registrados en el sistema
        $totalUsers = User::count();

        // Ingresos totales del mes
        $totalRevenue = Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
            ->sum('total_pay');

        $reportData = [
            'month' => $monthName,
            'year' => $year,
            'movieStats' => $movieStats,
            'totalMoviesSold' => $totalMoviesSold,
            'seatStats' => $seatStats,
            'timeStats' => $timeStats,
            'newUsers' => $newUsers,
            'activeUsers' => $activeUsers,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
        ];

        // Si se solicita PDF, generar y devolver
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.reports.monthly_pdf', $reportData);
            return $pdf->download("informe_mensual_{$monthName}_{$year}.pdf");
        }

        // De lo contrario, mostrar vista normal
        return view('admin.reports.monthly', $reportData);
    }

    /**
     * Genera un informe detallado de películas
     */
    public function movieReport(Request $request)
    {
        // Obtener el mes y año del formulario, o usar el mes actual si no se especifica
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $date = Carbon::createFromDate($year, $month, 1);
        $monthName = $date->translatedFormat('F');
        $startDate = $date->startOfMonth()->format('Y-m-d');
        $endDate = $date->endOfMonth()->format('Y-m-d');

        // Obtener estadísticas detalladas de películas
        $movies = Movie::select('movies.*')
            ->addSelect([
                'tickets_sold' => Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
                    ->join('screenings', 'tickets.screening_id', '=', 'screenings.id')
                    ->whereColumn('screenings.movie_id', 'movies.id')
                    ->selectRaw('COUNT(tickets.id)'),
                'revenue' => Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
                    ->join('screenings', 'tickets.screening_id', '=', 'screenings.id')
                    ->whereColumn('screenings.movie_id', 'movies.id')
                    ->selectRaw('SUM(tickets.total_pay)')
            ])
            ->orderBy('tickets_sold', 'desc')
            ->get();

        $reportData = [
            'month' => $monthName,
            'year' => $year,
            'movies' => $movies,
        ];

        // Si se solicita PDF, generar y devolver
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.reports.movies_pdf', $reportData);
            return $pdf->download("informe_peliculas_{$monthName}_{$year}.pdf");
        }

        // De lo contrario, mostrar vista normal
        return view('admin.reports.movies', $reportData);
    }

    /**
     * Genera un informe diario con mapa de butacas y estadísticas
     */
    public function dailyReport(Request $request)
    {
        // Obtener la fecha seleccionada o usar la fecha actual si no se especifica
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);
        $formattedDate = $selectedDate->translatedFormat('d F Y');
        
        // Obtener todas las proyecciones (screenings) del día
        $screenings = Screening::whereDate('screenings.date_time', $date)
            ->join('movies', 'screenings.movie_id', '=', 'movies.id')
            ->select('screenings.*', 'movies.title as movie_title')
            ->orderBy('screenings.date_time')
            ->get();
            
        // Para cada proyección, obtener estadísticas de butacas
        $screeningStats = [];
        $totalRegularTickets = 0;
        $totalVipTickets = 0;
        $totalRegularRevenue = 0;
        $totalVipRevenue = 0;
        
        foreach ($screenings as $screening) {
            // Obtener todos los tickets vendidos para esta proyección
            $tickets = Ticket::where('screening_id', $screening->id)->get();
            
            // Obtener todas las butacas ocupadas
            $occupiedSeats = Ticket::where('screening_id', $screening->id)
                ->pluck('seat_id')
                ->toArray();
                
            // Obtener todas las butacas disponibles para esta sala
            $allSeats = Seat::all();
            
            // Obtener todos los tickets para esta proyección
            $allTickets = Ticket::where('screening_id', $screening->id)
                ->with('seat')
                ->get();
            
            // Contar tickets normales y VIP usando el atributo computado is_vip
            $regularTickets = 0;
            $vipTickets = 0;
            $regularRevenue = 0;
            $vipRevenue = 0;
            
            foreach ($allTickets as $ticket) {
                if ($ticket->seat->is_vip) {
                    $vipTickets++;
                    $vipRevenue += $ticket->total_pay;
                } else {
                    $regularTickets++;
                    $regularRevenue += $ticket->total_pay;
                }
            }
                
            // Acumular totales
            $totalRegularTickets += $regularTickets;
            $totalVipTickets += $vipTickets;
            $totalRegularRevenue += $regularRevenue;
            $totalVipRevenue += $vipRevenue;
            
            // Guardar estadísticas para esta proyección
            $screeningStats[] = [
                'screening' => $screening,
                'allSeats' => $allSeats,
                'occupiedSeats' => $occupiedSeats,
                'regularTickets' => $regularTickets,
                'vipTickets' => $vipTickets,
                'regularRevenue' => $regularRevenue,
                'vipRevenue' => $vipRevenue,
                'totalRevenue' => $regularRevenue + $vipRevenue,
                'occupancyRate' => count($allSeats) > 0 ? (count($occupiedSeats) / count($allSeats)) * 100 : 0
            ];
        }
        
        $reportData = [
            'date' => $formattedDate,
            'screenings' => $screenings,
            'screeningStats' => $screeningStats,
            'totalRegularTickets' => $totalRegularTickets,
            'totalVipTickets' => $totalVipTickets,
            'totalRegularRevenue' => $totalRegularRevenue,
            'totalVipRevenue' => $totalVipRevenue,
            'totalRevenue' => $totalRegularRevenue + $totalVipRevenue,
            'totalTickets' => $totalRegularTickets + $totalVipTickets
        ];
        
        // Si se solicita PDF, generar y devolver
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.reports.daily_pdf', $reportData);
            return $pdf->download("informe_diario_{$date}.pdf");
        }
        
        // De lo contrario, mostrar vista normal
        return view('admin.reports.daily', $reportData);
    }
    
    /**
     * Genera un informe detallado de usuarios
     */
    public function userReport(Request $request)
    {
        // Obtener el mes y año del formulario, o usar el mes actual si no se especifica
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $date = Carbon::createFromDate($year, $month, 1);
        $monthName = $date->translatedFormat('F');
        $startDate = $date->startOfMonth()->format('Y-m-d');
        $endDate = $date->endOfMonth()->format('Y-m-d');

        // Usuarios más activos (por número de tickets comprados)
        $activeUsers = User::select('users.*')
            ->addSelect([
                'tickets_bought' => Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
                    ->whereColumn('tickets.user_id', 'users.id')
                    ->selectRaw('COUNT(tickets.id)'),
                'total_spent' => Ticket::whereBetween(DB::raw('DATE(tickets.created_at)'), [$startDate, $endDate])
                    ->whereColumn('tickets.user_id', 'users.id')
                    ->selectRaw('SUM(tickets.total_pay)')
            ])
            ->orderBy('tickets_bought', 'desc')
            ->get();

        $reportData = [
            'month' => $monthName,
            'year' => $year,
            'activeUsers' => $activeUsers,
            'newUsers' => User::whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate])->count(),
            'totalUsers' => User::count(),
        ];

        // Si se solicita PDF, generar y devolver
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.reports.users_pdf', $reportData);
            return $pdf->download("informe_usuarios_{$monthName}_{$year}.pdf");
        }

        // De lo contrario, mostrar vista normal
        return view('admin.reports.users', $reportData);
    }
}
