@extends('layouts.app')

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

    .movie-detail-card {
        background: linear-gradient(45deg, var(--color-mid-blue), var(--color-light-blue));
        color: var(--color-text);
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .movie-detail-card .card-header {
        background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 20px 30px;
        position: relative;
    }
    
    .movie-detail-card .card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 3px;
        background-color: var(--color-accent);
    }
    
    .movie-detail-card h2, .movie-detail-card h3, .movie-detail-card h4 {
        color: var(--color-text);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .movie-detail-card h3 {
        border-bottom: 2px solid var(--color-accent);
        padding-bottom: 10px;
        display: inline-block;
        margin-bottom: 20px;
    }
    
    .movie-detail-card .card-body {
        background-color: rgba(0, 0, 0, 0.15);
        padding: 30px;
    }
    
    .movie-detail-card .btn {
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
    
    .movie-detail-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .movie-detail-card .btn-primary {
        background-color: var(--color-accent);
        color: #0A2B4F;
    }
    
    .movie-detail-card .btn-secondary {
        background-color: rgba(255, 255, 255, 0.2);
        color: var(--color-text);
    }
    
    .movie-detail-card .movie-poster {
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        position: relative;
    }
    
    .movie-detail-card .movie-poster img {
        width: 100%;
        transition: transform 0.5s;
    }
    
    .movie-detail-card .movie-poster:hover img {
        transform: scale(1.05);
    }
    
    .movie-detail-card .movie-info .row {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .movie-detail-card .movie-info .row:last-child {
        border-bottom: none;
    }
    
    .movie-detail-card .font-weight-bold {
        color: var(--color-accent);
        font-weight: 600;
    }
    
    .movie-detail-card .description-section {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
    }
    
    .movie-detail-card .screenings-section {
        margin-top: 30px;
    }
    
    .movie-detail-card .screenings-section h4 {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .movie-detail-card table.table {
        color: var(--color-text);
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .movie-detail-card table.table thead th {
        background-color: var(--color-light-blue);
        padding: 12px 15px;
        border: none;
        color: var(--color-accent);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    
    .movie-detail-card table.table thead th:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .movie-detail-card table.table thead th:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .movie-detail-card table.table tbody tr {
        background-color: rgba(255, 255, 255, 0.05);
        transition: transform 0.2s, background-color 0.2s;
    }
    
    .movie-detail-card table.table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .movie-detail-card table.table tbody td {
        padding: 12px 15px;
        border: none;
    }
    
    .movie-detail-card table.table tbody td:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .movie-detail-card table.table tbody td:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .movie-detail-card .no-screenings {
        background-color: rgba(255, 255, 255, 0.05);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
    }
    
    @media (max-width: 768px) {
        .movie-detail-card .movie-poster {
            margin-bottom: 30px;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card movie-detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-film me-2"></i> Detalles de la Película</h2>
                    <div>
                        <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="movie-poster">
                                @if($movie->image)
                                    <img src="{{ asset('storage/' . $movie->image) }}" alt="{{ $movie->title }}" class="img-fluid">
                                @else
                                    <div class="text-center p-5" style="background-color: rgba(255,255,255,0.05);">
                                        <i class="fas fa-image" style="font-size: 5rem; color: rgba(255,255,255,0.2);"></i>
                                        <p class="mt-3">Sin imagen disponible</p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($movie->trailer)
                            <div class="mt-3 text-center">
                                <a href="{{ $movie->trailer }}" target="_blank" class="btn btn-secondary w-100">
                                    <i class="fas fa-play-circle me-1"></i> Ver trailer
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-4">{{ $movie->title }}</h3>
                            
                            <div class="movie-info">
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-user-tie me-2"></i> Director:</div>
                                    <div class="col-md-8">{{ $movie->director }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-users me-2"></i> Actores:</div>
                                    <div class="col-md-8">{{ $movie->actors ?? 'No especificado' }}</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-tag me-2"></i> Género:</div>
                                    <div class="col-md-8">
                                        <span class="badge" style="background-color: var(--color-secondary);">
                                            {{ $movie->movieGenre->name ?? 'Sin género' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-clock me-2"></i> Duración:</div>
                                    <div class="col-md-8">
                                        <span class="badge bg-light-blue text-white">
                                            {{ $movie->duration }} minutos
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 font-weight-bold"><i class="fas fa-calendar-alt me-2"></i> Estreno:</div>
                                    <div class="col-md-8">
                                        <span class="badge" style="background-color: var(--color-tertiary);">
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="description-section">
                                <h4><i class="fas fa-align-left me-2"></i> Descripción</h4>
                                <p>{{ $movie->description }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sesiones programadas -->
                    <div class="screenings-section">
                        <h4><i class="fas fa-ticket-alt me-2"></i> Sesiones Programadas</h4>
                        @if($movie->screenings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                                            <th><i class="fas fa-clock me-1"></i> Hora</th>
                                            <th><i class="fas fa-map-marker-alt me-1"></i> Auditorio</th>
                                            <th><i class="fas fa-euro-sign me-1"></i> Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($movie->screenings as $screening)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($screening->date)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($screening->start_time)->format('H:i') }}</td>
                                            <td>{{ $screening->auditorium->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($screening->price, 2) }} €</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="no-screenings">
                                <i class="fas fa-calendar-times mb-3" style="font-size: 2rem;"></i>
                                <p>No hay sesiones programadas para esta película.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection