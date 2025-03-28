<template>
  <div class="admin-dashboard">
    <h1 class="mb-4">Panel de Administración</h1>
    
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">Películas</h5>
            <p class="card-text">Administración de películas, edición y eliminación.</p>
            <NuxtLink to="/admin/movies" class="btn btn-primary">
              <i class="bi bi-film me-2"></i> Gestionar Películas
            </NuxtLink>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">Géneros</h5>
            <p class="card-text">Administración de géneros de películas.</p>
            <NuxtLink to="/admin/genres" class="btn btn-primary">
              <i class="bi bi-tags me-2"></i> Gestionar Géneros
            </NuxtLink>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">Sesiones</h5>
            <p class="card-text">Administración de sesiones y proyecciones.</p>
            <NuxtLink to="/admin/screenings" class="btn btn-primary">
              <i class="bi bi-calendar-event me-2"></i> Gestionar Sesiones
            </NuxtLink>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">Auditorios</h5>
            <p class="card-text">Administración de salas y auditorios.</p>
            <NuxtLink to="/admin/auditoriums" class="btn btn-primary">
              <i class="bi bi-building me-2"></i> Gestionar Auditorios
            </NuxtLink>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">Informes</h5>
            <p class="card-text">Visualiza informes y estadísticas de ventas.</p>
            <NuxtLink to="/admin/reports" class="btn btn-primary">
              <i class="bi bi-bar-chart me-2"></i> Ver Informes
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  
  layout: 'admin', // Asumiendo que tienes un layout para admin
  
  middleware: ['auth', 'admin'], // Middleware para verificar que el usuario esté autenticado y sea admin
  
  head() {
    return {
      title: 'Panel de Administración - Cinema'
    };
  }
};
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
}

.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}
</style>