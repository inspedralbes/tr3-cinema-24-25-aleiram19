@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')
<style>
    :root {
        --color-dark-blue: #0A2B4F;    /* Fondo principal */
        --color-mid-blue: #102C54;     /* Para degradados */
        --color-light-blue: #1C3A66;   /* Opcional en secciones */
        --color-accent: #00BCD4;       /* Celeste */
        --color-secondary: #FFB300;    /* Color secundario amarillo */
        --color-tertiary: #F46D75;     /* Color terciario rosa/rojo */
        --color-text: #FFFFFF;         /* Texto claro */
        
        --transition-speed: 0.3s;
    }

    .user-detail-card {
        background: linear-gradient(45deg, var(--color-mid-blue), var(--color-light-blue));
        color: var(--color-text);
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .user-detail-card .card-header {
        background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 20px 30px;
        position: relative;
    }
    
    .user-detail-card .card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 3px;
        background-color: var(--color-accent);
    }
    
    .user-detail-card h2, .user-detail-card h3, .user-detail-card h4 {
        color: var(--color-text);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .user-detail-card h3 {
        border-bottom: 2px solid var(--color-accent);
        padding-bottom: 10px;
        display: inline-block;
        margin-bottom: 20px;
    }
    
    .user-detail-card .card-body {
        background-color: rgba(0, 0, 0, 0.15);
        padding: 30px;
    }
    
    .user-detail-card .btn {
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
    
    .user-detail-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .user-detail-card .btn-primary {
        background-color: var(--color-accent);
        color: #0A2B4F;
    }
    
    .user-detail-card .btn-secondary {
        background-color: rgba(255, 255, 255, 0.2);
        color: var(--color-text);
    }
    
    .user-detail-card .user-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: var(--color-accent);
        color: var(--color-dark-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        font-weight: 700;
        margin: 0 auto 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }
    
    .user-detail-card .user-info .row {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .user-detail-card .user-info .row:last-child {
        border-bottom: none;
    }
    
    .user-detail-card .font-weight-bold {
        color: var(--color-accent);
        font-weight: 600;
    }
    
    .user-detail-card .tickets-section {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
    }
    
    .user-detail-card .tickets-section h4 {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .user-detail-card table.table {
        color: var(--color-text);
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .user-detail-card table.table thead th {
        background-color: var(--color-light-blue);
        padding: 12px 15px;
        border: none;
        color: var(--color-accent);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    
    .user-detail-card table.table thead th:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .user-detail-card table.table thead th:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .user-detail-card table.table tbody tr {
        background-color: rgba(255, 255, 255, 0.05);
        transition: transform 0.2s, background-color 0.2s;
    }
    
    .user-detail-card table.table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .user-detail-card table.table tbody td {
        padding: 12px 15px;
        border: none;
    }
    
    .user-detail-card table.table tbody td:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .user-detail-card table.table tbody td:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .user-detail-card .no-tickets {
        background-color: rgba(255, 255, 255, 0.05);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
    }
    
    @media (max-width: 768px) {
        .user-detail-card .user-avatar {
            margin-bottom: 30px;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card user-detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-user me-2"></i> Detalles del Usuario</h2>
                    <div>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4 text-center">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h3 class="mt-3">{{ $user->name }} {{ $user->last_name }}</h3>
                            <div class="badge mb-2" style="background-color: var(--color-tertiary);">
                                {{ $user->role->name ?? 'Sin rol' }}
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="user-info">
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-id-card me-2"></i> ID:</div>
                                    <div class="col-md-8">{{ $user->id }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-envelope me-2"></i> Email:</div>
                                    <div class="col-md-8">
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none" style="color: var(--color-accent);">
                                            {{ $user->email }}
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-user-tag me-2"></i> Invitado:</div>
                                    <div class="col-md-8">
                                        <span class="badge" style="background-color: {{ $user->is_guest ? 'var(--color-secondary)' : 'var(--color-accent)' }};">
                                            {{ $user->is_guest ? 'Sí' : 'No' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-calendar-plus me-2"></i> Fecha de creación:</div>
                                    <div class="col-md-8">{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-calendar-check me-2"></i> Última actualización:</div>
                                    <div class="col-md-8">{{ $user->updated_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tickets del usuario -->
                    <div class="tickets-section">
                        <h4><i class="fas fa-ticket-alt me-2"></i> Tickets del Usuario</h4>
                        @if($user->tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-hashtag me-1"></i> ID</th>
                                            <th><i class="fas fa-film me-1"></i> Película</th>
                                            <th><i class="fas fa-clock me-1"></i> Sesión</th>
                                            <th><i class="fas fa-chair me-1"></i> Asiento</th>
                                            <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->screening->movie->title ?? 'N/A' }}</td>
                                            <td>{{ $ticket->screening->start_time ? \Carbon\Carbon::parse($ticket->screening->start_time)->format('H:i') : 'N/A' }}</td>
                                            <td>{{ $ticket->seat->number ?? 'N/A' }}</td>
                                            <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="no-tickets">
                                <i class="fas fa-ticket-alt mb-3" style="font-size: 2rem;"></i>
                                <p>Este usuario no tiene tickets registrados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection