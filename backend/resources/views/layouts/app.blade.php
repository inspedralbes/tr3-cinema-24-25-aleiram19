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
      /* Paleta de colores */
      --navy-900: #051D40;  /* Color primario */
      --blue-400: #00A0E4;  /* Se conserva para detalles si se desea */
      --color-text: #FFFFFF;
      --color-secondary: #00A0E4;
      --color-tertiary: #0078C8;
      --color-light-text: #F0F8FF;
      --color-dark-blue: #051D40;
      --transition-speed: 0.3s;
      --button-logout: #0078C8; 

    }

    /* Reset y estilos base */
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--navy-900);
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
      width: 260px;
      background: linear-gradient(135deg, var(--navy-900) 0%, rgba(7, 38, 80, 1) 100%);
      padding: 25px 20px;
      box-shadow: 3px 0 15px rgba(0, 0, 0, 0.4);
      display: flex;
      flex-direction: column;
      gap: 25px;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1000;
      border-right: 1px solid rgba(255, 255, 255, 0.07);
      transition: all 0.3s ease;
    }
    .sidebar .brand a {
      color: var(--color-text);
      text-decoration: none;
      font-size: 1.4rem;
      font-weight: bold;
      letter-spacing: 0.5px;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      transition: all var(--transition-speed);
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
      margin-bottom: 10px;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      max-width: 100%;
      text-align: center;
    }
    .sidebar .brand a i {
      font-size: 2rem;
      min-width: 35px;
      text-align: center;
      display: block;
      margin: 0 auto 8px;
      color: #00A0E4;
      background-color: rgba(255, 255, 255, 0.1);
      width: 50px;
      height: 50px;
      line-height: 50px;
      border-radius: 50%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .sidebar .brand .brand-text {
      display: inline-block;
      width: 100%;
      white-space: normal;
      word-wrap: break-word;
      line-height: 1.2;
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
      padding: 12px 18px;
      border-radius: 10px;
      transition: all var(--transition-speed);
      border-left: 3px solid transparent;
      display: flex;
      align-items: center;
      margin-bottom: 8px;
      position: relative;
      overflow: hidden;
    }
    .sidebar nav a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 0;
      background: rgba(0, 160, 228, 0.1);
      z-index: -1;
      transition: width 0.3s ease;
    }
    .sidebar nav a:hover::before {
      width: 100%;
    }
    .sidebar nav a.active {
      background: linear-gradient(90deg, rgba(0, 160, 228, 0.9) 0%, rgba(0, 120, 200, 0.9) 100%);
      color: white;
      font-weight: 600;
      border-left-color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      transform: translateX(5px);
    }
    .sidebar nav a i {
      margin-right: 12px;
      width: 22px;
      text-align: center;
      font-size: 1.2rem;
      transition: transform 0.3s ease;
    }
    .sidebar nav a:hover i {
      transform: scale(1.2);
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
      margin-left: 260px; /* igual al ancho del sidebar */
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
      color: var(--blue-400);
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
      border-bottom: 2px solid var(--blue-400);
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
      grid-template-columns: repeat(2, 1fr);
      gap: 40px;
      padding: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    @media (max-width: 992px) {
      .cards-container {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    
    @media (max-width: 768px) {
      .cards-container {
        grid-template-columns: 1fr;
      }
    }
    .content-link {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: rgba(5, 29, 64, 0.7);
      padding: 40px 30px;
      border-radius: 15px;
      text-decoration: none;
      color: var(--color-text);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      transition: all var(--transition-speed);
      text-align: center;
      font-size: 1.4rem;
      font-weight: bold;
      height: 220px;
      position: relative;
      overflow: hidden;
    }
    
    .content-link:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
    }
    
    .content-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: var(--blue-400);
      transition: height 0.3s ease;
    }
    
    .content-link:hover::after {
      height: 8px;
    }
    
    .content-link i {
      font-size: 3.5rem;
      margin-bottom: 20px;
      color: var(--blue-400);
      transition: transform 0.3s ease;
    }
    
    .content-link:hover i {
      transform: scale(1.2);
    }
    
    .content-link.user::after {
      background: #3498db;
    }
    
    .content-link.movie::after {
      background: #e74c3c;
    }
    
    .content-link.seat::after {
      background: #2ecc71;
    }
    
    .content-link.screening::after {
      background: #f39c12;
    }
    
    .content-link.report::after {
      background: #9b59b6;
    }
    
    .content-link.user i {
      color: #3498db;
    }
    
    .content-link.movie i {
      color: #e74c3c;
    }
    
    .content-link.seat i {
      color: #2ecc71;
    }
    
    .content-link.screening i {
      color: #f39c12;
    }
    
    .content-link.report i {
      color: #9b59b6;
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
      background: var(--navy-900);
      border: none;
    }
    .btn-danger {
      background: #e74c3c;
      border: none;
    }
    .btn-info {
      background: #3498db;
      border: none;
    }
    .btn-sm {
      padding: 5px 10px;
      font-size: 0.85rem;
    }
    .logout-btn {
        width: 100%;
        border: none;
        background: #e74c3c; /* Color rojo más llamativo */
        color: var(--color-text);
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        transition: all var(--transition-speed);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .logout-btn:hover {
        background: #c0392b; /* Color más oscuro al pasar el mouse */
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
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
      
      .sidebar .brand a {
        font-size: 1.4rem;
      }
      
      .sidebar nav a {
        font-size: 0.95rem;
        padding: 10px 15px;
      }
    }
    
    @media (max-width: 576px) {
      .sidebar {
        width: 70px;
        padding: 10px;
        overflow: hidden;
      }
      .sidebar:hover {
        width: 200px;
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.5);
      }
      .sidebar .brand a {
        font-size: 1.2rem;
        justify-content: center;
        padding: 5px;
      }
      .sidebar .brand a i {
        font-size: 1.5rem;
      }
      
      .sidebar .brand a .brand-text {
        display: block;
        width: 100%;
        text-align: center;
        font-size: 1rem;
      }
      .sidebar:hover .brand a .brand-text {
        display: block;
        font-size: 1.2rem;
      }
      .sidebar nav a {
        padding: 12px 0;
        justify-content: center;
      }
      .sidebar nav a span {
        display: none;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
      }
      .sidebar:hover nav a {
        justify-content: flex-start;
        padding: 12px 15px;
      }
      .sidebar:hover nav a span {
        display: inline;
        opacity: 1;
        transform: translateX(0);
      }
      .sidebar nav a i {
        margin-right: 0;
        font-size: 1.2rem;
      }
      .sidebar:hover nav a i {
        margin-right: 12px;
      }
      .content {
        margin-left: 70px;
      }
      
      .sidebar form button {
        overflow: hidden;
        white-space: nowrap;
        display: flex;
        justify-content: center;
        padding: 10px 0;
      }
      
      .sidebar form button i {
        margin-right: 0;
      }
      
      .sidebar:hover form button {
        justify-content: flex-start;
        padding: 10px 15px;
      }
      
      .sidebar:hover form button i {
        margin-right: 10px;
      }
      
      .sidebar .logout-btn span {
        display: none;
      }
      
      .sidebar:hover .logout-btn span {
        display: inline;
      }
    }
  </style>
</head>
<body>
  <div class="container-flex">
    <!-- Sidebar lateral -->
    <div class="sidebar">
      <div class="brand">
        <a href="{{ route('admin.dashboard') }}" title="Cinema Management">
          <i class="fas fa-ticket-alt"></i>
          <span class="brand-text">Cinema Management</span>
        </a>
      </div>
      <nav>
        <a href="{{ route('movies.index') }}" class="{{ request()->routeIs('movies.*') ? 'active' : '' }}">
          <i class="fas fa-film me-1"></i> <span>Películas</span>
        </a>
        <a href="{{ route('screenings.index') }}" class="{{ request()->routeIs('screenings.*') ? 'active' : '' }}">
          <i class="fas fa-video me-1"></i> <span>Sesiones</span>
        </a>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
          <i class="fas fa-users me-1"></i> <span>Usuarios</span>
        </a>
        <a href="{{ route('seats.index') }}" class="{{ request()->routeIs('seats.*') ? 'active' : '' }}">
          <i class="fas fa-chair me-1"></i> <span>Asientos</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <i class="fas fa-chart-line me-1"></i> <span>Informes</span>
        </a>
      </nav>
      <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn" style="background-color: #e74c3c;">
            <i class="fas fa-sign-out-alt me-1"></i> <span>Cerrar Sesión</span>
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
