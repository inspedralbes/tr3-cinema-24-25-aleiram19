@extends('layouts.app')

@section('title', 'Detalles de Proyección')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="custom-form-card">
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
                                        {{ number_format($screening->price, 2) }} €
                                    </p>
                                </div>
                            </div>
                            
                            <div class="my-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                                <h4 class="mb-3"><i class="fas fa-tags me-2"></i> Precios por tipo de asiento</h4>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="card mb-3" style="background: rgba(255,255,255,0.1); border: none; border-radius: 8px;">
                                            <div class="card-body py-3">
                                                <h5>Normal</h5>
                                                <p class="h3 mb-0" style="color: var(--color-accent);">{{ $screening->is_special ? '4,00 €' : '6,00 €' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card" style="background: rgba(255,255,255,0.1); border: none; border-radius: 8px;">
                                            <div class="card-body py-3">
                                                <h5>VIP (Fila F)</h5>
                                                <p class="h3 mb-0" style="color: var(--color-secondary);">{{ $screening->is_special ? '6,00 €' : '8,00 €' }}</p>
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
                                    <h5>Sinopsis:</h5>
                                    <p>{{ $screening->movie->description }}</p>
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