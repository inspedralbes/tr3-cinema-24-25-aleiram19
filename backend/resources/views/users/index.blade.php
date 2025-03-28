@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Crear Usuario
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
                            <th scope="col"><i class="fas fa-user me-1"></i> Nombre</th>
                            <th scope="col"><i class="fas fa-user-tag me-1"></i> Apellido</th>
                            <th scope="col"><i class="fas fa-envelope me-1"></i> Email</th>
                            <th scope="col"><i class="fas fa-shield-alt me-1"></i> Rol</th>
                            <th scope="col" class="text-center"><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td data-label="ID">{{ $user->id }}</td>
                            <td data-label="Nombre" class="fw-bold">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle" style="background-color: var(--color-accent); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--color-dark-blue);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td data-label="Apellido">{{ $user->last_name }}</td>
                            <td data-label="Email">
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none" style="color: var(--color-accent);">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td data-label="Rol">
                                <span class="badge" style="background-color: var(--color-tertiary);">
                                    {{ $user->role->name ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td data-label="Acciones" class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm" title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Estás seguro de querer eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar usuario">
                                            <i class="fas fa-trash"></i>
                                        </button>
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