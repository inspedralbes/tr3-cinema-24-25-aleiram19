@extends('layouts.app')

@section('title', 'Gestión de Asientos')

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-chair"></i> Gestión de Asientos</h2>
            <a href="{{ route('seats.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Crear Asiento
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
                            <th scope="col"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th scope="col"><i class="fas fa-chair me-1"></i> Número</th>
                            <th scope="col"><i class="fas fa-door-open me-1"></i> Auditorio</th>
                            <th scope="col"><i class="fas fa-check-circle me-1"></i> Estado</th>
                            <th scope="col"><i class="fas fa-star me-1"></i> VIP</th>
                            <th scope="col" class="text-center"><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seats as $seat)
                        <tr>
                            <td data-label="ID">{{ $seat->id }}</td>
                            <td data-label="Número" class="fw-bold">{{ $seat->number }}</td>
                            <td data-label="Auditorio">
                                <span class="badge" style="background-color: var(--blue-400);">
                                    <i class="fas fa-door-open me-1"></i> {{ $seat->auditorium->name ?? 'Sin auditorio' }}
                                </span>
                            </td>
                            <td data-label="Estado">
                                @if($seat->status == 'available')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Disponible
                                    </span>
                                @elseif($seat->status == 'unavailable')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i> No disponible
                                    </span>
                                @elseif($seat->status == 'maintenance')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-tools me-1"></i> Mantenimiento
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ $seat->status }}</span>
                                @endif
                            </td>
                            <td data-label="VIP">
                                @if($seat->is_vip)
                                    <span class="badge" style="background-color: var(--blue-400);">
                                        <i class="fas fa-star me-1"></i> VIP
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Normal</span>
                                @endif
                            </td>
                            <td data-label="Acciones" class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('seats.show', $seat->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('seats.edit', $seat->id) }}" class="btn btn-primary btn-sm" title="Editar asiento">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('seats.destroy', $seat->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Estás seguro de querer eliminar este asiento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar asiento">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if(count($seats) == 0)
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-chair fa-3x mb-3 text-muted"></i>
                                    <p class="h5 text-muted">No hay asientos registrados</p>
                                    <a href="{{ route('seats.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus-circle me-1"></i> Crear Asiento
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