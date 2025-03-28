@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Crear Asiento</h2>
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

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seats.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="auditorium_id" class="col-md-4 col-form-label text-md-right">Auditorio</label>
                            <div class="col-md-6">
                                <select id="auditorium_id" class="form-control @error('auditorium_id') is-invalid @enderror" name="auditorium_id" required>
                                    <option value="">Seleccionar Auditorio</option>
                                    @foreach($auditoriums as $auditorium)
                                        <option value="{{ $auditorium->id }}" {{ old('auditorium_id') == $auditorium->id ? 'selected' : '' }}>{{ $auditorium->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="number" class="col-md-4 col-form-label text-md-right">Número de Asiento</label>
                            <div class="col-md-6">
                                <input id="number" type="text" class="form-control @error('number') is-invalid @enderror" name="number" value="{{ old('number') }}" required>
                                <small class="form-text text-muted">Formato recomendado: F5 (fila F, asiento 5)</small>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Estado</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>No disponible</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Crear Asiento
                                </button>
                                <a href="{{ route('seats.index') }}" class="btn btn-secondary">
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