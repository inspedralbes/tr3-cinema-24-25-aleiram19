<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Entrada de Cine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }
        .ticket-header {
            background-color: #1d3557;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .ticket-body {
            padding: 20px;
        }
        .movie-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #e63946;
        }
        .ticket-details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
        }
        .ticket-footer {
            background-color: #f1faee;
            padding: 15px;
            text-align: center;
            font-size: 12px;
        }
        .qrcode {
            text-align: center;
            margin: 20px auto;
            padding: 15px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #eee;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 230px;
            border-radius: 10px;
        }
        .qrcode img {
            max-width: 200px;
            margin-bottom: 10px;
        }
        .qrcode-text {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #000;
            background: #f9f9f9;
            padding: 4px 12px;
            border-radius: 4px;
        }
    </style>
    @yield('additional_styles')
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>CineXperience</h1>
        </div>
        
        <div class="ticket-body">   
            <div class="ticket-details">                
                <div class="detail-row">
                    <div class="detail-label">Fecha de Compra:</div>
                    <div>{{ $ticketData['purchase_date'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Película:</div>
                    <div>{{ $ticketData['movie']['title'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Duración:</div>
                    <div>{{ $ticketData['movie']['duration'] ?? 'N/A' }} min</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Clasificación:</div>
                    <div>{{ $ticketData['movie']['classification'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Fecha y Hora:</div>
                    <div>{{ $ticketData['screening']['date_time'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Sala:</div>
                    <div>{{ $ticketData['screening']['auditorium'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Asiento:</div>
                    <div>{{ $ticketData['seat']['number'] ?? 'N/A' }} {{ isset($ticketData['seat']['is_vip']) && $ticketData['seat']['is_vip'] ? '(VIP)' : '' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Cliente:</div>
                    <div>{{ $ticketData['user']['name'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Precio Total:</div>
                    <div><strong>{{ $ticketData['total_price'] ?? 'N/A' }}€</strong></div>
                </div>
            </div>
            
            @yield('qrcode_section')
        </div>
        
        <div class="ticket-footer">
            <p>Por favor, llega con al menos 15 minutos de antelación. Esta entrada es válida únicamente para la sesión indicada.</p>
            <p>© {{ date('Y') }} CineXperience. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
