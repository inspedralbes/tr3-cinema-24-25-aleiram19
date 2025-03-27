@extends('layouts.app')

@section('title', "Informe de Películas - $month $year")

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-film"></i> Informe de Películas: {{ $month }} {{ $year }}</h2>
            <div>
                <form action="{{ route('admin.reports.movies') }}" method="GET" class="d-inline">
                    <input type="hidden" name="month" value="{{ request()->input('month', Carbon\Carbon::now()->month) }}">
                    <input type="hidden" name="year" value="{{ request()->input('year', Carbon\Carbon::now()->year) }}">
                    <input type="hidden" name="download_pdf" value="1">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                    </button>
                </form>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="table-content">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Película</th>
                            <th>Duración</th>
                            <th>Clasificación</th>
                            <th class="text-end">Tickets Vendidos</th>
                            <th class="text-end">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movies as $movie)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($movie->poster_url)
                                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" width="50" class="me-3">
                                        @else
                                            <div class="bg-secondary rounded me-3" style="width: 50px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-film"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $movie->title }}</h6>
                                            <small class="text-muted">{{ $movie->director }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $movie->duration }} min</td>
                                <td>
                                    <span class="badge bg-primary">{{ $movie->classification }}</span>
                                </td>
                                <td class="text-end">
                                    {{ $movie->tickets_sold ?? 0 }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($movie->revenue ?? 0, 2) }}€
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay datos disponibles para este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection