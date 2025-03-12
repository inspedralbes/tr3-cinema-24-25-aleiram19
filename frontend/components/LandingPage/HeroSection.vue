<template>
  <div class="bg-movies-section">
    <section class="container mx-auto py-20 px-4">
      <div class="section-header text-center mb-14">
        <h2 class="section-title">TOP PELÍCULAS</h2>
        <div class="divider mx-auto"></div>
      </div>
      
      <div class="movies-slider-container">
        <div class="movies-slider" :style="{ transform: showSecondSlide ? 'translateX(-100%)' : 'translateX(0%)' }">
          <!-- Slide 1 -->
          <div class="movies-slide">
            <div class="movies-grid">
              <div v-for="movie in moviesSlide1" :key="movie.id" class="movie-card">
                <div class="movie-poster-container">
                  <img :src="movie.image" :alt="movie.title" class="movie-poster">
                  <div class="play-button">
                    <i class="fas fa-play"></i>
                  </div>
                  <div v-if="movie.comingSoon" class="badge">Próximos</div>
                </div>
                <div class="movie-info">
                  <h3 class="movie-title">{{ movie.title }}</h3>
                  <p v-if="movie.subtitle" class="movie-subtitle">{{ movie.subtitle }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Slide 2 -->
          <div class="movies-slide">
            <div class="movies-grid">
              <div v-for="movie in moviesSlide2" :key="movie.id" class="movie-card">
                <div class="movie-poster-container">
                  <img :src="movie.image" :alt="movie.title" class="movie-poster">
                  <div class="play-button">
                    <i class="fas fa-play"></i>
                  </div>
                  <div v-if="movie.comingSoon" class="badge">Próximos</div>
                </div>
                <div class="movie-info">
                  <h3 class="movie-title">{{ movie.title }}</h3>
                  <p v-if="movie.subtitle" class="movie-subtitle">{{ movie.subtitle }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Controles de navegación -->
      <div class="navigation-controls">
        <button class="nav-btn prev-btn" @click="showFirstSlide" :disabled="!showSecondSlide">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="nav-btn next-btn" @click="showNextSlide" :disabled="showSecondSlide">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showSecondSlide: false,
      allMovies: [
        { 
          id: 1, 
          title: "EL CABALLERO OSCURO", 
          genre: "Acción", 
          image: "/img/img6.jpeg",
          duration: "152 min",
          rating: "9.0/10",
          comingSoon: false
        },
        { 
          id: 2, 
          title: "INTERSTELLAR", 
          genre: "Ciencia Ficción", 
          image: "/img/img7.jpg",
          duration: "169 min",
          rating: "8.7/10",
          comingSoon: false
        },
        { 
          id: 3, 
          title: "GLADIATOR", 
          genre: "Acción/Historia", 
          image: "/img/img8.jpg",
          duration: "155 min",
          rating: "8.5/10",
          comingSoon: false
        },
        { 
          id: 4, 
          title: "BEAUTY AND THE BEAST", 
          genre: "Fantasía/Romance", 
          image: "/img/img9.jpeg",
          duration: "129 min",
          rating: "7.9/10",
          comingSoon: true
        },
        { 
          id: 5, 
          title: "MICKEY 17", 
          genre: "Ciencia Ficción", 
          image: "/img/img5.jpeg",
          duration: "166 min",
          rating: "8.5/10",
          comingSoon: false
        },
        { 
          id: 6, 
          title: "WOLFGANG", 
          subtitle: "(EXTRAORDINARIO)",
          genre: "Drama/Música", 
          image: "/img/img10.jpg",
          duration: "132 min",
          rating: "9.0/10",
          comingSoon: false
        },
        { 
          id: 7, 
          title: "ANORA", 
          genre: "Drama", 
          image: "/img/img11.jpeg",
          duration: "125 min",
          rating: "8.8/10",
          comingSoon: false
        },
        { 
          id: 8, 
          title: "PADDINGTON:", 
          subtitle: "AVENTURA EN LA SELVA",
          genre: "Familiar/Aventura", 
          image: "/img/img12.jpg",
          duration: "95 min",
          rating: "7.9/10",
          comingSoon: true
        }
      ]
    };
  },
  computed: {
    moviesSlide1() {
      return this.allMovies.slice(0, 4);
    },
    moviesSlide2() {
      return this.allMovies.slice(4, 8);
    }
  },
  methods: {
    showNextSlide() {
      this.showSecondSlide = true;
    },
    showFirstSlide() {
      this.showSecondSlide = false;
    }
  }
}
</script>

<style scoped>
.bg-movies-section {
  background-color: #051D40;
  color: white;
  padding: 80px 0;
}

.section-title {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.divider {
  height: 4px;
  width: 100px;
  background: #0078C8;
  margin-top: 15px;
  margin-bottom: 50px;
  border-radius: 2px;
}

/* Contenedor de slides */
.movies-slider-container {
  width: 100%;
  position: relative;
  overflow: hidden;
}

.movies-slider {
  display: flex;
  transition: transform 0.5s ease;
  width: 100%;
}

.movies-slide {
  min-width: 100%;
  flex-shrink: 0;
}

/* Grid de películas - exactamente 4 columnas */
.movies-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
  width: 100%;
}

/* Tarjeta de película */
.movie-card {
  width: 100%;
  cursor: pointer;
  overflow: hidden;
}

/* Contenedor del póster */
.movie-poster-container {
  position: relative;
  width: 100%;
  /* Proporción 2:3 para pósters de cine */
  padding-top: 150%;
  overflow: hidden;
  border-radius: 6px;
  margin-bottom: 15px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.movie-poster {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.movie-card:hover .movie-poster {
  transform: scale(1.05);
}

/* Play button */
.play-button {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 50px;
  height: 50px;
  background-color: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-size: 20px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.movie-card:hover .play-button {
  opacity: 1;
}

/* Badge "Próximos" */
.badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #051D40;
  color: white;
  padding: 4px 10px;
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 4px;
  z-index: 10;
}

/* Información de la película */
.movie-info {
  padding: 10px 0;
}

.movie-title {
  color: white;
  font-size: 1.5rem;
  font-weight: 800;
  text-transform: uppercase;
  line-height: 1.2;
  margin: 0;
}

.movie-subtitle {
  color: white;
  font-size: 1.2rem;
  font-weight: 600;
  text-transform: uppercase;
  line-height: 1.2;
  margin: 5px 0 0 0;
}

/* Controles de navegación */
.navigation-controls {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 60px;
}

.nav-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.1);
  display: flex;
  justify-content: center;
  align-items: center;
  border: none;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.nav-btn:hover:not([disabled]) {
  background-color: #0078C8;
  transform: translateY(-3px);
}

.nav-btn[disabled] {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Compra rápida */
.quick-purchase-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.btn-comprar-tickets {
  background-color: #0078C8;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-comprar-tickets:hover {
  background-color: #00A0E4;
  transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 992px) {
  .movies-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  
  .movie-title {
    font-size: 1.2rem;
  }
  
  .movie-subtitle {
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .movies-grid {
    grid-template-columns: 1fr;
  }
}
</style>