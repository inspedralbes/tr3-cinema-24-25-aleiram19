@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Crear Película</h2>
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

                    <form method="POST" action="{{ route('movies.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="title" class="col-md-4 col-form-label text-md-right">Título</label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="director" class="col-md-4 col-form-label text-md-right">Director</label>
                            <div class="col-md-6">
                                <input id="director" type="text" class="form-control @error('director') is-invalid @enderror" name="director" value="{{ old('director') }}" required>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="actors" class="col-md-4 col-form-label text-md-right">Actores</label>
                            <div class="col-md-6">
                                <input id="actors" type="text" class="form-control @error('actors') is-invalid @enderror" name="actors" value="{{ old('actors') }}">
                                <small class="form-text text-muted">Separados por comas</small>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="trailer" class="col-md-4 col-form-label text-md-right">URL del Trailer</label>
                            <div class="col-md-6">
                                <input id="trailer" type="url" class="form-control @error('trailer') is-invalid @enderror" name="trailer" value="{{ old('trailer') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="duration" class="col-md-4 col-form-label text-md-right">Duración (minutos)</label>
                            <div class="col-md-6">
                                <input id="duration" type="number" class="form-control @error('duration') is-invalid @enderror" name="duration" value="{{ old('duration') }}" required min="1">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="movie_genre_id" class="col-md-4 col-form-label text-md-right">Género</label>
                            <div class="col-md-6">
                                <select id="movie_genre_id" class="form-control @error('movie_genre_id') is-invalid @enderror" name="movie_genre_id" required>
                                    <option value="">Seleccionar Género</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ old('movie_genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="release_date" class="col-md-4 col-form-label text-md-right">Fecha de Estreno</label>
                            <div class="col-md-6">
                                <input id="release_date" type="date" class="form-control @error('release_date') is-invalid @enderror" name="release_date" value="{{ old('release_date') }}" required>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="image" class="col-md-4 col-form-label text-md-right">Imagen</label>
                            <div class="col-md-6">
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Crear Película
                                </button>
                                <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                                    Cancelar
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