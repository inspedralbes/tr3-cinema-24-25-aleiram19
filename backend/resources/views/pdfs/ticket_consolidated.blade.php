@extends('layouts.tickets.base')

@section('qrcode_section')
<div class="qrcode">
    @if(isset($qrCodeBase64))
        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR de la entrada">
    @elseif(isset($qrUrl))
        <img src="{{ $qrUrl }}" alt="Código QR de la entrada">
    @endif
    <div class="qrcode-text">TICKET-{{ $ticketData['ticket_id'] ?? 'N/A' }}</div>
</div>
@endsection