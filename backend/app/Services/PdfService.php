<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfService
{
    /**
     * Genera un PDF para un ticket
     *
     * @param Ticket $ticket
     * @return string Ruta del archivo PDF generado
     */
    public function generateTicketPdf(Ticket $ticket)
    {
        // Cargar el ticket con todas sus relaciones
        $ticket->load(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack']);

        // Preparar datos para la vista PDF
        $ticketData = [
            'ticket_id' => $ticket->id,
            'purchase_date' => $ticket->purchase_date,
            'movie' => [
                'title' => $ticket->screening->movie->title,
                'duration' => $ticket->screening->movie->duration,
                'classification' => $ticket->screening->movie->classification
            ],
            'screening' => [
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name
            ],
            'seat' => [
                'number' => $ticket->seat->number,
                'is_vip' => $ticket->seat->isVip()
            ],
            'user' => [
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'total_price' => $ticket->total_pay
        ];

        // Verificar si existe el código QR genérico
        $qrPath = storage_path('app/public/qrcodes/ticket_qr.png');
        
        // Si no existe, generarlo y guardarlo
        if (!file_exists($qrPath)) {
            // Crear el directorio si no existe
            $qrDir = dirname($qrPath);
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            
            // Crear un código QR genérico con formato más moderno
            $qrCodeData = json_encode([
                'cinema' => 'CineXperience',
                'app' => 'Ticket Validator',
                'timestamp' => time()
            ]);
            
            // Generar el código QR como PNG con diseño moderno en negro
            $qrCode = QrCode::format('png')
                           ->size(200)
                           ->backgroundColor(255, 255, 255)
                           ->color(0, 0, 0)
                           ->margin(1)
                           ->errorCorrection('H')
                           ->generate($qrCodeData);
            
            // Guardar el archivo
            file_put_contents($qrPath, $qrCode);
        } else {
            // Leer el código QR existente
            $qrCode = file_get_contents($qrPath);
        }
        
        // Convertir a base64 para incluirlo en el HTML
        $qrCodeBase64 = base64_encode($qrCode);
        
        // Generar PDF usando la vista consolidada
        try {
            // Usar new Pdf directamente en lugar de la fachada
            $pdf = PDF::loadView('pdfs.ticket_consolidated', [
                'ticketData' => $ticketData,
                'qrCodeBase64' => $qrCodeBase64
            ]);
            
            // Log para depuración
            \Log::info('PDF generado correctamente para el ticket: ' . $ticket->id);
        } catch (\Exception $e) {
            \Log::error('Error al generar PDF: ' . $e->getMessage());
            \Log::error('Datos enviados a la vista: ' . json_encode(['ticketData' => $ticketData], JSON_PRETTY_PRINT));
            throw $e;
        }
        
        // Guardar PDF en storage
        $pdfPath = storage_path('app/public/tickets/ticket_' . $ticket->id . '.pdf');
        $pdfDirectory = dirname($pdfPath);
        
        // Asegurarnos de que el directorio existe
        if (!is_dir($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }
        
        // Guardar el PDF
        $pdf->save($pdfPath);
        
        return $pdfPath;
    }
    
    /**
     * Obtiene los datos del ticket para reuso en otros servicios
     * 
     * @param Ticket $ticket
     * @return array Datos del ticket y código QR en base64
     */
    public function getTicketData(Ticket $ticket)
    {
        // Cargar el ticket con todas sus relaciones si no están ya cargadas
        if (!$ticket->relationLoaded('screening.movie') || 
            !$ticket->relationLoaded('screening.auditorium') || 
            !$ticket->relationLoaded('seat') || 
            !$ticket->relationLoaded('user') || 
            ($ticket->snack_id && !$ticket->relationLoaded('snack'))) {
            $ticket->load(['screening.movie', 'screening.auditorium', 'seat', 'user', 'snack']);
        }

        // Preparar datos para la vista PDF
        $ticketData = [
            'ticket_id' => $ticket->id,
            'purchase_date' => $ticket->purchase_date,
            'movie' => [
                'title' => $ticket->screening->movie->title,
                'duration' => $ticket->screening->movie->duration,
                'classification' => $ticket->screening->movie->classification
            ],
            'screening' => [
                'date_time' => $ticket->screening->date_time,
                'auditorium' => $ticket->screening->auditorium->name
            ],
            'seat' => [
                'number' => $ticket->seat->number,
                'is_vip' => $ticket->seat->isVip()
            ],
            'user' => [
                'name' => $ticket->user->name,
                'email' => $ticket->user->email
            ],
            'snack' => $ticket->snack ? [
                'name' => $ticket->snack->name,
                'quantity' => $ticket->snack_quantity,
                'price' => $ticket->snack->price * $ticket->snack_quantity
            ] : null,
            'total_price' => $ticket->total_pay
        ];

        // Verificar si existe el código QR genérico
        $qrPath = storage_path('app/public/qrcodes/ticket_qr.png');
        
        // Si no existe, generarlo ahora
        if (!file_exists($qrPath)) {
            // Crear el directorio si no existe
            $qrDir = dirname($qrPath);
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            
            // Crear un código QR genérico con formato más moderno
            $qrCodeData = json_encode([
                'cinema' => 'CineXperience',
                'app' => 'Ticket Validator',
                'timestamp' => time()
            ]);
            
            // Generar el código QR como PNG con diseño moderno en negro
            $qrCode = QrCode::format('png')
                           ->size(200)
                           ->backgroundColor(255, 255, 255)
                           ->color(0, 0, 0)
                           ->margin(1)
                           ->errorCorrection('H')
                           ->generate($qrCodeData);
            
            // Guardar el archivo
            file_put_contents($qrPath, $qrCode);
        } else {
            // Leer el código QR existente
            $qrCode = file_get_contents($qrPath);
        }
        
        // Convertir a base64 para incluirlo en el HTML
        $qrCodeBase64 = base64_encode($qrCode);
        
        // Usar un identificador único para este ticket
        $qrCodeData = 'TICKET-' . $ticket->id;
        
        return [
            'ticketData' => $ticketData,
            'qrCodeBase64' => $qrCodeBase64,
            'qrCodeData' => $qrCodeData
        ];
    }
}
