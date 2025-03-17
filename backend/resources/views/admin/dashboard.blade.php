@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
    <div class="container">
        <div class="cards-container">
            <a href="{{ route('users.index') }}" class="content-link user">
                <i class="fas fa-users"></i>
                Usuarios
            </a>
            <a href="{{ route('movies.index') }}" class="content-link movie">
                <i class="fas fa-film"></i>
                Películas
            </a>
            <a href="{{ route('seats.index') }}" class="content-link seat">
                <i class="fas fa-chair"></i>
                Asientos
            </a>
        </div>
    </div>
@endsection
