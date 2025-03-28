@extends('layouts.app')

@section('title', "Informe Mensual - $month $year")

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-chart-line"></i> Informe Mensual: {{ $month }} {{ $year }}</h2>
            <div>
                <form action="{{ route('admin.reports.monthly') }}" method="GET" class="d-inline">
                    <input type="hidden" name="month" value="{{ request()->input('month', Carbon\Carbon::now()->month) }}">
                    <input type="hidden" name="year" value="{{ request()->input('year', Carbon\Carbon::now()->year) }}">
                    <input type="hidden" name="download_pdf" value="1">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                    </button>
                </form>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="table-content">
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg text-white overflow-hidden" style="background: linear-gradient(to right, #1a365d, #3182ce);">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center">
                            <i class="fas fa-ticket-alt fa-2x me-3 text-blue-200"></i>
                            <h5 class="m-0">Tickets Vendidos</h5>
                        </div>
                        <div class="card-body text-center pt-0">
                            <h3 class="display-3 fw-bold mb-0 text-white">{{ $totalMoviesSold }}</h3>
                            <div class="progress mt-3" style="height: 8px; background-color: rgba(0, 0, 0, 0.2);">
                                <div class="progress-bar" role="progressbar" style="width: 75%; background-color: rgba(255, 255, 255, 0.6);"></div>
                            </div>
                            <p class="mt-2 text-blue-200">
                                <i class="fas fa-arrow-up me-1"></i> 
                                +12.5% vs mes anterior
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg text-white overflow-hidden" style="background: linear-gradient(to right, #1c4532, #38a169);">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center">
                            <i class="fas fa-chair fa-2x me-3 text-green-200"></i>
                            <h5 class="m-0">Asientos Ocupados</h5>
                        </div>
                        <div class="card-body text-center pt-0">
                            <h3 class="display-3 fw-bold mb-0 text-white">{{ $seatStats->vip_seats + $seatStats->regular_seats }}</h3>
                            <div class="progress mt-3" style="height: 8px; background-color: rgba(0, 0, 0, 0.2);">
                                <div class="progress-bar" role="progressbar" style="width: 68%; background-color: rgba(255, 255, 255, 0.6);"></div>
                            </div>
                            <p class="mt-2 text-green-200">
                                <i class="fas fa-arrow-up me-1"></i> 
                                +8.3% vs mes anterior
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg text-white overflow-hidden" style="background: linear-gradient(to right, #44337a, #805ad5);">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center">
                            <i class="fas fa-users fa-2x me-3 text-purple-200"></i>
                            <h5 class="m-0">Usuarios</h5>
                        </div>
                        <div class="card-body text-center pt-0">
                            <h3 class="display-3 fw-bold mb-0 text-white">{{ $totalUsers }}</h3>
                            <div class="progress mt-3" style="height: 8px; background-color: rgba(0, 0, 0, 0.2);">
                                <div class="progress-bar" role="progressbar" style="width: 82%; background-color: rgba(255, 255, 255, 0.6);"></div>
                            </div>
                            <p class="mt-2 text-purple-200">
                                <i class="fas fa-arrow-up me-1"></i> 
                                +15.7% vs mes anterior
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg text-white overflow-hidden" style="background: linear-gradient(to right, #742a2a, #e53e3e);">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center">
                            <i class="fas fa-euro-sign fa-2x me-3 text-red-200"></i>
                            <h5 class="m-0">Ingresos</h5>
                        </div>
                        <div class="card-body text-center pt-0">
                            <h3 class="display-3 fw-bold mb-0 text-white">{{ number_format($totalRevenue, 0) }}€</h3>
                            <div class="progress mt-3" style="height: 8px; background-color: rgba(0, 0, 0, 0.2);">
                                <div class="progress-bar" role="progressbar" style="width: 78%; background-color: rgba(255, 255, 255, 0.6);"></div>
                            </div>
                            <p class="mt-2 text-red-200">
                                <i class="fas fa-arrow-up me-1"></i> 
                                +9.4% vs mes anterior
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Películas Vendidas - Más visual con gráfico de barras -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg bg-dark text-white">
                        <div class="card-header text-white py-3 rounded-top" style="background: linear-gradient(to right, #2c5282, #434190);">
                            <h5 class="card-title mb-0 d-flex align-items-center">
                                <i class="fas fa-film me-2"></i>Películas Más Vendidas
                            </h5>
                        </div>
                        <div class="card-body">
                            @forelse($movieStats->take(5) as $movie)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                    <span class="text-truncate text-white" style="max-width: 200px; font-size: 1.05rem;" title="{{ $movie->title }}">{{ $movie->title }}</span>
                                    <span class="text-white fw-bold">{{ $movie->tickets_sold }} tickets</span>
                                </div>
                                    <div class="progress" style="height: 20px; background-color: rgba(255, 255, 255, 0.1);">
                                        @php
                                            $percentage = ($movie->tickets_sold / $totalMoviesSold) * 100;
                                            $barColor = match(true) {
                                                $percentage > 50 => 'bg-success',
                                                $percentage > 30 => 'bg-info',
                                                $percentage > 15 => 'bg-primary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <div class="progress-bar {{ $barColor }} progress-bar-striped" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            {{ round($percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">No hay datos disponibles</div>
                            @endforelse
                            
                            @if(count($movieStats) > 5)
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#moreMovies">
                                        Ver más películas <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="moreMovies">
                                    @foreach($movieStats->slice(5) as $movie)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-truncate" style="max-width: 200px;" title="{{ $movie->title }}">{{ $movie->title }}</span>
                                                <span class="text-dark fw-bold">{{ $movie->tickets_sold }} tickets</span>
                                            </div>
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $percentage = ($movie->tickets_sold / $totalMoviesSold) * 100;
                                                    $barColor = match(true) {
                                                        $percentage > 50 => 'bg-success',
                                                        $percentage > 30 => 'bg-info',
                                                        $percentage > 15 => 'bg-primary',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <div class="progress-bar {{ $barColor }}" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%; font-size: 1rem; font-weight: bold; text-shadow: 1px 1px 1px rgba(0,0,0,0.5);"
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            {{ round($percentage, 1) }}%
                                        </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Horarios - Visualización mejorada -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 rounded-lg shadow-lg bg-white">
                        <div class="card-header text-white py-3 rounded-top" style="background: linear-gradient(to right, #d97706, #c2410c);">
                            <h5 class="card-title mb-0 d-flex align-items-center">
                                <i class="fas fa-clock me-2"></i>Ventas por Horarios
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center my-3">
                                <div class="position-relative" style="width: 220px; height: 220px;">
                                    <!-- Crear un diagrama circular para visualizar la distribución -->
                                    <div class="position-relative">
                                        <svg viewBox="0 0 36 36" width="100%" height="100%" class="circular-chart">
                                            @php
                                                $colors = ['#FF9F43', '#5A8DEE', '#39DA8A'];
                                                $offset = 0;
                                                $totalCount = $timeStats->sum('count');
                                            @endphp
                                            
                                            @foreach($timeStats as $index => $time)
                                                @php
                                                    $percentage = ($time->count / $totalCount) * 100;
                                                    $dashArray = $percentage * 2.64; // 2.64 = 264 / 100
                                                    $dashOffset = 66 - $offset;
                                                    $offset += $dashArray;
                                                    $color = $colors[$index % count($colors)];
                                                @endphp
                                                
                                                <circle class="donut-segment" 
                                                    cx="18" cy="18" r="15.91"
                                                    style="stroke: {{ $color }};" 
                                                    stroke-width="5"
                                                    stroke-dasharray="{{ $dashArray }} {{ 100 - $dashArray }}"
                                                    stroke-dashoffset="{{ $dashOffset }}"
                                                    fill="none">
                                                </circle>
                                            @endforeach
                                            
                                            <!-- Centro del círculo -->
                                            <circle cx="18" cy="18" r="10" fill="white"></circle>
                                            <text x="18" y="18" text-anchor="middle" dominant-baseline="middle" font-size="5px" font-weight="bold">
                                                {{ $totalCount }}
                                            </text>
                                            <text x="18" y="22" text-anchor="middle" dominant-baseline="middle" font-size="2.5px">
                                                tickets
                                            </text>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                @forelse($timeStats as $index => $time)
                                    @php
                                        $percentage = ($time->count / $totalCount) * 100;
                                        $color = $colors[$index % count($colors)];
                                        $icon = match($time->time_period) {
                                            'Mañana' => 'fa-sun',
                                            'Tarde' => 'fa-cloud-sun',
                                            'Noche' => 'fa-moon',
                                            default => 'fa-clock'
                                        };
                                    @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="color-indicator me-2" style="width: 20px; height: 20px; background-color: {{ $color }}; border-radius: 4px;"></div>
                                        <div class="d-flex justify-content-between align-items-center flex-grow-1">
                                            <div>
                                                <i class="fas {{ $icon }} me-2"></i>
                                                <span class="fw-medium">{{ $time->time_period }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold">{{ $time->count }}</span>
                                                <span class="text-muted ms-2">({{ round($percentage, 1) }}%)</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info">No hay datos disponibles</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Asientos -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-dark text-white">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-chair me-2"></i>Distribución de Asientos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipo de Asiento</th>
                                            <th class="text-end">Cantidad</th>
                                            <th class="text-end">Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>VIP</td>
                                            <td class="text-end">{{ $seatStats->vip_seats }}</td>
                                            <td class="text-end">
                                                {{ round(($seatStats->vip_seats / ($seatStats->vip_seats + $seatStats->regular_seats)) * 100, 1) }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Regular</td>
                                            <td class="text-end">{{ $seatStats->regular_seats }}</td>
                                            <td class="text-end">
                                                {{ round(($seatStats->regular_seats / ($seatStats->vip_seats + $seatStats->regular_seats)) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Usuarios -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-dark text-white">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Usuarios</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>Métrica</th>
                                            <th class="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Nuevos usuarios registrados</td>
                                            <td class="text-end">{{ $newUsers }}</td>
                                        </tr>
                                        <tr>
                                            <td>Usuarios activos (compraron tickets)</td>
                                            <td class="text-end">{{ $activeUsers }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total de usuarios registrados</td>
                                            <td class="text-end">{{ $totalUsers }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para los gráficos y visualizaciones -->
<style>
    .circular-chart {
        display: block;
        margin: 10px auto;
        max-width: 80%;
        max-height: 250px;
    }
    
    .circle {
        stroke-width: 2;
        stroke-linecap: round;
        fill: none;
    }
    
    .donut-segment {
        transition: stroke-dasharray 0.3s ease;
    }
    
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .card-header {
        border-bottom: none;
    }
    
    /* Animación para el número de tickets */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .display-3 {
        animation: pulse 2s infinite;
    }
</style>
@endsection