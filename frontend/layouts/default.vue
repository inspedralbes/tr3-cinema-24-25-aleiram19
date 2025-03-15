<template>
  <div class="default-layout">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <NuxtLink class="navbar-brand" to="/">
          <i class="bi bi-film me-2"></i>Cinema
        </NuxtLink>
        
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#mainNavbar" 
          aria-controls="mainNavbar" 
          aria-expanded="false" 
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/" exact-active-class="active">
                Inicio
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/movies" active-class="active">
                Películas
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/screenings" active-class="active">
                Cartelera
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/about" active-class="active">
                Nosotros
              </NuxtLink>
            </li>
            <li class="nav-item">
              <NuxtLink class="nav-link" to="/contact" active-class="active">
                Contacto
              </NuxtLink>
            </li>
          </ul>
          
          <div class="d-flex">
            <template v-if="isLoggedIn">
              <div class="nav-item dropdown">
                <a 
                  class="nav-link dropdown-toggle text-white" 
                  href="#" 
                  id="userDropdown" 
                  role="button" 
                  data-bs-toggle="dropdown" 
                  aria-expanded="false"
                >
                  <i class="bi bi-person-circle me-1"></i> {{ userName }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li>
                    <NuxtLink class="dropdown-item" to="/profile">
                      <i class="bi bi-person me-1"></i> Mi Perfil
                    </NuxtLink>
                  </li>
                  <li>
                    <NuxtLink class="dropdown-item" to="/my-tickets">
                      <i class="bi bi-ticket-perforated me-1"></i> Mis Entradas
                    </NuxtLink>
                  </li>
                  <li v-if="isAdmin">
                    <NuxtLink class="dropdown-item" to="/admin">
                      <i class="bi bi-gear me-1"></i> Administración
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
            </template>
            
            <template v-else>
              <NuxtLink to="/login" class="btn btn-outline-light me-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
              </NuxtLink>
              <NuxtLink to="/register" class="btn btn-light">
                <i class="bi bi-person-plus me-1"></i> Registrarse
              </NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </nav>
    
    <main class="content-container">
      <slot />
    </main>
    
    <footer class="footer bg-dark text-white py-4 mt-auto">
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-4 mb-md-0">
            <h5>Cinema</h5>
            <p>Tu destino para una experiencia cinematográfica única.</p>
          </div>
          <div class="col-md-4 mb-4 mb-md-0">
            <h5>Enlaces Rápidos</h5>
            <ul class="list-unstyled">
              <li><NuxtLink to="/" class="text-white">Inicio</NuxtLink></li>
              <li><NuxtLink to="/movies" class="text-white">Películas</NuxtLink></li>
              <li><NuxtLink to="/screenings" class="text-white">Cartelera</NuxtLink></li>
              <li><NuxtLink to="/about" class="text-white">Nosotros</NuxtLink></li>
              <li><NuxtLink to="/contact" class="text-white">Contacto</NuxtLink></li>
            </ul>
          </div>
          <div class="col-md-4">
            <h5>Síguenos</h5>
            <div class="social-icons">
              <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-4"></i></a>
              <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-4"></i></a>
              <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-4"></i></a>
              <a href="#" class="text-white"><i class="bi bi-youtube fs-4"></i></a>
            </div>
          </div>
        </div>
        <hr class="bg-light">
        <div class="d-flex flex-column flex-md-row justify-content-between">
          <div>&copy; 2024 Cinema. Todos los derechos reservados.</div>
          <div>
            <NuxtLink to="/privacy" class="text-white me-3">Privacidad</NuxtLink>
            <NuxtLink to="/terms" class="text-white">Términos de Uso</NuxtLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'DefaultLayout',
  
  data() {
    return {
      // Estos valores deberían venir de un store de autenticación
      isLoggedIn: false,
      isAdmin: false,
      userName: 'Usuario'
    };
  },
  
  methods: {
    logout() {
      // Implementar lógica de cierre de sesión
      // Por ejemplo, usando el store de auth:
      // this.$store.dispatch('auth/logout');
      
      // Por ahora, solo actualizamos el estado
      this.isLoggedIn = false;
    }
  }
};
</script>

<style scoped>
.default-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content-container {
  flex: 1;
}

.navbar-brand {
  font-weight: bold;
}

.footer {
  font-size: 0.9rem;
}

.social-icons a:hover {
  opacity: 0.8;
}
</style>