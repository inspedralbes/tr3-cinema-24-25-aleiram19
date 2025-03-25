<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'snack' => $ticket->snack ? [
                'name' => $ticket->snack->name,
                'quantity' => $ticket->snack_quantity,
                'price' => $ticket->snack->price * $ticket->snack_quantity
            ] : null,
            'total_price' => $ticket->total_pay
        ];

        // Generar PDF
        $pdf = PDF::loadView('pdfs.ticket', ['ticketData' => $ticketData]);
        
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
