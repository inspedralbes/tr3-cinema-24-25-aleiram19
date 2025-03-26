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
      /* Paleta de colores mejorada */
      --navy-600: #0078C8;  /* Inicio del gradiente navy */
      --navy-900: #051D40;  /* Fin del gradiente navy */
      --blue-400: #00A0E4;  /* Inicio del gradiente blue */
      --blue-900: #051D40;  /* Fin del gradiente blue */
      --color-text: #FFFFFF;
      --color-secondary: #00A0E4;
      --color-tertiary: #0078C8;
      --color-light-text: #F0F8FF;  /* Texto claro con mejor contraste */
      --color-dark-blue: #051D40;  /* Azul oscuro para contrastar */
      --transition-speed: 0.3s;
    }

    /* Reset y estilos base */
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(60deg, var(--navy-600), var(--navy-900));
      color: var(--color-light-text);
      font-size: 16px;
      letter-spacing: 0.3px;
      line-height: 1.6;
    }

    /* Contenedor principal: Sidebar y contenido */
    .container-flex {
      display: flex;
      height: 100vh;
    }

    /* Sidebar lateral */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, var(--blue-400), var(--blue-900));
      padding: 20px;
      box-shadow: 2px 0 12px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      gap: 20px;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1000;
    }
    .sidebar .brand a {
      color: var(--color-accent);
      text-decoration: none;
      font-size: 1.8rem;
      font-weight: bold;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 15px;
      transition: all var(--transition-speed);
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    .sidebar nav {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .sidebar nav a {
      color: var(--color-light-text);
      text-decoration: none;
      font-size: 1.1rem;
      font-weight: 500;
      padding: 10px 15px;
      border-radius: 8px;
      transition: all var(--transition-speed);
      border-left: 3px solid transparent;
      display: flex;
      align-items: center;
    }
    .sidebar nav a.active {
      background-color: var(--blue-400);
      color: var(--color-dark-blue);
      font-weight: 600;
      border-left-color: var(--color-accent);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    .sidebar nav a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    .sidebar form button {
      width: 100%;
      border: none;
      background: var(--navy-900);
      color: var(--color-text);
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 1rem;
      transition: all var(--transition-speed);
    }

    /* Contenido principal */
    .content {
      margin-left: 250px; /* igual al ancho del sidebar */
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1;
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
    }
    footer {
      background-color: var(--blue-400);
      color: var(--color-light-text);
      text-align: center;
      padding: 30px 20px;
      box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.2);
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    
    /* Estilos para tablas y contenedores de datos */
    .data-table-container {
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      backdrop-filter: blur(10px);
      margin-bottom: 30px;
    }
    .table-header {
      background-color: rgba(0, 160, 228, 0.2);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .table-header h2 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--color-light-text);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .table-header h2 i {
      color: var(--color-accent);
    }
    .table-content {
      padding: 20px;
    }
    .table {
      color: var(--color-light-text);
      border-collapse: separate;
      border-spacing: 0 5px;
    }
    .table thead th {
      background-color: rgba(0, 120, 200, 0.3);
      border-bottom: 2px solid var(--color-accent);
      font-weight: 600;
      padding: 12px 15px;
      color: var(--color-light-text);
      font-size: 0.95rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .table tbody tr {
      background-color: rgba(255, 255, 255, 0.05);
      transition: all var(--transition-speed);
    }
    .table tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .table td {
      padding: 12px 15px;
      border-top: none;
      vertical-align: middle;
    }
    .badge {
      padding: 6px 10px;
      font-weight: 500;
      font-size: 0.85rem;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Estilos para cards, tablas y formularios */
    .cards-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      padding: 20px;
    }
    .content-link {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: var(--navy-900);
      padding: 30px;
      border-radius: 10px;
      text-decoration: none;
      color: var(--color-text);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
      transition: transform var(--transition-speed), box-shadow var(--transition-speed);
      border-left: 5px solid var(--blue-400);
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
    /* Si se desea diferenciar, se puede conservar la misma paleta en todas las secciones */
    .content-link.user,
    .content-link.movie,
    .content-link.seat {
      border-left-color: var(--blue-400);
    }

    /* Botones y formularios */
    .btn {
      padding: 8px 16px;
      font-weight: 500;
      border-radius: 6px;
      transition: all var(--transition-speed);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .btn-primary {
      background: linear-gradient(45deg, var(--blue-400), var(--navy-600));
      border: none;
    }
    .btn-danger {
      background: linear-gradient(45deg, #e74c3c, #c0392b);
      border: none;
    }
    .btn-info {
      background: linear-gradient(45deg, #3498db, #2980b9);
      border: none;
    }
    .btn-sm {
      padding: 5px 10px;
      font-size: 0.85rem;
    }
    
    /* Alertas */
    .alert {
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      border-left: 4px solid transparent;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .alert-success {
      background-color: rgba(46, 204, 113, 0.2);
      border-left-color: #2ecc71;
    }
    .alert-danger {
      background-color: rgba(231, 76, 60, 0.2);
      border-left-color: #e74c3c;
    }
    
    /* Ajustes responsivos */
    @media (max-width: 992px) {
      .sidebar {
        width: 220px;
        padding: 15px;
      }
      .content {
        margin-left: 220px;
      }
      .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
      .table-header a {
        align-self: flex-start;
      }
    }
    
    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .content {
        margin-left: 200px;
      }
      .card {
        margin-bottom: 20px;
      }
      .table td[data-label]:before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        margin-right: 10px;
      }
      .table thead {
        display: none;
      }
      .table tbody tr {
        display: block;
        margin-bottom: 15px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        overflow: hidden;
      }
      .table tbody td {
        display: block;
        text-align: right;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      }
      .table tbody td:last-child {
        border-bottom: none;
      }
    }
    
    @media (max-width: 576px) {
      .sidebar {
        width: 70px;
        padding: 10px;
      }
      .sidebar .brand a {
        font-size: 1.2rem;
        justify-content: center;
        padding: 5px;
      }
      .sidebar .brand a span {
        display: none;
      }
      .sidebar nav a {
        padding: 10px 0;
        justify-content: center;
      }
      .sidebar nav a span {
        display: none;
      }
      .sidebar nav a i {
        margin-right: 0;
        font-size: 1.2rem;
      }
      .content {
        margin-left: 70px;
      }
    }
  </style>
</head>
<body>
  <div class="container-flex">
    <!-- Sidebar lateral -->
    <div class="sidebar">
      <div class="brand">
        <a href="{{ route('admin.dashboard') }}">
          <span>Cinema Management</span>
        </a>
      </div>
      <nav>
        <a href="{{ route('movies.index') }}" class="{{ request()->routeIs('movies.*') ? 'active' : '' }}">
          <i class="fas fa-film me-1"></i> Películas
        </a>
        <a href="{{ route('screenings.index') }}" class="{{ request()->routeIs('screenings.*') ? 'active' : '' }}">
          <i class="fas fa-video me-1"></i> Sesiones
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
          <i class="fas fa-users me-1"></i> Usuarios
        </a>
        <a href="{{ route('seats.index') }}" class="{{ request()->routeIs('seats.*') ? 'active' : '' }}">
          <i class="fas fa-chair me-1"></i> Asientos
        </a>
      </nav>
      <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit">
          <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
        </button>
      </form>
    </div>
    
    <!-- Contenido principal -->
    <div class="content">
      <main>
        @yield('content')
      </main>
      <footer>
        <p>© {{ date('Y') }} CineXperience Management System</p>
      </footer>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
