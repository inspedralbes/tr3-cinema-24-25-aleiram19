@extends('layouts.app')

@section('title', 'Gestión de Proyecciones')

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-video"></i> Gestión de Proyecciones</h2>
            <a href="{{ route('screenings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Crear Proyección
            </a>
        </div>

        <div class="table-content">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Película</th>
                            <th scope="col">Auditorio</th>
                            <th scope="col">Fecha y Hora</th>
                            <th scope="col">Precio Base</th>
                            <th scope="col">Especial</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($screenings as $screening)
                        <tr>
                            <td data-label="ID">{{ $screening->id }}</td>
                            <td data-label="Película" class="fw-bold">
                                @if($screening->movie)
                                    <div class="d-flex align-items-center">
                                        @if($screening->movie->image)
                                            <img src="{{ asset('storage/' . $screening->movie->image) }}" 
                                                alt="{{ $screening->movie->title }}" 
                                                class="me-2"
                                                style="width: 45px; height: auto; border-radius: 4px; object-fit: cover;">
                                        @else
                                            <div class="me-2" style="width: 45px; height: 45px; background-color: rgba(255,255,255,0.1); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-film text-muted"></i>
                                            </div>
                                        @endif
                                        {{ $screening->movie->title }}
                                    </div>
                                @else
                                    <span class="text-muted">Sin película asignada</span>
                                @endif
                            </td>
                            <td data-label="Auditorio">
                                @if($screening->auditorium)
                                    <span class="badge" style="background-color: var(--color-secondary);">
                                        <i class="fas fa-door-open me-1"></i> {{ $screening->auditorium->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin auditorio asignado</span>
                                @endif
                            </td>
                            <td data-label="Fecha y Hora">
                                <span class="badge bg-info">
                                    <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($screening->date_time)->format('d/m/Y') }}
                                </span>
                                <br>
                                <span class="badge" style="background-color: var(--color-tertiary);">
                                    <i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($screening->date_time)->format('H:i') }}
                                </span>
                            </td>
                            <td data-label="Precio Base">{{ number_format($screening->price, 2) }} €</td>
                            <td data-label="Especial">
                                @if($screening->is_special)
                                    <span class="badge bg-success">
                                        <i class="fas fa-star me-1"></i> Día del Espectador
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Normal</span>
                                @endif
                            </td>
                            <td data-label="Estado">
                                @if($screening->active ?? true)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Activa
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td data-label="Acciones" class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('screenings.show', $screening->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('screenings.edit', $screening->id) }}" class="btn btn-primary btn-sm" title="Editar proyección">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('screenings.toggle-active', $screening->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ ($screening->active ?? true) ? 'btn-warning' : 'btn-success' }}" title="{{ ($screening->active ?? true) ? 'Desactivar sesión' : 'Activar sesión' }}">
                                            <i class="fas {{ ($screening->active ?? true) ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('screenings.destroy', $screening->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Estás seguro de querer eliminar esta proyección? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar proyección">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if(count($screenings) == 0)
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-film fa-3x mb-3 text-muted"></i>
                                    <p class="h5 text-muted">No hay proyecciones programadas</p>
                                    <a href="{{ route('screenings.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus-circle me-1"></i> Crear Proyección
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection