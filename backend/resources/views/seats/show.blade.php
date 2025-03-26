@extends('layouts.app')

@section('title', 'Detalles del Asiento')

@section('content')
<style>
    :root {
        --color-dark-blue: #051D40;    /* Fondo principal - navy-900 */
        --color-mid-blue: #0078C8;     /* Para degradados - navy-600 */
        --color-light-blue: #00A0E4;   /* Opcional en secciones - blue-400 */
        --color-accent: #00A0E4;       /* Celeste - blue-400 */
        --color-secondary: #0078C8;    /* Color secundario - navy-600 */
        --color-tertiary: #051D40;     /* Color terciario - blue-900 */
        --color-text: #FFFFFF;         /* Texto claro */
        
        --transition-speed: 0.3s;
    }

    .seat-detail-card {
        background: linear-gradient(45deg, var(--color-mid-blue), var(--color-light-blue));
        color: var(--color-text);
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .seat-detail-card .card-header {
        background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 20px 30px;
        position: relative;
    }
    
    .seat-detail-card .card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 3px;
        background-color: var(--color-accent);
    }
    
    .seat-detail-card h2, .seat-detail-card h3, .seat-detail-card h4 {
        color: var(--color-text);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .seat-detail-card .card-body {
        background-color: rgba(0, 0, 0, 0.15);
        padding: 30px;
    }
    
    .seat-detail-card .btn {
        border: none;
        border-radius: 6px;
        transition: all var(--transition-speed);
        font-weight: 500;
        padding: 8px 16px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        margin-right: 10px;
    }
    
    .seat-detail-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .seat-detail-card .btn-primary {
        background-color: var(--color-accent);
        color: #0A2B4F;
    }
    
    .seat-detail-card .btn-secondary {
        background-color: rgba(255, 255, 255, 0.2);
        color: var(--color-text);
    }
    
    .seat-detail-card .font-weight-bold {
        color: var(--color-accent);
        font-weight: 600;
    }
    
    .seat-detail-card .seat-info .row {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .seat-detail-card .seat-info .row:last-child {
        border-bottom: none;
    }
    
    .seat-detail-card table.table {
        color: var(--color-text);
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .seat-detail-card table.table thead th {
        background-color: var(--color-tertiary);
        padding: 12px 15px;
        border: none;
        color: var(--color-text);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    
    .seat-detail-card table.table thead th:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .seat-detail-card table.table thead th:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .seat-detail-card table.table tbody tr {
        background-color: rgba(255, 255, 255, 0.05);
        transition: transform 0.2s, background-color 0.2s;
    }
    
    .seat-detail-card table.table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .seat-detail-card table.table tbody td {
        padding: 12px 15px;
        border: none;
    }
    
    .seat-detail-card table.table tbody td:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .seat-detail-card table.table tbody td:last-child {
        border-radius: 0 8px 8px 0;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card seat-detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-chair me-2"></i> Detalles del Asiento</h2>
                    <div>
                        <a href="{{ route('seats.edit', $seat->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('seats.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-md-6 mb-4">
                            <div class="text-center p-4" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                                <div style="font-size: 5rem; color: var(--color-accent);">
                                    <i class="fas fa-chair"></i>
                                </div>
                                <h3 class="mt-3">Asiento {{ $seat->number }}</h3>
                                <div class="mt-2">
                                    @if($seat->is_vip)
                                        <span class="badge px-3 py-2" style="background-color: #FFD700; color: #000;">
                                            <i class="fas fa-star me-1"></i> VIP
                                        </span>
                                    @else
                                        <span class="badge px-3 py-2" style="background-color: var(--color-secondary);">
                                            Estándar
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="seat-info">
                                <div class="row">
                                    <div class="col-md-5 font-weight-bold"><i class="fas fa-id-badge me-2"></i> ID:</div>
                                    <div class="col-md-7">{{ $seat->id }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-5 font-weight-bold"><i class="fas fa-film me-2"></i> Auditorio:</div>
                                    <div class="col-md-7">{{ $seat->auditorium->name ?? 'Sin auditorio' }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-5 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Estado:</div>
                                    <div class="col-md-7">
                                        @if($seat->status == 'available')
                                            <span class="badge bg-success px-3">Disponible</span>
                                        @elseif($seat->status == 'unavailable')
                                            <span class="badge bg-danger px-3">No disponible</span>
                                        @elseif($seat->status == 'maintenance')
                                            <span class="badge bg-warning px-3 text-dark">Mantenimiento</span>
                                        @else
                                            <span class="badge bg-secondary px-3">{{ $seat->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-5 font-weight-bold"><i class="fas fa-calendar-plus me-2"></i> Creado:</div>
                                    <div class="col-md-7">{{ $seat->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-5 font-weight-bold"><i class="fas fa-calendar-check me-2"></i> Actualizado:</div>
                                    <div class="col-md-7">{{ $seat->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reservas del asiento -->
                    <div class="mt-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                        <h4 class="mb-3 border-bottom pb-2">
                            <i class="fas fa-ticket-alt me-2"></i> Historial de Reservas
                        </h4>
                        
                        @if($seat->bookings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-hashtag me-1"></i> ID</th>
                                            <th><i class="fas fa-film me-1"></i> Película</th>
                                            <th><i class="fas fa-user me-1"></i> Usuario</th>
                                            <th><i class="fas fa-calendar-day me-1"></i> Fecha</th>
                                            <th><i class="fas fa-clock me-1"></i> Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seat->bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->screening->movie->title ?? 'N/A' }}</td>
                                            <td>{{ $booking->user->name ?? 'Invitado' }}</td>
                                            <td>{{ $booking->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $booking->created_at->format('H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-4" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <i class="fas fa-calendar-times mb-3" style="font-size: 2rem; color: var(--color-accent);"></i>
                                <p>Este asiento no tiene reservas en su historial.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection