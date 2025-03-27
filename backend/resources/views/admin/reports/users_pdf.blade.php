<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Usuarios: {{ $month }} {{ $year }}</title>
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
        .stats-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            width: 30%;
            text-align: center;
            padding: 15px;
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
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .user-table th, .user-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-table th {
            background-color: #f1faee;
            font-weight: bold;
        }
        .user-table tr:nth-child(even) {
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
            <h1>Informe de Usuarios de CineXperience</h1>
            <h2>{{ $month }} {{ $year }}</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $newUsers }}</div>
                <div class="stat-label">Nuevos Usuarios</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ count($activeUsers->where('tickets_bought', '>', 0)) }}</div>
                <div class="stat-label">Usuarios Activos</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Usuarios</div>
            </div>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th class="text-right">Tickets Comprados</th>
                    <th class="text-right">Total Gastado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeUsers as $user)
                    @if($user->tickets_bought > 0)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-right">{{ $user->tickets_bought }}</td>
                            <td class="text-right">{{ number_format($user->total_spent, 2) }}€</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay datos disponibles para este período</td>
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