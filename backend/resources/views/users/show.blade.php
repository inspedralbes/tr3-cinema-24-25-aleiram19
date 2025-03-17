@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Detalles del Usuario</h2>
                    <div>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Editar</a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">ID:</div>
                        <div class="col-md-8">{{ $user->id }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Nombre:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Apellido:</div>
                        <div class="col-md-8">{{ $user->last_name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Rol:</div>
                        <div class="col-md-8">{{ $user->role->name ?? 'Sin rol' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Invitado:</div>
                        <div class="col-md-8">{{ $user->is_guest ? 'Sí' : 'No' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Fecha de creación:</div>
                        <div class="col-md-8">{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Última actualización:</div>
                        <div class="col-md-8">{{ $user->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <!-- Tickets del usuario -->
                    <h4 class="mt-4 mb-3">Tickets</h4>
                    @if($user->tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Película</th>
                                        <th>Sesión</th>
                                        <th>Asiento</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ $ticket->screening->movie->title ?? 'N/A' }}</td>
                                        <td>{{ $ticket->screening->start_time ?? 'N/A' }}</td>
                                        <td>{{ $ticket->seat->number ?? 'N/A' }}</td>
                                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Este usuario no tiene tickets.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection