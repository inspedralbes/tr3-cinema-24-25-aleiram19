@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Detalles del Asiento</h2>
                    <div>
                        <a href="{{ route('seats.edit', $seat->id) }}" class="btn btn-primary">Editar</a>
                        <a href="{{ route('seats.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">ID:</div>
                        <div class="col-md-8">{{ $seat->id }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Número de Asiento:</div>
                        <div class="col-md-8">{{ $seat->number }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Auditorio:</div>
                        <div class="col-md-8">{{ $seat->auditorium->name ?? 'Sin auditorio' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Estado:</div>
                        <div class="col-md-8">
                            @if($seat->status == 'available')
                                <span class="badge bg-success">Disponible</span>
                            @elseif($seat->status == 'unavailable')
                                <span class="badge bg-danger">No disponible</span>
                            @elseif($seat->status == 'maintenance')
                                <span class="badge bg-warning">Mantenimiento</span>
                            @else
                                <span class="badge bg-secondary">{{ $seat->status }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">VIP:</div>
                        <div class="col-md-8">
                            @if($seat->is_vip)
                                <span class="badge bg-info">Sí</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Fecha de creación:</div>
                        <div class="col-md-8">{{ $seat->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Última actualización:</div>
                        <div class="col-md-8">{{ $seat->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <!-- Reservas del asiento -->
                    <h4 class="mt-4 mb-3">Reservas</h4>
                    @if($seat->bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sesión</th>
                                        <th>Película</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seat->bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->screening_id }}</td>
                                        <td>{{ $booking->screening->movie->title ?? 'N/A' }}</td>
                                        <td>{{ $booking->user->name ?? 'Invitado' }}</td>
                                        <td>{{ $booking->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Este asiento no tiene reservas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection