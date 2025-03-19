@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Gestión de Asientos</h2>
                    <a href="{{ route('seats.create') }}" class="btn btn-primary">Crear Asiento</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número</th>
                                    <th>Auditorio</th>
                                    <th>Estado</th>
                                    <th>VIP</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seats as $seat)
                                <tr>
                                    <td>{{ $seat->id }}</td>
                                    <td>{{ $seat->number }}</td>
                                    <td>{{ $seat->auditorium->name ?? 'Sin auditorio' }}</td>
                                    <td>
                                        @if($seat->status == 'available')
                                            <span class="badge bg-success">Disponible</span>
                                        @elseif($seat->status == 'unavailable')
                                            <span class="badge bg-danger">No disponible</span>
                                        @elseif($seat->status == 'maintenance')
                                            <span class="badge bg-warning">Mantenimiento</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $seat->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($seat->is_vip)
                                            <span class="badge bg-info">VIP</span>
                                        @else
                                            <span class="badge bg-secondary">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('seats.show', $seat->id) }}" class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('seats.edit', $seat->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                            <form action="{{ route('seats.destroy', $seat->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar este asiento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection