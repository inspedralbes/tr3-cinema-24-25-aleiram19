<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Ticket;
use App\Services\PdfService;

class MailService
{
    protected $pdfService;
    
    /**
     * Constructor del servicio
     *
     * @param PdfService $pdfService
     */
    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    
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
            // Obtener los datos del ticket usando el servicio PdfService para evitar duplicación
            $ticketInfo = $this->pdfService->getTicketData($ticket);
            $ticketData = $ticketInfo['ticketData'];
            $qrCodeBase64 = $ticketInfo['qrCodeBase64'];
            
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
            $mail->addAddress($ticketData['user']['email'], $ticketData['user']['name']);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Tu entrada para ' . $ticketData['movie']['title'];
            
            // Construir el cuerpo del correo - simplificado
            $messageBody = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    h1 { color: #e63946; }
                    .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>¡Gracias por tu compra!</h1>
                    <p>Hola ' . $ticketData['user']['name'] . ',</p>
                    <p>Te confirmamos la compra de tu entrada para ver la película <strong>' . $ticketData['movie']['title'] . '</strong>.</p>
                    
                    <p>Adjuntamos tu entrada en formato PDF con todos los detalles de tu compra. Te recomendamos guardarla o imprimirla para presentarla en el cine.</p>
                    
                    <p>¡Esperamos que disfrutes de la película!</p>
                    
                    <div class="footer">
                        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                        <p>© ' . date('Y') . ' CineXperience. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->Body = $messageBody;
            $mail->AltBody = 'Gracias por tu compra. Tu entrada para ' . $ticketData['movie']['title'] . ' está adjunta en formato PDF.';
            
            // Adjuntar PDF
            $mail->addAttachment($pdfPath, 'entrada_' . $ticketData['ticket_id'] . '.pdf');
            
            // Agregar logs para depuración
            \Log::info('Intentando enviar correo a: ' . $ticketData['user']['email']);
            
            // Enviar correo
            $mail->send();
            
            // Registrar éxito
            \Log::info('Correo enviado con éxito a: ' . $ticketData['user']['email']);
            
            return true;
        } catch (Exception $e) {
            // Registrar error con más detalles
            \Log::error('Error al enviar correo: ' . $e->getMessage());
            \Log::error('Detalles del correo: Host=' . config('mail.mailers.smtp.host') . 
                        ', Port=' . config('mail.mailers.smtp.port') . 
                        ', Username=' . config('mail.mailers.smtp.username'));
            
            return false;
        }
    }
}
