<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <!-- Nombre del Cine -->
        <a class="navbar-brand" href="#">CineXeperience</a>
        
        <!-- Botones del Navbar -->
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav ms-auto">
            <!-- Cartelera -->
            <li class="nav-item">
              <a class="nav-link" href="#">Cartelera</a>
            </li>
            <!-- Promociones -->
            <li class="nav-item">
              <a class="nav-link" href="#">Promociones</a>
            </li>
            <!-- Registro -->
            <li class="nav-item">
              <a class="nav-link" href="#">Registro</a>
            </li>
            <!-- Ubicación -->
            <li class="nav-item">
              <a class="nav-link" href="#">Ubicación</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </template>
  
  <script setup>
  // No hay lógica en este componente por ahora.
  </script>
  
  <style scoped>
  /* Puedes personalizar los estilos aquí si lo deseas */
  .navbar {
    border-bottom: 2px solid #333;
  }
  
  .navbar-brand {
    font-weight: bold;
  }
  
  .nav-link {
    padding: 10px;
  }
  </style>
  