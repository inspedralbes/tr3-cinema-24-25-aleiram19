<template>
  <div class="admin-layout">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <NuxtLink class="navbar-brand" to="/admin">
          <i class="bi bi-film me-2"></i>Cinema Admin
        </NuxtLink>
        
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#adminNavbar" 
          aria-controls="adminNavbar" 
          aria-expanded="false" 
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin" exact-active-class="active">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin/movies" active-class="active">
                <i class="bi bi-film me-1"></i> Películas
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin/genres" active-class="active">
                <i class="bi bi-tags me-1"></i> Géneros
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin/screenings" active-class="active">
                <i class="bi bi-calendar-event me-1"></i> Sesiones
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin/auditoriums" active-class="active">
                <i class="bi bi-building me-1"></i> Auditorios
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/admin/reports" active-class="active">
                <i class="bi bi-bar-chart me-1"></i> Informes
              </NuxtLink>
            </li>
          </ul>
          
          <div class="d-flex">
            <div class="nav-item dropdown">
              <a 
                class="nav-link dropdown-toggle text-white" 
                href="#" 
                id="userDropdown" 
                role="button" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
              >
                <i class="bi bi-person-circle me-1"></i> Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <NuxtLink class="dropdown-item" to="/profile">
                    <i class="bi bi-person me-1"></i> Perfil
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink class="dropdown-item" to="/">
                    <i class="bi bi-house me-1"></i> Ir a Web Pública
                  </NuxtLink>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="logout">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
    
    <div class="content-container">
      <slot />
    </div>
    
    <footer class="footer bg-dark text-white py-3 mt-auto">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
          <span>&copy; 2024 Cinema Admin</span>
          <span>Versión 1.0</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'AdminLayout',
  
  methods: {
    logout() {
      // Implementar lógica de cierre de sesión
      // Por ejemplo, usando el store de auth:
      // this.$store.dispatch('auth/logout');
      
      // Por ahora, solo redirigimos
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content-container {
  flex: 1;
  background-color: #f8f9fa;
}

.navbar-brand {
  font-weight: bold;
}

.footer {
  font-size: 0.9rem;
}
</style>