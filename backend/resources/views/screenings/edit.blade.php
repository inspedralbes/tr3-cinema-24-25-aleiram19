@extends('layouts.app')

@section('title', 'Editar Proyección')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="custom-form-card">
                <div class="card-header">
                    <h2><i class="fas fa-edit me-2"></i> Editar Proyección</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('screenings.update', $screening->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <label for="movie_id" class="col-md-3 col-form-label">Película</label>
                            <div class="col-md-9">
                                <select id="movie_id" class="form-select @error('movie_id') is-invalid @enderror" name="movie_id" required>
                                    <option value="">Seleccione una película</option>
                                    @foreach($movies as $movie)
                                        <option value="{{ $movie->id }}" {{ (old('movie_id', $screening->movie_id) == $movie->id) ? 'selected' : '' }}>
                                            {{ $movie->title }} ({{ $movie->duration }} min)
                                        </option>
                                    @endforeach
                                </select>
                                @error('movie_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="auditorium_id" class="col-md-3 col-form-label">Auditorio</label>
                            <div class="col-md-9">
                                <select id="auditorium_id" class="form-select @error('auditorium_id') is-invalid @enderror" name="auditorium_id" required>
                                    <option value="">Seleccione un auditorio</option>
                                    @foreach($auditoriums as $auditorium)
                                        <option value="{{ $auditorium->id }}" {{ (old('auditorium_id', $screening->auditorium_id) == $auditorium->id) ? 'selected' : '' }}>
                                            {{ $auditorium->name }} ({{ $auditorium->capacity }} asientos)
                                        </option>
                                    @endforeach
                                </select>
                                @error('auditorium_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="date" class="col-md-3 col-form-label">Fecha</label>
                            <div class="col-md-9">
                                @php
                                    $screeningDate = \Carbon\Carbon::parse($screening->date_time)->format('Y-m-d');
                                    $minDate = now()->format('Y-m-d');
                                    // Si la fecha de la proyección es anterior a hoy, permitimos mantenerla
                                    if (\Carbon\Carbon::parse($screeningDate)->lt(now())) {
                                        $minDate = $screeningDate;
                                    }
                                @endphp
                                <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date', $screeningDate) }}" required min="{{ $minDate }}">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i> La fecha debe ser hoy o posterior
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="time" class="col-md-3 col-form-label">Hora</label>
                            <div class="col-md-9">
                                <select id="time" class="form-select @error('time') is-invalid @enderror" name="time" required>
                                    <option value="">Seleccione un horario</option>
                                    @php
                                        $screeningTime = \Carbon\Carbon::parse($screening->date_time)->format('H:i');
                                    @endphp
                                    <option value="16:00" {{ (old('time', $screeningTime) == '16:00') ? 'selected' : '' }}>16:00</option>
                                    <option value="18:00" {{ (old('time', $screeningTime) == '18:00') ? 'selected' : '' }}>18:00</option>
                                    <option value="20:00" {{ (old('time', $screeningTime) == '20:00') ? 'selected' : '' }}>20:00</option>
                                </select>
                                @error('time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i> Horarios disponibles: 16:00, 18:00 y 20:00
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-9 offset-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_special" name="is_special" value="1" {{ old('is_special', $screening->is_special) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_special">
                                        <i class="fas fa-star me-1 text-warning"></i> Día del Espectador
                                    </label>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Marcar esta opción aplicará precios reducidos:
                                    <ul class="mt-1 mb-0">
                                        <li>Asientos normales: 4€ (en lugar de 6€)</li>
                                        <li>Asientos VIP (fila F): 6€ (en lugar de 8€)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('screenings.index') }}" class="btn btn-secondary">
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