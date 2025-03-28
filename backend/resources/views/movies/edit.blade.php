@extends('layouts.app')

@section('title', 'Editar Película')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="custom-form-card">
                <div class="card-header">
                    <h2><i class="fas fa-film me-2"></i> Editar Película</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('movies.update', $movie->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <label for="title" class="col-md-3 col-form-label">Título</label>
                            <div class="col-md-9">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $movie->title) }}" required autofocus>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="director" class="col-md-3 col-form-label">Director</label>
                            <div class="col-md-9">
                                <input id="director" type="text" class="form-control @error('director') is-invalid @enderror" name="director" value="{{ old('director', $movie->director) }}" required>
                                @error('director')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="actors" class="col-md-3 col-form-label">Actores</label>
                            <div class="col-md-9">
                                <input id="actors" type="text" class="form-control @error('actors') is-invalid @enderror" name="actors" value="{{ old('actors', $movie->actors) }}">
                                <div class="form-text">Separados por comas (p. ej: Tom Hanks, Leonardo DiCaprio)</div>
                                @error('actors')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="description" class="col-md-3 col-form-label">Descripción</label>
                            <div class="col-md-9">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required rows="4">{{ old('description', $movie->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="trailer" class="col-md-3 col-form-label">URL del Trailer</label>
                            <div class="col-md-9">
                                <input id="trailer" type="url" class="form-control @error('trailer') is-invalid @enderror" name="trailer" value="{{ old('trailer', $movie->trailer) }}">
                                @error('trailer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="duration" class="col-md-3 col-form-label">Duración (minutos)</label>
                            <div class="col-md-9">
                                <input id="duration" type="number" class="form-control @error('duration') is-invalid @enderror" name="duration" value="{{ old('duration', $movie->duration) }}" required min="1">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="movie_genre_id" class="col-md-3 col-form-label">Género</label>
                            <div class="col-md-9">
                                <select id="movie_genre_id" class="form-select @error('movie_genre_id') is-invalid @enderror" name="movie_genre_id" required>
                                    <option value="">Seleccionar Género</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ old('movie_genre_id', $movie->movie_genre_id) == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                                    @endforeach
                                </select>
                                @error('movie_genre_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="release_date" class="col-md-3 col-form-label">Fecha de Estreno</label>
                            <div class="col-md-9">
                                <input id="release_date" type="date" class="form-control @error('release_date') is-invalid @enderror" name="release_date" value="{{ old('release_date', $movie->release_date) }}" required>
                                @error('release_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="image" class="col-md-3 col-form-label">Imagen</label>
                            <div class="col-md-9">
                                @if($movie->image)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $movie->image) }}" alt="{{ $movie->title }}" 
                                             class="img-thumbnail" style="max-width: 300px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                    </div>
                                @endif
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                                <div class="form-text">Dejar en blanco para mantener la imagen actual</div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="fas fa-save me-2"></i> Actualizar Película
                                </button>
                                <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection