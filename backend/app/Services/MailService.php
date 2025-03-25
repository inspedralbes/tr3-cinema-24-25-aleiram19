<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MailService
{
    /**
     * Envía un correo electrónico con un PDF adjunto para un ticket
     *
     * @param Ticket $ticket
     * @param string $pdfPath
     * @return bool
     */
    public function sendTicketEmail(Ticket $ticket, string $pdfPath)
    {
        try {
            // Cargar el ticket con todas sus relaciones
            $ticket->load(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack']);
            
            // Generar QR para incluir en el correo
            $qrCodeData = json_encode([
                'ticket_id' => $ticket->id,
                'movie' => $ticket->screening->movie->title,
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name,
                'seat' => $ticket->seat->number,
                'purchase_date' => $ticket->purchase_date
            ]);
            
            // Generar el código QR como PNG para el correo
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                       ->size(180)
                       ->backgroundColor(245, 245, 245)
                       ->color(29, 53, 87)
                       ->margin(1)
                       ->errorCorrection('H')
                       ->generate($qrCodeData);
                       
            // Convertir a base64 para incluirlo en el HTML
            $qrCodeBase64 = base64_encode($qrCode);
            
            // Crear instancia de PHPMailer
            $mail = new PHPMailer(true);
            
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption');
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Remitente y destinatario
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($ticket->user->email, $ticket->user->name);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Tu entrada para ' . $ticket->screening->movie->title;
            
            // Construir el cuerpo del correo
            $messageBody = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    h1 { color: #e63946; }
                    .ticket-info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                    .movie-title { font-size: 20px; font-weight: bold; color: #1d3557; }
                    .details-row { margin-bottom: 10px; }
                    .label { font-weight: bold; }
                    .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>¡Gracias por tu compra!</h1>
                    <p>Hola ' . $ticket->user->name . ',</p>
                    <p>Te confirmamos la compra de tu entrada para ver la película:</p>
                    
                    <div class="ticket-info">
                        <div class="movie-title">' . $ticket->screening->movie->title . '</div>
                        
                        <div class="details-row">
                            <span class="label">Fecha y hora:</span> ' . $ticket->screening->date_time . '
                        </div>
                        
                        <div class="details-row">
                            <span class="label">Sala:</span> ' . $ticket->screening->auditorium->name . '
                        </div>
                        
                        <div class="details-row">
                            <span class="label">Asiento:</span> ' . $ticket->seat->number . '
                        </div>
                        
                        <div class="details-row">
                            <span class="label">Precio total:</span> ' . $ticket->total_pay . ' €
                        </div>';
            
            // Agregar información de snacks si hay
            if ($ticket->snack) {
                $messageBody .= '
                        <div class="details-row">
                            <span class="label">Snack:</span> ' . $ticket->snack->name . ' (x' . $ticket->snack_quantity . ')
                        </div>';
            }
            
            $messageBody .= '
                    </div>
                    
                    <div style="text-align: center; margin: 20px 0; padding: 10px; background: #f5f5f5;">
                        <p style="font-weight: bold; margin-bottom: 10px;">Código QR de tu entrada</p>
                        <img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="Código QR de la entrada" style="max-width: 180px;">
                        <p style="font-family: monospace; margin-top: 10px;">TICKET-' . $ticket->id . '</p>
                    </div>
                    
                    <p>Adjuntamos también tu entrada en formato PDF. Te recomendamos guardarla o imprimirla para presentarla en el cine.</p>
                    
                    <p>¡Esperamos que disfrutes de la película!</p>
                    
                    <div class="footer">
                        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                        <p>© ' . date('Y') . ' CineXperience. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->Body = $messageBody;
            $mail->AltBody = 'Tu entrada para ' . $ticket->screening->movie->title . ' - Película: ' . $ticket->screening->movie->title . ', Fecha: ' . $ticket->screening->date_time . ', Sala: ' . $ticket->screening->auditorium->name . ', Asiento: ' . $ticket->seat->number;
            
            // Adjuntar PDF
            $mail->addAttachment($pdfPath, 'entrada_' . $ticket->id . '.pdf');
            
            // Enviar correo
            $mail->send();
            
            return true;
        } catch (Exception $e) {
            // Registrar error
            \Log::error('Error al enviar correo: ' . $e->getMessage());
            return false;
        }
    }
}
