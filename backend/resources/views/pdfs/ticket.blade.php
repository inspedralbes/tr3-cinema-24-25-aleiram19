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
        .barcode {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f5f5f5;
        }
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>CineXperience</h1>
        </div>
        
        <div class="ticket-body">
            <div class="movie-title">
                {{ $ticketData['movie']['title'] }}
            </div>
            
            <div class="ticket-details">
                <div class="detail-row">
                    <div class="detail-label">ID de Entrada:</div>
                    <div>{{ $ticketData['ticket_id'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Fecha de Compra:</div>
                    <div>{{ $ticketData['purchase_date'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Película:</div>
                    <div>{{ $ticketData['movie']['title'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Duración:</div>
                    <div>{{ $ticketData['movie']['duration'] }} min</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Clasificación:</div>
                    <div>{{ $ticketData['movie']['classification'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Fecha y Hora:</div>
                    <div>{{ $ticketData['screening']['date_time'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Sala:</div>
                    <div>{{ $ticketData['screening']['auditorium'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Asiento:</div>
                    <div>{{ $ticketData['seat']['number'] }} {{ $ticketData['seat']['is_vip'] ? '(VIP)' : '' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Cliente:</div>
                    <div>{{ $ticketData['user']['name'] }}</div>
                </div>
                
                @if($ticketData['snack'])
                <div class="detail-row">
                    <div class="detail-label">Snack:</div>
                    <div>{{ $ticketData['snack']['name'] }} (x{{ $ticketData['snack']['quantity'] }}) - {{ $ticketData['snack']['price'] }}€</div>
                </div>
                @endif
                
                <div class="detail-row">
                    <div class="detail-label">Precio Total:</div>
                    <div><strong>{{ $ticketData['total_price'] }}€</strong></div>
                </div>
            </div>
            
            <div class="barcode">
                <div class="barcode-text">*TICKET{{ $ticketData['ticket_id'] }}*</div>
            </div>
        </div>
        
        <div class="ticket-footer">
            <p>Por favor, llega con al menos 15 minutos de antelación. Esta entrada es válida únicamente para la sesión indicada.</p>
            <p>© {{ date('Y') }} CineXperience. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
