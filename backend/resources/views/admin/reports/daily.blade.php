@extends('layouts.app')

@section('title', "Informe Diario - $date")

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-calendar-day"></i> Informe Diario: {{ $date }}</h2>
            <div>
                <form action="{{ route('admin.reports.daily') }}" method="GET" class="d-inline">
                    <input type="hidden" name="date" value="{{ request()->input('date', Carbon\Carbon::now()->format('Y-m-d')) }}">
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
            <!-- Resumen global de entradas y recaudación -->
            <div class="daily-summary">
                <div class="row">
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="summary-data">
                                <h3>Total Entradas</h3>
                                <div class="summary-value">{{ $totalTickets }}</div>
                                <div class="summary-breakdown">
                                    <div>
                                        <span class="badge bg-primary">Normal: {{ $totalRegularTickets }}</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">VIP: {{ $totalVipTickets }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="summary-data">
                                <h3>Recaudación Total</h3>
                                <div class="summary-value">{{ number_format($totalRevenue, 2) }}€</div>
                                <div class="summary-breakdown">
                                    <div>
                                        <span class="badge bg-primary">Normal: {{ number_format($totalRegularRevenue, 2) }}€</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">VIP: {{ number_format($totalVipRevenue, 2) }}€</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-film"></i>
                            </div>
                            <div class="summary-data">
                                <h3>Proyecciones</h3>
                                <div class="summary-value">{{ count($screenings) }}</div>
                                <div class="summary-breakdown">
                                    @if(count($screenings) > 0)
                                    <div>
                                        <span class="badge bg-success">
                                            Ocupación media: {{ number_format(collect($screeningStats)->avg('occupancyRate'), 1) }}%
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(count($screenings) == 0)
                <div class="alert alert-info my-4">
                    <i class="fas fa-info-circle me-2"></i> No hay proyecciones programadas para esta fecha.
                </div>
            @else
                <!-- Desglose por proyección -->
                <h3 class="mt-5 mb-4"><i class="fas fa-list me-2"></i>Detalle por Proyección</h3>
                
                <div class="accordion" id="screeningsAccordion">
                    @foreach($screeningStats as $index => $stats)
                    <div class="accordion-item bg-dark text-white mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                <div class="d-flex align-items-center justify-content-between w-100 pe-3">
                                    <div>
                                        <span class="fw-bold">{{ $stats['screening']->movie_title }}</span>
                                        <span class="ms-3 text-muted">
                                            {{ \Carbon\Carbon::parse($stats['screening']->date_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $stats['regularTickets'] + $stats['vipTickets'] }} entradas</span>
                                        <span class="badge bg-success">{{ number_format($stats['totalRevenue'], 2) }}€</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#screeningsAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="seat-map-container">
                                            <h5 class="mb-3">Mapa de Butacas</h5>
                                            <div class="seat-map">
                                                <div class="screen mb-4">Pantalla</div>
                                                <div class="seats-container">
                                                    @php
                                                        // Organizar los asientos por filas
                                                        $seatsByRow = [];
                                                        foreach ($stats['allSeats'] as $seat) {
                                                            $seatsByRow[$seat->row][] = $seat;
                                                        }
                                                        
                                                        // Ordenar por filas
                                                        ksort($seatsByRow);
                                                    @endphp
                                                    
                                                    @foreach($seatsByRow as $row => $seats)
                                                        <div class="seat-row">
                                                    <div class="row-label">{{ $row }}</div>
                                                    @foreach($seats as $seat)
                                                        @php
                                                            $isOccupied = in_array($seat->id, $stats['occupiedSeats']);
                                                            $seatClass = $isOccupied ? 'occupied' : 'available';
                                                            $seatClass .= $seat->is_vip ? ' vip' : '';
                                                        @endphp
                                                        <div class="seat {{ $seatClass }}" title="Fila {{ $seat->row }}, Asiento {{ $seat->number }} {{ $seat->is_vip ? '(VIP)' : '' }}">
                                                            <span>{{ $seat->number }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                    @endforeach
                                                </div>
                                                
                                                <div class="seat-legend mt-3">
                                                    <div class="legend-item">
                                                        <div class="seat available"></div>
                                                        <span>Disponible</span>
                                                    </div>
                                                    <div class="legend-item">
                                                        <div class="seat occupied"></div>
                                                        <span>Ocupado</span>
                                                    </div>
                                                    <div class="legend-item">
                                                        <div class="seat vip available"></div>
                                                        <span>VIP</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="screening-stats mb-4">
                                            <h5 class="mb-3">Estadísticas</h5>
                                            <div class="stats-grid">
                                                <div class="stat-item">
                                                    <div class="stat-label">Ocupación</div>
                                                    <div class="stat-value">{{ number_format($stats['occupancyRate'], 1) }}%</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-label">Entradas Normales</div>
                                                    <div class="stat-value">{{ $stats['regularTickets'] }}</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-label">Entradas VIP</div>
                                                    <div class="stat-value">{{ $stats['vipTickets'] }}</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-label">Recaudación Normal</div>
                                                    <div class="stat-value">{{ number_format($stats['regularRevenue'], 2) }}€</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-label">Recaudación VIP</div>
                                                    <div class="stat-value">{{ number_format($stats['vipRevenue'], 2) }}€</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-label">Recaudación Total</div>
                                                    <div class="stat-value">{{ number_format($stats['totalRevenue'], 2) }}€</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Estilos para las tarjetas de resumen */
    .daily-summary {
        margin: 20px 0 40px;
    }
    
    .summary-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 20px;
        height: 100%;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .summary-card:hover {
        transform: translateY(-5px);
    }
    
    .summary-icon {
        background-color: rgba(0, 160, 228, 0.2);
        color: #00A0E4;
        height: 60px;
        width: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-right: 20px;
    }
    
    .summary-data {
        flex: 1;
    }
    
    .summary-data h3 {
        margin: 0 0 10px;
        font-size: 1rem;
        color: #aaa;
    }
    
    .summary-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: white;
        margin-bottom: 10px;
    }
    
    .summary-breakdown {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    /* Estilos para el mapa de butacas */
    .seat-map-container {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .screen {
        background: linear-gradient(to bottom, rgba(153, 204, 255, 0.8), rgba(102, 178, 255, 0.3));
        padding: 12px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
        color: #fff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        font-size: 1.1rem;
        letter-spacing: 1px;
        margin-bottom: 25px;
    }
    
    .seats-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px 0;
        overflow-x: auto;
        max-width: 100%;
    }
    
    .seat-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 6px;
        margin-bottom: 5px;
        padding: 0 10px;
    }
    
    .row-label {
        min-width: 30px;
        height: 30px;
        text-align: center;
        font-weight: bold;
        color: #fff;
        background-color: rgba(0, 160, 228, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    .seat {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: default;
        transition: all 0.3s ease;
        margin: 3px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .seat span {
        display: inline-block;
        padding: 2px;
    }
    
    .seat.available {
        background-color: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
    
    .seat.occupied {
        background-color: #e74c3c;
        color: white;
        transform: scale(1.05);
    }
    
    .seat.vip {
        border: 3px solid #f39c12;
    }
    
    .seat.vip.occupied {
        background-color: #9b59b6;
    }
    
    .seat:hover {
        transform: scale(1.1);
        z-index: 1;
    }
    
    .seat-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .legend-item .seat {
        width: 20px;
        height: 20px;
        font-size: 0;
    }
    
    /* Estilos para las estadísticas de proyección */
    .screening-stats {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .stat-item {
        padding: 10px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 5px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #aaa;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 1.2rem;
        font-weight: bold;
        color: white;
    }
    
    /* Estilos para el acordeón */
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-button.collapsed::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-button:focus {
        box-shadow: none;
    }
</style>
@endsection