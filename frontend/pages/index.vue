<template>
  <div class="home-page">
    <!-- Hero/Banner -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center text-white">
            <h1 class="display-4">Cinema Experience</h1>
            <p class="lead">Disfruta de las mejores películas en nuestras salas</p>
            <div class="mt-4">
              <NuxtLink to="/movies" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-film me-2"></i> Ver Películas
              </NuxtLink>
              <NuxtLink to="/screenings" class="btn btn-outline-light btn-lg">
                <i class="bi bi-calendar-event me-2"></i> Cartelera
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Películas en Cartelera -->
    <section class="current-movies py-5">
      <div class="container">
        <h2 class="section-title mb-4">Ahora en Cartelera</h2>
        
        <MovieGrid 
          title="Películas en Cartelera" 
          :currentOnly="true" 
        />
      </div>
    </section>
    
    <!-- Géneros -->
    <section class="genres-section py-5 bg-light">
      <div class="container">
        <h2 class="section-title mb-4">Explora por Género</h2>
        
        <GenreList @select="goToGenre" />
      </div>
    </section>
    
    <!-- Promociones -->
    <section class="promo-section py-5">
      <div class="container">
        <h2 class="section-title mb-4">Promociones</h2>
        
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card h-100 promo-card">
              <div class="card-body text-center">
                <i class="bi bi-calendar2-heart promo-icon text-primary"></i>
                <h4 class="card-title">Martes 2x1</h4>
                <p class="card-text">Todos los martes, compra una entrada y lleva otra gratis.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card h-100 promo-card">
              <div class="card-body text-center">
                <i class="bi bi-person-bounding-box promo-icon text-primary"></i>
                <h4 class="card-title">Tarjeta Premium</h4>
                <p class="card-text">Hazte miembro premium y disfruta de beneficios exclusivos.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="card h-100 promo-card">
              <div class="card-body text-center">
                <i class="bi bi-cup-straw promo-icon text-primary"></i>
                <h4 class="card-title">Combo Familiar</h4>
                <p class="card-text">Palomitas grandes + 4 refrescos a un precio especial.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Newsletter -->
    <section class="newsletter-section py-5 bg-dark text-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h3>Suscríbete a nuestro newsletter</h3>
            <p>Recibe las últimas novedades, estrenos y promociones exclusivas.</p>
          </div>
          <div class="col-lg-6">
            <div class="input-group">
              <input type="email" class="form-control" placeholder="Tu correo electrónico">
              <button class="btn btn-primary" type="button">Suscribirse</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import MovieGrid from '~/components/public/MovieGrid.vue';
import GenreList from '~/components/public/GenreList.vue';

export default {
  name: 'HomePage',
  
  components: {
    MovieGrid,
    GenreList
  },
  
  head() {
    return {
      title: 'Cinema - Disfruta de las mejores películas'
    };
  },
  
  methods: {
    goToGenre(genreId) {
      if (genreId) {
        this.$router.push(`/movies?genre=${genreId}`);
      } else {
        this.$router.push('/movies');
      }
    }
  }
};
</script>

<style scoped>
.hero-section {
  position: relative;
  background-image: url('/images/cinema-hero.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 70vh;
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 3rem;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
}

.section-title {
  position: relative;
  display: inline-block;
  margin-bottom: 2rem;
  font-weight: 700;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  width: 50px;
  height: 3px;
  background-color: var(--bs-primary);
}

.promo-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: none;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.promo-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.promo-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.newsletter-section {
  background-image: linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('/images/cinema-seats.jpg');
  background-size: cover;
  background-position: center;
}
</style>