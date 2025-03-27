@extends('layouts.app')

@section('title', "Informe de Usuarios - $month $year")

@section('content')
<div class="container">
    <div class="data-table-container">
        <div class="table-header">
            <h2><i class="fas fa-users"></i> Informe de Usuarios: {{ $month }} {{ $year }}</h2>
            <div>
                <form action="{{ route('admin.reports.users') }}" method="GET" class="d-inline">
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
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h3 class="display-4 mb-3">{{ $newUsers }}</h3>
                            <p class="mb-0 text-muted">Nuevos Usuarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h3 class="display-4 mb-3">{{ count($activeUsers) }}</h3>
                            <p class="mb-0 text-muted">Usuarios Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h3 class="display-4 mb-3">{{ $totalUsers }}</h3>
                            <p class="mb-0 text-muted">Total Usuarios</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th class="text-end">Tickets Comprados</th>
                            <th class="text-end">Total Gastado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeUsers as $user)
                            @if($user->tickets_bought > 0)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-end">{{ $user->tickets_bought }}</td>
                                    <td class="text-end">{{ number_format($user->total_spent, 2) }}€</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay datos disponibles para este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection