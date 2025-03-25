<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class PdfServiceAlternative
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
        
        // Nombre del archivo QR
        $qrFileName = 'qr_' . $ticket->id . '.png';
        $qrPath = 'qrcodes/' . $qrFileName;
        $qrFullPath = storage_path('app/public/' . $qrPath);
        
        // Generar el código QR como PNG y guardarlo como archivo
        QrCode::format('png')
             ->size(180)
             ->backgroundColor(245, 245, 245)
             ->color(29, 53, 87)
             ->margin(1)
             ->errorCorrection('H')
             ->generate($qrCodeData, $qrFullPath);
        
        // URL completa para acceder a la imagen
        $qrUrl = asset('storage/' . $qrPath);
        
        // Generar PDF incluyendo la URL del código QR
        $pdf = PDF::loadView('pdfs.ticket_alt', [
            'ticketData' => $ticketData,
            'qrUrl' => $qrUrl,
            'qrPath' => storage_path('app/public/' . $qrPath)
        ]);
        
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
}
