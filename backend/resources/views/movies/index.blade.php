@extends('layouts.app')

@section('title', 'Gestión de Películas')

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-film"></i> Gestión de Películas</h2>
            <a href="{{ route('movies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Crear Película
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
                            <th scope="col">Imagen</th>
                            <th scope="col">Título</th>
                            <th scope="col">Director</th>
                            <th scope="col">Duración</th>
                            <th scope="col">Género</th>
                            <th scope="col">Estreno</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movies as $movie)
                        <tr>
                            <td data-label="ID">{{ $movie->id }}</td>
                            <td data-label="Imagen">
                                @if($movie->image)
                                    <img src="{{ asset('storage/' . $movie->image) }}"
                                         alt="{{ $movie->title }}"
                                         style="width: 70px; height: auto; object-fit: cover; border-radius: 6px; box-shadow: 0 3px 6px rgba(0,0,0,0.2);">
                                @else
                                    <div class="text-center p-2 rounded" style="background-color: rgba(255,255,255,0.1);">
                                        <i class="fas fa-film text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td data-label="Título" class="fw-bold">{{ $movie->title }}</td>
                            <td data-label="Director">{{ $movie->director }}</td>
                            <td data-label="Duración">
                                <span class="badge bg-info">
                                    <i class="fas fa-clock me-1"></i> {{ $movie->duration }} min
                                </span>
                            </td>
                            <td data-label="Género">
                                <span class="badge" style="background-color: var(--color-secondary);">
                                    <i class="fas fa-tag me-1"></i> {{ $movie->movieGenre->name ?? 'Sin género' }}
                                </span>
                            </td>
                            <td data-label="Estreno">
                                <span class="badge" style="background-color: var(--color-tertiary);">
                                    <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td data-label="Acciones" class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-primary btn-sm" title="Editar película">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('movies.destroy', $movie->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Estás seguro de querer eliminar esta película?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar película">
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
