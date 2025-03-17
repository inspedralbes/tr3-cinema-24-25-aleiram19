@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Detalles de la Película</h2>
                    <div>
                        <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-primary">Editar</a>
                        <a href="{{ route('movies.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            @if($movie->image)
                                <img src="{{ asset('storage/' . $movie->image) }}" alt="{{ $movie->title }}" class="img-fluid rounded">
                            @else
                                <div class="bg-light text-center p-5 rounded">
                                    <p>Sin imagen</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-3">{{ $movie->title }}</h3>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Director:</div>
                                <div class="col-md-8">{{ $movie->director }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Actores:</div>
                                <div class="col-md-8">{{ $movie->actors ?? 'No especificado' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Género:</div>
                                <div class="col-md-8">{{ $movie->movieGenre->name ?? 'Sin género' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Duración:</div>
                                <div class="col-md-8">{{ $movie->duration }} minutos</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Fecha de estreno:</div>
                                <div class="col-md-8">{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</div>
                            </div>
                            
                            @if($movie->trailer)
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Trailer:</div>
                                <div class="col-md-8">
                                    <a href="{{ $movie->trailer }}" target="_blank">Ver trailer</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Descripción</h4>
                        <p>{{ $movie->description }}</p>
                    </div>
                    
                    <!-- Sesiones programadas -->
                    <div class="mt-4">
                        <h4>Sesiones Programadas</h4>
                        @if($movie->screenings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Auditorio</th>
                                            <th>Precio</th>
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
                            <p>No hay sesiones programadas para esta película.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection