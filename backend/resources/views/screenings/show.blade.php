@extends('layouts.app')

@section('title', 'Detalles de Proyección')

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
    
    .screening-detail-card {
        background: linear-gradient(45deg, var(--color-mid-blue), var(--color-light-blue));
        color: var(--color-text);
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .screening-detail-card .card-header {
        background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 20px 30px;
        position: relative;
    }
    
    .screening-detail-card .card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 3px;
        background-color: var(--color-accent);
    }
    
    .screening-detail-card h2, .screening-detail-card h3, .screening-detail-card h4, .screening-detail-card h5 {
        color: var(--color-text);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .screening-detail-card .card-body {
        background-color: rgba(0, 0, 0, 0.15);
        padding: 30px;
    }
    
    .screening-detail-card .btn {
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
    
    .screening-detail-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .screening-detail-card .btn-primary {
        background-color: var(--color-accent);
        color: #0A2B4F;
    }
    
    .screening-detail-card .btn-secondary {
        background-color: rgba(255, 255, 255, 0.2);
        color: var(--color-text);
    }
    
    .screening-detail-card p strong {
        color: var(--color-accent);
        font-weight: 600;
    }
    
    .screening-detail-card .info-section {
        margin-bottom: 20px;
    }
    
    .screening-detail-card .movie-info {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="screening-detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-video me-2"></i> Detalles de Proyección</h2>
                    <div>
                        <a href="{{ route('screenings.edit', $screening->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <a href="{{ route('screenings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="mb-3 text-center p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                                @if($screening->movie && $screening->movie->image)
                                    <img src="{{ asset('storage/' . $screening->movie->image) }}" 
                                        alt="{{ $screening->movie->title }}" 
                                        class="img-fluid" 
                                        style="max-height: 300px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                                @else
                                    <div style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-film fa-5x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <h3 class="mb-4 border-bottom pb-2" style="color: var(--color-accent);">
                                {{ $screening->movie ? $screening->movie->title : 'Sin película asignada' }}
                            </h3>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-door-open me-2"></i> Auditorio:</strong><br>
                                        {{ $screening->auditorium ? $screening->auditorium->name : 'Sin auditorio asignado' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-chair me-2"></i> Capacidad:</strong><br>
                                        {{ $screening->auditorium ? $screening->auditorium->capacity : 'Desconocida' }} asientos
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-calendar-alt me-2"></i> Fecha:</strong><br>
                                        {{ \Carbon\Carbon::parse($screening->date_time)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-clock me-2"></i> Hora:</strong><br>
                                        {{ \Carbon\Carbon::parse($screening->date_time)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-ticket-alt me-2"></i> Tipo de Sesión:</strong><br>
                                        @if($screening->is_special)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-star me-1"></i> Día del Espectador
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">Normal</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-euro-sign me-2"></i> Precio Base:</strong><br>
                                        <span style="font-size: 1.2rem; font-weight: bold; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                                            {{ number_format($screening->price, 2) }} €
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="my-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                                <h4 class="mb-3" style=""><i class="fas fa-tags me-2"></i> Precios por tipo de asiento</h4>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="card mb-3" style="background: rgba(255,255,255,0.1); border: none; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                            <div class="card-body py-3">
                                                <h5 style="letter-spacing: 1px;">NORMAL</h5>
                                                <p class="h3 mb-0" style="color: #FFFFFF; text-shadow: 0 2px 4px rgba(0,0,0,0.5); font-weight: bold;">
                                                    {{ $screening->is_special ? '4,00 €' : '6,00 €' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card" style="background: rgba(255,255,255,0.15); border: none; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                            <div class="card-body py-3">
                                                <h5 style=" letter-spacing: 1px;">VIP (Fila F)</h5>
                                                <p class="h3 mb-0" style="color; text-shadow: 0 2px 4px rgba(0,0,0,0.5); font-weight: bold;">
                                                    {{ $screening->is_special ? '6,00 €' : '8,00 €' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($screening->movie)
                            <div class="mt-4">
                                <h4 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle me-2"></i> Información de la Película</h4>
                                <p>
                                    <strong>Director:</strong> {{ $screening->movie->director }}<br>
                                    <strong>Duración:</strong> {{ $screening->movie->duration }} minutos<br>
                                    <strong>Género:</strong> {{ $screening->movie->movieGenre->name ?? 'Sin género' }}<br>
                                    <strong>Estreno:</strong> {{ \Carbon\Carbon::parse($screening->movie->release_date)->format('d/m/Y') }}
                                </p>
                                @if($screening->movie->description)
                                <div class="mt-3">
                                    <h5 class="mb-2" style=""><i class="fas fa-quote-left me-2"></i> Sinopsis:</h5>
                                    <p style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; font-size: 1.05rem; line-height: 1.6; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                        {{ $screening->movie->description }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection