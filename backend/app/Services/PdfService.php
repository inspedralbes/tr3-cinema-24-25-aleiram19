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

        // Generar QR code con la información del ticket
        $qrCodeData = json_encode([
            'movie' => $ticket->screening->movie->title,
            'date_time' => $ticket->screening->date_time,
            'auditorium' => $ticket->screening->auditorium->name,
            'seat' => $ticket->seat->number,
            'purchase_date' => $ticket->purchase_date
        ]);
        
        // Generar el código QR como PNG para mejor compatibilidad con DomPDF
        $qrCode = QrCode::format('png')
                       ->size(180)
                       ->backgroundColor(245, 245, 245)
                       ->color(29, 53, 87)
                       ->margin(1)
                       ->errorCorrection('H')
                       ->generate($qrCodeData);
        
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

        // Generar QR code con la información del ticket
        $qrCodeData = json_encode([
            'ticket_id' => $ticket->id,
            'movie' => $ticket->screening->movie->title,
            'date_time' => $ticket->screening->date_time,
            'auditorium' => $ticket->screening->auditorium->name,
            'seat' => $ticket->seat->number,
            'purchase_date' => $ticket->purchase_date
        ]);
        
        // Generar el código QR como PNG
        $qrCode = QrCode::format('png')
                       ->size(180)
                       ->backgroundColor(245, 245, 245)
                       ->color(29, 53, 87)
                       ->margin(1)
                       ->errorCorrection('H')
                       ->generate($qrCodeData);
        
        // Convertir a base64 para incluirlo en el HTML
        $qrCodeBase64 = base64_encode($qrCode);
        
        return [
            'ticketData' => $ticketData,
            'qrCodeBase64' => $qrCodeBase64,
            'qrCodeData' => $qrCodeData
        ];
    }
}
