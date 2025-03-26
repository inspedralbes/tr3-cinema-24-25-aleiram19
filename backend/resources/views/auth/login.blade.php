<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Cinema</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
  <style>
    :root {
      /* Colores principales actualizados */
      --navy-600: #0078C8;  /* Inicio del gradiente navy */
      --navy-900: #051D40;  /* Fin del gradiente navy */
      --blue-400: #00A0E4;   /* Inicio del gradiente blue */
      --blue-900: #051D40;   /* Fin del gradiente blue */
      --color-text: #FFFFFF;
      --transition-speed: 0.3s;
    }
    body {
      background: linear-gradient(60deg, var(--navy-600), var(--navy-900));
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--color-text);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }
    .login-container {
      max-width: 400px;
      width: 100%;
      padding: 30px;
      background: linear-gradient(135deg, var(--blue-400), var(--blue-900));
      border-radius: 10px;
      box-shadow: 0px 8px 30px rgba(0,0,0,0.3);
      border-left: 5px solid var(--blue-400);
    }
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-header h1 {
      color: var(--color-text);
      font-weight: bold;
      margin-bottom: 5px;
    }
    .login-header p {
      color: var(--blue-400);
      font-size: 1.1rem;
    }
    .login-header i {
      font-size: 3rem;
      color: var(--blue-400);
      margin-bottom: 15px;
    }
    .form-control, .form-check-input {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: var(--color-text);
      border-radius: 8px;
      padding: 12px 15px;
      transition: all var(--transition-speed);
    }
    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: var(--blue-400);
      box-shadow: 0 0 0 0.25rem rgba(0, 160, 228, 0.25);
      color: var(--color-text);
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .form-label {
      color: var(--color-text);
      font-weight: 500;
    }
    .btn-primary {
      background-color: var(--blue-400);
      border-color: var(--blue-400);
      color: var(--navy-900);
      font-weight: 600;
      padding: 12px;
      transition: all var(--transition-speed);
    }
    .alert-danger {
      background-color: rgba(220, 53, 69, 0.2);
      color: #ef7783;
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      position: relative;
      overflow: hidden;
    }
    .alert-danger::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
      background-color: #F46D75;
    }
    .form-check-label {
      color: var(--color-text);
    }
    .form-check-input:checked {
      background-color: var(--blue-400);
      border-color: var(--blue-400);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Cinema Admin</h1>
      <p>Panel de Administración</p>
    </div>
    
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    
    <form action="{{ route('admin.login') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
        </button>
      </div>
    </form>
  </div>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
