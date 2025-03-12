<template>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <!-- Logo y Nombre del Cine -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <span class="cinema-logo">CineXeperience</span>
      </a>
      
      <!-- Botón hamburguesa para móviles -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <!-- Botones del Navbar -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <!-- Cartelera -->
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-film me-1"></i> Cartelera
            </a>
          </li>
          <!-- Promociones -->
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-tags me-1"></i> Promociones
            </a>
          </li>
          <!-- Estrenos -->
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-star me-1"></i> Estrenos
            </a>
          </li>
          <!-- Experiencias -->
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-glasses me-1"></i> Experiencias
            </a>
          </li>
          <!-- Ubicaciones -->
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-map-marker-alt me-1"></i> Cines
            </a>
          </li>
          <!-- Registro/Login -->
          <li class="nav-item">
            <a class="nav-link btn-login" href="#">
              <i class="fas fa-user me-1"></i> Iniciar sesión
            </a>
          </li>
          <!-- Botón Comprar -->
          <li class="nav-item ms-2">
            <a class="btn btn-primary btn-comprar rounded-pill" href="#">
              COMPRAR ENTRADAS
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    }
  }
}
</script>

<style scoped>
.navbar {
  background: rgba(5, 29, 64, 0.1);
  backdrop-filter: blur(5px);
  box-shadow: none;
  padding: 20px 0;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1000;
  border-bottom: none;
}

.navbar.scrolled {
  background-color: rgba(5, 29, 64, 0.95);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  padding: 12px 0;
}

.navbar-brand {
  font-weight: bold;
  font-size: 1.8rem;
  transition: all 0.3s ease;
  color: white;
}

.cinema-logo {
  background: linear-gradient(90deg, #ffffff, #00A0E4);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.nav-link {
  padding: 8px 15px;
  font-weight: 500;
  color: white !important;
  position: relative;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.nav-link:hover {
  color: #00A0E4 !important;
  transform: translateY(-2px);
}

.nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  background-color: #00A0E4;
  transition: all 0.3s ease;
}

.nav-link:hover::after {
  width: 80%;
  left: 10%;
}

.btn-login {
  border-radius: 20px;
  transition: all 0.3s ease;
}

.btn-login:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.btn-comprar {
  background-color: #0078C8;
  border: none;
  font-weight: bold;
  padding: 8px 20px;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-comprar:hover {
  background-color: #00A0E4;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 160, 228, 0.3);
}

@media (max-width: 992px) {
  .navbar-nav {
    padding: 1rem 0;
  }
  
  .nav-item {
    margin-bottom: 0.5rem;
  }
  
  .btn-comprar {
    margin-top: 0.5rem;
    display: inline-block;
  }
  
  .navbar {
    background-color: rgba(5, 29, 64, 0.95);
  }
}
</style>