@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
    <div class="container">
        <!-- Bienvenida y Estadísticas Principales -->
        <div class="welcome-stats">
            <div class="welcome-message">
                <h1>¡Bienvenido {{ Auth::user()->name }}! <i class="fas fa-crown text-warning"></i></h1>
                <p class="text-light">Resumen de rendimiento del sistema de cine</p>
                
                <!-- Estadísticas Principales -->
                <div class="stats-cards">
                    <div class="stat-card sales">
                        <div class="stat-icon">
                            <i class="fas fa-ticket"></i>
                        </div>
                        <div class="stat-data">
                            <h3>€{{ number_format($stats['monthly_sales'], 2) }}</h3>
                            <p>Ventas este mes</p>
                        </div>
                    </div>
                    <div class="stat-card tickets">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-data">
                            <h3>{{ $stats['total_tickets'] }}</h3>
                            <p>Tickets vendidos</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mini gráfico de ventas -->
            <div class="sales-chart">
                <div class="chart-header">
                    <h3>Ventas últimos 7 días</h3>
                </div>
                <div class="chart-visual">
                    <div class="bar-chart">
                        @php
                            // Encontrar el valor máximo para calcular porcentajes
                            $maxAmount = max(array_column($dailySales, 'amount')) ?: 1;
                        @endphp
                        
                        @foreach($dailySales as $day)
                            @php
                                $height = ($day['amount'] / $maxAmount) * 100;
                                $isWeekend = in_array($day['day'], ['Sat', 'Sun']) ? 'weekend' : '';
                            @endphp
                            <div class="bar {{ $isWeekend }}" 
                                 style="height: {{ $height }}%;" 
                                 data-tooltip="{{ __($day['day']) }}: €{{ number_format($day['amount'], 2) }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas en Cards -->
        <div class="metrics-container">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-film"></i></div>
                <div class="metric-value">{{ $stats['active_movies'] }}</div>
                <div class="metric-label">Películas activas</div>
            </div>
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-calendar"></i></div>
                <div class="metric-value">{{ $stats['scheduled_screenings'] }}</div>
                <div class="metric-label">Sesiones programadas</div>
            </div>
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-user-plus"></i></div>
                <div class="metric-value">{{ $stats['registered_users'] }}</div>
                <div class="metric-label">Usuarios registrados</div>
            </div>
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-ticket-alt"></i></div>
                <div class="metric-value">{{ $stats['week_tickets'] }}</div>
                <div class="metric-label">Tickets esta semana</div>
            </div>
        </div>

        <!-- Accesos rápidos mejorados -->
        <h2 class="section-title">Gestión rápida</h2>
        <div class="cards-container">
            <a href="{{ route('users.index') }}" class="content-link user">
                <i class="fas fa-users"></i>
                <span class="link-title">Usuarios</span>
                <span class="link-count">{{ $stats['registered_users'] }} registrados</span>
            </a>
            <a href="{{ route('movies.index') }}" class="content-link movie">
                <i class="fas fa-film"></i>
                <span class="link-title">Películas</span>
                <span class="link-count">{{ $stats['active_movies'] }} en cartelera</span>
            </a>
            <a href="{{ route('seats.index') }}" class="content-link seat">
                <i class="fas fa-chair"></i>
                <span class="link-title">Asientos</span>
                <span class="link-count">{{ App\Models\Seat::count() }} configurados</span>
            </a>
            <a href="{{ route('screenings.index') }}" class="content-link screening">
                <i class="fas fa-video"></i>
                <span class="link-title">Sesiones</span>
                <span class="link-count">{{ $stats['scheduled_screenings'] }} programadas</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="content-link report">
                <i class="fas fa-chart-line"></i>
                <span class="link-title">Informes</span>
                <span class="link-count">Ventas: €{{ number_format($stats['weekly_sales'], 2) }} esta semana</span>
            </a>
        </div>
    </div>

    <style>
        .welcome-stats {
            display: flex;
            background-color: rgba(0, 120, 200, 0.1);
            border-radius: 15px;
            margin-bottom: 30px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .welcome-message {
            flex: 1;
            padding-right: 20px;
        }
        
        .welcome-message h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-cards {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .stat-card {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
            width: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .stat-icon {
            height: 50px;
            width: 50px;
            background-color: rgba(0, 160, 228, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .stat-icon i {
            font-size: 1.5rem;
            color: #00A0E4;
        }
        
        .stat-data h3 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: bold;
            color: #ffffff;
        }
        
        .stat-data p {
            margin: 0;
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .sales-chart {
            width: 300px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .chart-header {
            margin-bottom: 15px;
        }
        
        .chart-header h3 {
            font-size: 1.2rem;
            margin: 0;
            color: #ffffff;
        }
        
        .chart-visual {
            height: 150px;
            display: flex;
            align-items: flex-end;
        }
        
        .bar-chart {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
            height: 100%;
        }
        
        .bar {
            width: 25px;
            background-color: #00A0E4;
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: all 0.3s;
        }
        
        .bar.weekend {
            background-color: #0078C8;
        }
        
        .bar:hover {
            transform: scaleY(1.05);
            background-color: #00BFFF;
            box-shadow: 0 0 10px rgba(0, 160, 228, 0.6);
        }
        
        .bar:hover::before {
            content: attr(data-tooltip);
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            z-index: 10;
        }
        
        .metrics-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .metric-card {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        
        .metric-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            width: 40px;
            background-color: rgba(0, 160, 228, 0.2);
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .metric-icon i {
            color: #00A0E4;
            font-size: 1.2rem;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }
        
        .metric-label {
            color: #aaa;
            font-size: 0.9rem;
        }
        
        .section-title {
            margin: 30px 0 20px;
            color: #ffffff;
            font-size: 1.5rem;
            border-left: 4px solid #00A0E4;
            padding-left: 10px;
        }
        
        /* Mejorar los enlaces de contenido */
        .content-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: rgba(5, 29, 64, 0.7);
            padding: 30px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--color-text);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            transition: all var(--transition-speed);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .content-link .link-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin: 10px 0 5px;
        }
        
        .content-link .link-count {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        @media (max-width: 992px) {
            .welcome-stats {
                flex-direction: column;
            }
            
            .welcome-message {
                padding-right: 0;
                padding-bottom: 20px;
            }
            
            .sales-chart {
                width: 100%;
            }
            
            .metrics-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .stats-cards {
                flex-direction: column;
            }
            
            .stat-card {
                width: 100%;
            }
        }
    </style>
@endsection
