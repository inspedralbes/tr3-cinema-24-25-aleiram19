@extends('layouts.app')

@section('content')
<!-- Estilos personalizados para la sección de películas -->
<style>
    :root {
        /* Paleta de colores inspirada en tu imagen */
        --color-dark-blue: #0A2B4F;    /* Fondo principal */
        --color-mid-blue: #102C54;     /* Para degradados */
        --color-light-blue: #1C3A66;   /* Opcional en secciones */
        --color-accent: #00BCD4;       /* Celeste */
        --color-text: #FFFFFF;         /* Texto claro */
        
        --transition-speed: 0.3s;
    }

    /* Contenedor principal (sobrescribe estilos del layout si es necesario) */
    body {
        background-color: var(--color-dark-blue) !important;
    }

    /* Card personalizado */
    .movie-card {
        background: linear-gradient(60deg, var(--color-mid-blue), var(--color-light-blue));
        color: var(--color-text);
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    .movie-card .card-header {
        background: transparent; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    .movie-card h2 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: bold;
    }

    /* Botones */
    .movie-card .btn {
        border: none;
        transition: background-color var(--transition-speed);
        font-weight: 500;
    }
    .movie-card .btn-primary {
        background-color: var(--color-accent);
        color: #0A2B4F; /* Texto oscuro para contrastar con el acento celeste */
    }
    .movie-card .btn-primary:hover {
        background-color: #00a2ba;
    }
    .movie-card .btn-info {
        background-color: #17a2b8;
        color: #fff;
    }
    .movie-card .btn-info:hover {
        background-color: #138496;
    }
    .movie-card .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }
    .movie-card .btn-danger:hover {
        background-color: #c82333;
    }

    /* Cuerpo del card */
    .movie-card .card-body {
        background-color: rgba(0, 0, 0, 0.15);
        border-radius: 0 0 10px 10px;
    }

    /* Alertas */
    .movie-card .alert {
        border: 1px solid transparent;
        margin-bottom: 1rem;
    }
    .movie-card .alert.alert-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-color: #28a745;
    }
    .movie-card .alert.alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-color: #dc3545;
    }

    /* Tabla */
    .movie-card table.table {
        color: var(--color-text);
    }
    .movie-card table.table thead th {
        background-color: var(--color-light-blue);
        border-bottom: 2px solid var(--color-accent);
    }
    .movie-card table.table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, 0.05);
    }
    .movie-card table.table-striped tbody tr:nth-of-type(even) {
        background-color: rgba(255, 255, 255, 0.02);
    }
    .movie-card table.table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Se reemplaza la clase 'card' por 'card movie-card' para aplicar estilos propios -->
            <div class="card movie-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Gestión de Películas</h2>
                    <a href="{{ route('movies.create') }}" class="btn btn-primary">Crear Película</a>
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
                                    <th>Imagen</th>
                                    <th>Título</th>
                                    <th>Director</th>
                                    <th>Duración</th>
                                    <th>Género</th>
                                    <th>Estreno</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movies as $movie)
                                <tr>
                                    <td>{{ $movie->id }}</td>
                                    <td>
                                        @if($movie->image)
                                            <img src="{{ asset('storage/' . $movie->image) }}"
                                                 alt="{{ $movie->title }}"
                                                 style="width: 50px; height: auto;">
                                        @else
                                            <span>Sin imagen</span>
                                        @endif
                                    </td>
                                    <td>{{ $movie->title }}</td>
                                    <td>{{ $movie->director }}</td>
                                    <td>{{ $movie->duration }} min</td>
                                    <td>{{ $movie->movieGenre->name ?? 'Sin género' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                            <form action="{{ route('movies.destroy', $movie->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta película?');">
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
                    </div> <!-- table-responsive -->
                </div> <!-- card-body -->
            </div> <!-- movie-card -->
        </div> <!-- col-md-12 -->
    </div> <!-- row -->
</div> <!-- container -->
@endsection
