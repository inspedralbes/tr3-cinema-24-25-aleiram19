<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Películas: {{ $month }} {{ $year }}</title>
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
        .movie-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .movie-table th, .movie-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .movie-table th {
            background-color: #f1faee;
            font-weight: bold;
        }
        .movie-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .classification {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #1d3557;
            color: white;
            font-size: 12px;
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
            <h1>Informe de Películas de CineXperience</h1>
            <h2>{{ $month }} {{ $year }}</h2>
        </div>
        
        <table class="movie-table">
            <thead>
                <tr>
                    <th>Película</th>
                    <th>Duración</th>
                    <th>Clasificación</th>
                    <th class="text-right">Tickets Vendidos</th>
                    <th class="text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movies as $movie)
                    <tr>
                        <td>
                            <strong>{{ $movie->title }}</strong>
                            <div>{{ $movie->director }}</div>
                        </td>
                        <td>{{ $movie->duration }} min</td>
                        <td>
                            <span class="classification">{{ $movie->classification }}</span>
                        </td>
                        <td class="text-right">
                            {{ $movie->tickets_sold ?? 0 }}
                        </td>
                        <td class="text-right">
                            {{ number_format($movie->revenue ?? 0, 2) }}€
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay datos disponibles para este período</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="footer">
            <p>Este informe fue generado automáticamente por el sistema de CineXperience el {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>