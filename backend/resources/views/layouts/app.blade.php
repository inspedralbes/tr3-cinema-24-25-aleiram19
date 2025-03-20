<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard | Cinema Management')</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            /* Colores principales */
            --color-dark-blue: #0A2B4F;     /* Fondo principal */
            --color-mid-blue: #102C54;      /* Para degradados o fondos intermedios */
            --color-light-blue: #1C3A66;    /* Se usa en tarjetas */
            --color-accent: #00BCD4;        /* Acento (cian) */
            --color-secondary: #FFB300;    /* Amarillo/dorado */
            --color-tertiary: #F46D75;     /* Rosa/rojo */
            --color-success: #28a745;      /* Verde para éxito */
            --color-danger: #dc3545;       /* Rojo para errores */
            --color-warning: #ffc107;      /* Amarillo para advertencias */
            --color-info: #17a2b8;         /* Azul claro para info */
            --color-text: #FFFFFF;          /* Texto en fondos oscuros */
            
            --transition-speed: 0.3s;
        }

        /* Configuración base del documento */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--color-dark-blue);
            color: var(--color-text);
            display: flex;
            flex-direction: column;
        }

        /* Header con gradiente */
        header {
            background: linear-gradient(60deg, var(--color-mid-blue), var(--color-light-blue));
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 100;
        }
        header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        header nav .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        header nav .brand a {
            color: var(--color-text);
            text-decoration: none;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all var(--transition-speed);
        }
        header nav .brand a:hover {
            color: var(--color-accent);
        }
        header nav .nav-links {
            display: flex;
            gap: 20px;
        }
        header nav .nav-links a {
            color: var(--color-text);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all var(--transition-speed);
        }
        header nav .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--color-accent);
            transform: translateY(-2px);
        }
        header nav .nav-links a.active {
            background-color: var(--color-accent);
            color: var(--color-mid-blue);
            font-weight: 600;
        }

        /* Contenido principal */
        main {
            flex: 1; /* Para empujar el footer al final de la página */
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        /* Footer */
        footer {
            background-color: var(--color-mid-blue);
            color: var(--color-text);
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.2);
        }
        
        footer p {
            margin: 0;
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Contenedor de tarjetas (cards) */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        /* Tarjetas */
        .content-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: var(--color-light-blue);
            padding: 30px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--color-text);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            border-left: 5px solid transparent;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
        }
        .content-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        .content-link i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        /* Colores de borde para cada sección */
        .content-link.user {
            border-left-color: var(--color-accent);
        }
        .content-link.movie {
            border-left-color: #FFB300; /* Ejemplo de otro acento (amarillo) */
        }
        .content-link.seat {
            border-left-color: #F46D75; /* Ejemplo de acento en rosa/rojo */
        }

        /* Estilos para formularios */
        .custom-form-card {
            background: linear-gradient(135deg, var(--color-mid-blue), var(--color-light-blue));
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            color: var(--color-text);
        }
        
        .custom-form-card .card-header {
            background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            position: relative;
        }
        
        .custom-form-card .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background-color: var(--color-accent);
        }
        
        .custom-form-card .card-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .custom-form-card .card-body {
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .custom-form-card label {
            color: var(--color-text);
            font-weight: 500;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        
        .custom-form-card .form-control,
        .custom-form-card .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--color-text);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all var(--transition-speed);
        }
        
        .custom-form-card .form-control:focus,
        .custom-form-card .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--color-accent);
            box-shadow: 0 0 0 0.25rem rgba(0, 188, 212, 0.25);
        }
        
        .custom-form-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .custom-form-card .form-text {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .custom-form-card .is-invalid {
            border-color: var(--color-danger);
        }
        
        .custom-form-card .invalid-feedback {
            color: var(--color-tertiary);
        }
        
        .custom-form-card .alert {
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .custom-form-card .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
        }
        
        .custom-form-card .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #5cde78;
        }
        
        .custom-form-card .alert-success::before {
            background-color: var(--color-success);
        }
        
        .custom-form-card .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: #ef7783;
        }
        
        .custom-form-card .alert-danger::before {
            background-color: var(--color-danger);
        }
        
        .custom-form-card .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all var(--transition-speed);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .custom-form-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .custom-form-card .btn-primary {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: var(--color-dark-blue);
        }
        
        .custom-form-card .btn-primary:hover {
            background-color: #00d8f4;
            border-color: #00d8f4;
        }
        
        .custom-form-card .btn-secondary {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: transparent;
        }
        
        .custom-form-card .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        /* Estilos para tablas */
        .data-table-container {
            background: linear-gradient(135deg, var(--color-mid-blue), var(--color-light-blue));
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }
        
        .data-table-container .table-header {
            background: linear-gradient(90deg, rgba(0, 188, 212, 0.2), transparent);
            padding: 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        
        .data-table-container .table-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background-color: var(--color-accent);
        }
        
        .data-table-container .table-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--color-text);
            letter-spacing: 0.5px;
        }
        
        .data-table-container .table-header .btn-primary {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: var(--color-dark-blue);
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all var(--transition-speed);
        }
        
        .data-table-container .table-header .btn-primary:hover {
            background-color: #00d8f4;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .data-table-container .table-content {
            padding: 20px 30px;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .data-table-container .table {
            color: var(--color-text);
            margin-bottom: 0;
        }
        
        .data-table-container .table th {
            background-color: var(--color-light-blue);
            padding: 15px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--color-accent);
            vertical-align: middle;
        }
        
        .data-table-container .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: rgba(255, 255, 255, 0.05);
        }
        
        .data-table-container .table tr {
            transition: all var(--transition-speed);
        }
        
        .data-table-container .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .data-table-container .table .btn-group {
            display: flex;
            gap: 8px;
        }
        
        .data-table-container .table .btn {
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all var(--transition-speed);
            padding: 5px 12px;
        }
        
        .data-table-container .table .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .data-table-container .table .btn-info {
            background-color: var(--color-info);
            border-color: var(--color-info);
            color: white;
        }
        
        .data-table-container .table .btn-primary {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: var(--color-dark-blue);
        }
        
        .data-table-container .table .btn-danger {
            background-color: var(--color-danger);
            border-color: var(--color-danger);
        }
        
        .data-table-container .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 6px;
        }
        
        /* Ajustes responsivos */
        @media (max-width: 991px) {
            header nav {
                flex-direction: column;
                gap: 15px;
            }
            
            header nav .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .data-table-container .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .custom-form-card label {
                text-align: left;
            }
        }
        
        @media (max-width: 768px) {
            .data-table-container .table thead {
                display: none;
            }
            
            .data-table-container .table tbody tr {
                display: block;
                margin-bottom: 20px;
                background-color: rgba(255, 255, 255, 0.03);
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }
            
            .data-table-container .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 12px 15px;
            }
            
            .data-table-container .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--color-accent);
                text-align: left;
            }
            
            .data-table-container .table .btn-group {
                justify-content: flex-end;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            header nav .brand a {
                font-size: 1.4rem;
            }
            
            header nav .nav-links a {
                font-size: 0.9rem;
                padding: 6px 12px;
            }
            
            .custom-form-card .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .content-link {
                font-size: 1.1rem;
            }
            
            .content-link i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="brand">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-film"></i> Cinema Management
                </a>
            </div>
            <div class="nav-links">
                <a href="{{ route('movies.index') }}" class="{{ request()->routeIs('movies.*') ? 'active' : '' }}">
                    <i class="fas fa-film me-1"></i> Películas
                </a>
                <a href="{{ route('screenings.index') }}" class="{{ request()->routeIs('screenings.*') ? 'active' : '' }}">
                    <i class="fas fa-video me-1"></i> Sessiones
                </a>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-1"></i> Usuarios
                </a>
                <a href="{{ route('seats.index') }}" class="{{ request()->routeIs('seats.*') ? 'active' : '' }}">
                    <i class="fas fa-chair me-1"></i> Asientos
                </a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>© {{ date('Y') }} Cinema Management System | Desarrollado con <i class="fas fa-heart text-danger"></i></p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
