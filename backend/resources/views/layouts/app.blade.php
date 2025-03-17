<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard | Mi Aplicación')</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    <style>
        :root {
            /* Colores principales */
            --color-dark-blue: #0A2B4F;     /* Fondo principal */
            --color-mid-blue: #102C54;      /* Para degradados o fondos intermedios */
            --color-light-blue: #1C3A66;    /* Se usa en tarjetas */
            --color-accent: #00BCD4;        /* Acento (cian) */
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
            text-align: center;
        }
        header nav a {
            color: var(--color-text);
            text-decoration: none;
            font-size: 2rem;
            font-weight: bold;
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
            padding: 20px;
            margin-top: 20px;
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

        /* Ajustes responsivos menores (opcional) */
        @media (max-width: 480px) {
            header nav a {
                font-size: 1.5rem;
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
            <a href="{{ route('admin.dashboard') }}">Panel de Administración</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>© {{ date('Y') }} Mi Aplicación</p>
    </footer>
</body>
</html>
