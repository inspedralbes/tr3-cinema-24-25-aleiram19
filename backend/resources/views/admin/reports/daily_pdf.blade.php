<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe Diario - {{ $date }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        h1 {
            color: #051D40;
            border-bottom: 2px solid #00A0E4;
            padding-bottom: 10px;
            font-size: 24px;
        }
        
        h2 {
            color: #051D40;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        
        .summary-container {
            display: flex;
            margin-bottom: 30px;
        }
        
        .summary-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-right: 15px;
            width: 30%;
            background-color: #f8f9fa;
        }
        
        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #051D40;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #00A0E4;
        }
        
        .summary-detail {
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 5px;
        }
        
        .badge-primary {
            background-color: #00A0E4;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
        }
        
        td {
            padding: 8px;
        }
        
        .screening-title {
            font-weight: bold;
            color: #051D40;
        }
        
        .screening-time {
            color: #666;
        }
        
        .seat-grid {
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
            background-color: #f8f9fa;
        }
        
        .seat-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .seat {
            width: 25px;
            height: 25px;
            margin-right: 5px;
            text-align: center;
            line-height: 25px;
            font-size: 12px;
            border-radius: 3px;
        }
        
        .seat-available {
            background-color: #e9ecef;
            color: #666;
            border: 1px solid #ddd;
        }
        
        .seat-occupied {
            background-color: #dc3545;
            color: white;
            border: 1px solid #c82333;
        }
        
        .seat-vip {
            border: 2px solid #f39c12;
        }
        
        .seat-vip.seat-occupied {
            background-color: #9b59b6;
            border: 2px solid #8e44ad;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h1>Informe Diario: {{ $date }}</h1>
    
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">Total Entradas</div>
            <div class="summary-value">{{ $totalTickets }}</div>
            <div class="summary-detail">
                <span class="badge badge-primary">Normal: {{ $totalRegularTickets }}</span>
                <span class="badge badge-info">VIP: {{ $totalVipTickets }}</span>
            </div>
        </div>
        
        <div class="summary-box">
            <div class="summary-title">Recaudación Total</div>
            <div class="summary-value">{{ number_format($totalRevenue, 2) }}€</div>
            <div class="summary-detail">
                <span class="badge badge-primary">Normal: {{ number_format($totalRegularRevenue, 2) }}€</span>
                <span class="badge badge-info">VIP: {{ number_format($totalVipRevenue, 2) }}€</span>
            </div>
        </div>
        
        <div class="summary-box">
            <div class="summary-title">Proyecciones</div>
            <div class="summary-value">{{ count($screenings) }}</div>
            @if(count($screenings) > 0)
            <div class="summary-detail">
                Ocupación media: {{ number_format(collect($screeningStats)->avg('occupancyRate'), 1) }}%
            </div>
            @endif
        </div>
    </div>
    
    @if(count($screenings) == 0)
        <p>No hay proyecciones programadas para esta fecha.</p>
    @else
        <h2>Detalle por Proyección</h2>
        
        @foreach($screeningStats as $index => $stats)
            @if($index > 0)
                <div class="page-break"></div>
            @endif
            
            <table>
                <tr>
                    <th colspan="2">
                        <span class="screening-title">{{ $stats['screening']->movie_title }}</span>
                        <span class="screening-time">
                            ({{ \Carbon\Carbon::parse($stats['screening']->date_time)->format('H:i') }})
                        </span>
                    </th>
                </tr>
                <tr>
                    <td width="50%">
                        <table>
                            <tr>
                                <th colspan="2">Estadísticas</th>
                            </tr>
                            <tr>
                                <td>Ocupación</td>
                                <td>{{ number_format($stats['occupancyRate'], 1) }}%</td>
                            </tr>
                            <tr>
                                <td>Entradas Normales</td>
                                <td>{{ $stats['regularTickets'] }}</td>
                            </tr>
                            <tr>
                                <td>Entradas VIP</td>
                                <td>{{ $stats['vipTickets'] }}</td>
                            </tr>
                            <tr>
                                <td>Recaudación Normal</td>
                                <td>{{ number_format($stats['regularRevenue'], 2) }}€</td>
                            </tr>
                            <tr>
                                <td>Recaudación VIP</td>
                                <td>{{ number_format($stats['vipRevenue'], 2) }}€</td>
                            </tr>
                            <tr>
                                <td>Recaudación Total</td>
                                <td>{{ number_format($stats['totalRevenue'], 2) }}€</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <div>
                            <h3>Mapa de Butacas</h3>
                            <div style="text-align: center; background-color: #eee; padding: 5px; margin-bottom: 10px;">
                                PANTALLA
                            </div>
                            <div class="seat-grid">
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
                                        <div style="width: 20px; text-align: center; font-weight: bold;">{{ $row }}</div>
                                        @foreach($seats as $seat)
                                            @php
                                                $isOccupied = in_array($seat->id, $stats['occupiedSeats']);
                                                $seatClass = $isOccupied ? 'seat-occupied' : 'seat-available';
                                                $seatClass .= $seat->is_vip ? ' seat-vip' : '';
                                            @endphp
                                            <div class="seat {{ $seatClass }}">
                                                {{ $seat->number }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            
                            <div style="margin-top: 10px; display: flex; justify-content: center;">
                                <div style="margin-right: 15px;">
                                    <span class="seat seat-available" style="display: inline-block;"></span> Disponible
                                </div>
                                <div style="margin-right: 15px;">
                                    <span class="seat seat-occupied" style="display: inline-block;"></span> Ocupado
                                </div>
                                <div>
                                    <span class="seat seat-vip seat-available" style="display: inline-block;"></span> VIP
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        @endforeach
    @endif
    
    <footer>
        <p>© {{ date('Y') }} CineXperience Management System - Informe generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </footer>
</body>
</html>