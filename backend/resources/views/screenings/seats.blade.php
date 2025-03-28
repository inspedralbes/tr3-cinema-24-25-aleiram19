@extends('layouts.app')

@section('title', 'Asientos de Proyección')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="custom-form-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-chair me-2"></i> Asientos para "{{ $screening->movie->title }}"</h2>
                    <div>
                        <a href="{{ route('screenings.show', $screening->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver a Detalles
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <h4 class="mb-0 me-3">Información de la Sesión:</h4>
                                @if($screening->is_special)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-star me-1"></i> Día del Espectador
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">Sesión Normal</span>
                                @endif
                            </div>
                            <p class="mt-3">
                                <strong>Auditorio:</strong> {{ $auditorium->name }}<br>
                                <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($screening->date_time)->format('d/m/Y') }}<br>
                                <strong>Hora:</strong> {{ \Carbon\Carbon::parse($screening->date_time)->format('H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100" style="background: rgba(0,0,0,0.2); border: none; border-radius: 10px;">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-tags me-2"></i> Precios</h5>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <div class="p-2 rounded mb-2" style="background: rgba(255,255,255,0.1);">
                                                <p class="mb-1">Normal</p>
                                                <p class="h4 mb-0" style="color: var(--color-accent);">{{ $screening->is_special ? '4,00 €' : '6,00 €' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 rounded" style="background: rgba(255,255,255,0.1);">
                                                <p class="mb-1">VIP (Fila F)</p>
                                                <p class="h4 mb-0" style="color: var(--color-secondary);">{{ $screening->is_special ? '6,00 €' : '8,00 €' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                        <h4 class="mb-3"><i class="fas fa-film me-2"></i> Pantalla</h4>
                        <div style="height: 10px; background: linear-gradient(to bottom, var(--color-accent), transparent); width: 80%; margin: 0 auto; border-top-left-radius: 50%; border-top-right-radius: 50%;"></div>
                    </div>

                    <div class="seats-container py-4">
                        @foreach($seatsByRow as $row => $seats)
                            <div class="row-container mb-3 d-flex justify-content-center">
                                <div class="row-label me-3 d-flex align-items-center">
                                    <span class="badge {{ $row === 'F' ? 'bg-warning text-dark' : 'bg-secondary' }} p-2">
                                        {{ $row }}
                                    </span>
                                </div>
                                <div class="seats-row d-flex gap-2">
                                    @foreach($seats as $seat)
                                        <div class="seat-item">
                                            <button class="btn {{ $seat['is_vip'] ? 'btn-warning' : 'btn-secondary' }} position-relative {{ $seat['status'] == 'occupied' ? 'disabled opacity-50' : '' }}" 
                                                style="width: 50px; height: 50px; border-radius: 8px;" 
                                                title="{{ $seat['number'] }} - {{ $seat['price'] }}€ {{ $seat['status'] == 'occupied' ? '(Ocupado)' : '' }}">
                                                {{ substr($seat['number'], 1) }}
                                                @if($seat['is_vip'])
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                                                    VIP
                                                </span>
                                                @endif
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 10px;">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Leyenda</h5>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 30px; height: 30px; background-color: var(--bs-secondary); border-radius: 5px;"></div>
                                <span>Asiento Normal</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 30px; height: 30px; background-color: var(--bs-warning); border-radius: 5px;"></div>
                                <span>Asiento VIP (Fila F)</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 30px; height: 30px; background-color: var(--bs-secondary); opacity: 0.5; border-radius: 5px;"></div>
                                <span>Asiento Ocupado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection