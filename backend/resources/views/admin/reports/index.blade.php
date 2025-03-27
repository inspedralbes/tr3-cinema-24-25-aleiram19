@extends('layouts.app')

@section('title', 'Informes - Panel de Administración')

@section('content')
<div class="container">
    <!-- Panel de estadísticas rápidas -->
    <div class="quick-stats">
        <div class="stat-item">
            <div class="stat-value">€{{ number_format($stats['monthly_revenue'], 2) }}</div>
            <div class="stat-label">Ingresos este mes</div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> 
                @php
                    $lastMonthRevenue = \App\Models\Ticket::whereMonth('created_at', now()->subMonth()->month)
                        ->whereYear('created_at', now()->subMonth()->year)
                        ->sum('total_pay');
                    
                    $percentChange = $lastMonthRevenue > 0 
                        ? (($stats['monthly_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                        : 100;
                    
                    $trendClass = $percentChange >= 0 ? 'up' : 'down';
                    $percentChange = abs($percentChange);
                @endphp
                {{ number_format($percentChange, 1) }}%
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['tickets_sold'] }}</div>
            <div class="stat-label">Tickets vendidos</div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> {{ rand(5, 15) }}.{{ rand(0, 9) }}%
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['avg_occupancy'] }}%</div>
            <div class="stat-label">Ocupación media</div>
            <div class="stat-trend down">
                <i class="fas fa-arrow-down"></i> {{ rand(1, 5) }}.{{ rand(0, 9) }}%
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['new_users'] }}</div>
            <div class="stat-label">Nuevos usuarios</div>
            <div class="stat-trend up">
                <i class="fas fa-arrow-up"></i> {{ rand(10, 20) }}.{{ rand(0, 9) }}%
            </div>
        </div>
    </div>

    <!-- Gráfico de tendencias -->
    <div class="trend-chart mb-5 pb-3">
        <div class="chart-header">
            <h3>Tendencias de ventas (últimos 7 días)</h3>
        </div>
        <div class="chart-container">
            <div class="chart-labels">
                <div class="y-axis">
                    <div>€1500</div>
                    <div>€1200</div>
                    <div>€900</div>
                    <div>€600</div>
                    <div>€300</div>
                    <div>€0</div>
                </div>
            </div>
            <div class="line-chart">
                <svg height="300" width="100%" viewBox="0 0 700 300" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">
                    <!-- Líneas de la cuadrícula -->
                    <line x1="50" y1="50" x2="650" y2="50" stroke="#444" stroke-width="1" />
                    <line x1="50" y1="100" x2="650" y2="100" stroke="#444" stroke-width="1" />
                    <line x1="50" y1="150" x2="650" y2="150" stroke="#444" stroke-width="1" />
                    <line x1="50" y1="200" x2="650" y2="200" stroke="#444" stroke-width="1" />
                    <line x1="50" y1="250" x2="650" y2="250" stroke="#444" stroke-width="1" />
                    
                    @php
                        // Calcular puntos para el gráfico de línea
                        $maxAmount = max(array_column($lastWeekSales, 'amount')) ?: 1;
                        $points = [];
                        $xPositions = [50, 150, 250, 350, 450, 550, 650];
                        
                        foreach ($xPositions as $index => $x) {
                            $amount = $lastWeekSales[$index]['amount'];
                            // Invertir la escala Y (250 es la línea base, 50 es el máximo)
                            $yPosition = $amount > 0 
                                ? 250 - (($amount / $maxAmount) * 200) 
                                : 250;
                            $points[] = "$x,$yPosition";
                        }
                        
                        $pointsString = implode(' ', $points);
                    @endphp
                    
                    <!-- Línea de ventas -->
                    <polyline
                        points="{{ $pointsString }}"
                        fill="none"
                        stroke="#00A0E4"
                        stroke-width="3"
                    />
                    
                    <!-- Puntos de datos -->
                    @foreach ($points as $index => $point)
                        @php
                            list($x, $y) = explode(',', $point);
                        @endphp
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#00A0E4" />
                    @endforeach
                    
                    <!-- Etiquetas del eje X -->
                    @php
                        $daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                        $today = now()->dayOfWeek;
                        $orderedDays = [];
                        
                        // Reorganizar los días para mostrar los últimos 7 días en orden
                        for ($i = 6; $i >= 0; $i--) {
                            $dayIndex = (($today - $i) % 7 + 7) % 7;
                            $orderedDays[] = $daysOfWeek[$dayIndex === 0 ? 6 : $dayIndex - 1];
                        }
                    @endphp
                    
                    @foreach ($xPositions as $index => $x)
                        <text x="{{ $x }}" y="280" fill="#aaa" text-anchor="middle">{{ $orderedDays[$index] }}</text>
                    @endforeach
                </svg>
            </div>
        </div>
    </div>

    <!-- Selector de informes mejorado -->
    <div class="reports-selector mt-5 pt-4">
        <h2 class="section-title">Generador de Informes</h2>
        
        <div class="reports-grid">
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3>Informe Diario Detallado</h3>
                <p>Mapa de butacas y estadísticas detalladas de ventas por día específico.</p>
                
                <form action="{{ route('admin.reports.daily') }}" method="GET">
                    <div class="date-selector-daily">
                        <div class="select-wrapper full-width">
                            <input type="date" name="date" class="styled-date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="generate-btn">
                            <i class="fas fa-chart-line me-1"></i> Ver Informe
                        </button>
                        <button type="submit" name="download_pdf" value="1" class="download-pdf-btn">
                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-file-chart-line"></i>
                </div>
                <h3>Informe General Mensual</h3>
                <p>Estadísticas completas sobre películas, asientos, horarios y usuarios.</p>
                
                <form action="{{ route('admin.reports.monthly') }}" method="GET">
                    <div class="date-selectors">
                        <div class="select-wrapper">
                            <select name="month" class="styled-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <select name="year" class="styled-select">
                                @for ($i = now()->year; $i >= now()->year - 2; $i--)
                                    <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="generate-btn">
                            <i class="fas fa-chart-line me-1"></i> Ver Informe
                        </button>
                        <button type="submit" name="download_pdf" value="1" class="download-pdf-btn">
                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-film"></i>
                </div>
                <h3>Informe de Películas</h3>
                <p>Análisis detallado de películas más populares, tickets e ingresos.</p>
                
                <form action="{{ route('admin.reports.movies') }}" method="GET">
                    <div class="date-selectors">
                        <div class="select-wrapper">
                            <select name="month" class="styled-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <select name="year" class="styled-select">
                                @for ($i = now()->year; $i >= now()->year - 2; $i--)
                                    <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="generate-btn">
                            <i class="fas fa-chart-line me-1"></i> Ver Informe
                        </button>
                        <button type="submit" name="download_pdf" value="1" class="download-pdf-btn">
                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Informe de Usuarios</h3>
                <p>Análisis de usuarios activos, tickets comprados y gastos totales.</p>
                
                <form action="{{ route('admin.reports.users') }}" method="GET">
                    <div class="date-selectors">
                        <div class="select-wrapper">
                            <select name="month" class="styled-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <select name="year" class="styled-select">
                                @for ($i = now()->year; $i >= now()->year - 2; $i--)
                                    <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="generate-btn">
                            <i class="fas fa-chart-line me-1"></i> Ver Informe
                        </button>
                        <button type="submit" name="download_pdf" value="1" class="download-pdf-btn">
                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Panel de estadísticas rápidas */
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-item {
        background-color: rgba(0, 120, 200, 0.1);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
    }
    
    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    
    .stat-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #00A0E4;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #aaa;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .stat-trend {
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .stat-trend.up {
        color: #2ecc71;
    }
    
    .stat-trend.down {
        color: #e74c3c;
    }
    
    /* Gráfico de tendencias */
    .trend-chart {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .chart-header {
        margin-bottom: 20px;
    }
    
    .chart-header h3 {
        color: #ffffff;
        font-size: 1.3rem;
        margin: 0;
    }
    
    .chart-container {
        display: flex;
        height: 350px;
        margin-top: 20px;
    }
    
    .chart-labels {
        width: 50px;
    }
    
    .y-axis {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .y-axis div {
        color: #aaa;
        font-size: 0.8rem;
        text-align: right;
        padding-right: 10px;
    }
    
    .line-chart {
        flex: 1;
    }
    
    /* Selector de informes */
    .reports-selector {
        margin-top: 40px;
    }
    
    .section-title {
        color: #ffffff;
        font-size: 1.5rem;
        margin-bottom: 20px;
        border-left: 4px solid #00A0E4;
        padding-left: 10px;
    }
    
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
    
    .report-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        background-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    
    .report-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 50px;
        width: 50px;
        background-color: rgba(0, 160, 228, 0.2);
        border-radius: 12px;
        margin-bottom: 15px;
    }
    
    .report-icon i {
        color: #00A0E4;
        font-size: 1.5rem;
    }
    
    .report-card h3 {
        color: #ffffff;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    
    .report-card p {
        color: #aaa;
        font-size: 0.9rem;
        margin-bottom: 20px;
        min-height: 40px;
    }
    
    .date-selectors {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .select-wrapper {
        position: relative;
        flex: 1;
    }
    
    .full-width {
        width: 100%;
    }
    
    .styled-date {
        width: 100%;
        padding: 8px 12px;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #ffffff;
        outline: none;
    }
    
    .date-selector-daily {
        margin-bottom: 15px;
    }
    
    .select-wrapper::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Pro';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        pointer-events: none;
    }
    
    .styled-select {
        width: 100%;
        padding: 8px 12px;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #ffffff;
        appearance: none;
        outline: none;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .generate-btn {
        flex: 1;
        padding: 12px;
        background-color: #00A0E4;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .generate-btn:hover {
        background-color: #0078C8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .download-pdf-btn {
        flex: 1;
        padding: 12px;
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .download-pdf-btn:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .download-pdf-btn i {
        font-size: 1.2rem;
        margin-right: 8px;
    }
    
    @media (max-width: 992px) {
        .quick-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .reports-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .quick-stats {
            grid-template-columns: 1fr;
        }
        
        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection