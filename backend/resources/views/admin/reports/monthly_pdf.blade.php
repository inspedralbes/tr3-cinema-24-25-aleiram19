<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe Mensual: {{ $month }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1d3557;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1d3557;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #1d3557;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .stat-box {
            width: 23%;
            text-align: center;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1d3557;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f1faee;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Informe Mensual de CineXperience</h1>
            <h2>{{ $month }} {{ $year }}</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $totalMoviesSold }}</div>
                <div class="stat-label">Tickets Vendidos</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $seatStats->vip_seats + $seatStats->regular_seats }}</div>
                <div class="stat-label">Asientos Ocupados</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Usuarios Registrados</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($totalRevenue, 2) }}€</div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
        </div>
        
        <!-- Películas Vendidas -->
        <div class="section">
            <div class="section-title">Películas Más Vendidas</div>
            <table>
                <thead>
                    <tr>
                        <th>Película</th>
                        <th class="text-right">Tickets</th>
                        <th class="text-right">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movieStats as $movie)
                        <tr>
                            <td>{{ $movie->title }}</td>
                            <td class="text-right">{{ $movie->tickets_sold }}</td>
                            <td class="text-right">
                                {{ round(($movie->tickets_sold / $totalMoviesSold) * 100, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center;">No hay datos disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Estadísticas de Horarios -->
        <div class="section">
            <div class="section-title">Ventas por Horarios</div>
            <table>
                <thead>
                    <tr>
                        <th>Franja Horaria</th>
                        <th class="text-right">Tickets</th>
                        <th class="text-right">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timeStats as $time)
                        <tr>
                            <td>{{ $time->time_period }}</td>
                            <td class="text-right">{{ $time->count }}</td>
                            <td class="text-right">
                                {{ round(($time->count / $totalMoviesSold) * 100, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center;">No hay datos disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Estadísticas de Asientos -->
        <div class="section">
            <div class="section-title">Distribución de Asientos</div>
            <table>
                <thead>
                    <tr>
                        <th>Tipo de Asiento</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>VIP</td>
                        <td class="text-right">{{ $seatStats->vip_seats }}</td>
                        <td class="text-right">
                            {{ round(($seatStats->vip_seats / ($seatStats->vip_seats + $seatStats->regular_seats)) * 100, 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Regular</td>
                        <td class="text-right">{{ $seatStats->regular_seats }}</td>
                        <td class="text-right">
                            {{ round(($seatStats->regular_seats / ($seatStats->vip_seats + $seatStats->regular_seats)) * 100, 1) }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Estadísticas de Usuarios -->
        <div class="section">
            <div class="section-title">Usuarios</div>
            <table>
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nuevos usuarios registrados</td>
                        <td class="text-right">{{ $newUsers }}</td>
                    </tr>
                    <tr>
                        <td>Usuarios activos (compraron tickets)</td>
                        <td class="text-right">{{ $activeUsers }}</td>
                    </tr>
                    <tr>
                        <td>Total de usuarios registrados</td>
                        <td class="text-right">{{ $totalUsers }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>Este informe fue generado automáticamente por el sistema de CineXperience el {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>