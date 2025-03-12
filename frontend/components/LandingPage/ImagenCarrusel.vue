<template>
  <div>
    <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-indicators">
        <button v-for="(movie, index) in moviesList" 
                :key="`indicator-${movie.id}`"
                type="button" 
                data-bs-target="#mainCarousel" 
                :data-bs-slide-to="index" 
                :class="{ 'active': index === 0 }"
                :aria-current="index === 0 ? 'true' : 'false'"
                :aria-label="`Slide ${index + 1}`"></button>
      </div>
      
      <div class="carousel-inner">
        <div 
          v-for="(movie, index) in moviesList" 
          :key="movie.id" 
          :class="['carousel-item', { 'active': index === 0 }]"
        >
          <div class="carousel-image-container">
            <img 
              :src="movie.image" 
              class="d-block w-100" 
              :alt="movie.title"
            >
            <div class="overlay-gradient"></div>
          </div>
          <div class="carousel-caption d-flex flex-column align-items-start justify-content-center text-start">
            <div class="content-wrapper">
              <span class="badge-featured mb-3">{{ movie.badge }}</span>
              <h1 class="movie-title">{{ movie.title }}</h1>
              <p class="movie-genre">{{ movie.genre }}</p>
              <div class="movie-details">
                <span><i class="fas fa-clock"></i> {{ movie.duration }}</span>
                <span><i class="fas fa-star"></i> {{ movie.rating }}</span>
              </div>
              <div class="action-buttons">
                <button class="btn btn-play me-3">
                  <i class="fas fa-play-circle"></i> Ver Trailer
                </button>
                <button class="btn btn-buy-tickets">
                  <i class="fas fa-ticket-alt"></i> Comprar Entradas
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      moviesList: [
        { 
          id: 1, 
          title: "Bridget Jones: Loca por él", 
          genre: "Comedia/Romance", 
          image: "/img/img1.jpg", 
          badge: "¡YA EN TU CINE!",
          duration: "105 min",
          rating: "8.2/10"
        },
        { 
          id: 2, 
          title: "Mickey 17", 
          genre: "Ciencia Ficción/Aventura", 
          image: "/img/img2.jpg",
          badge: "ESTRENO EXCLUSIVO",
          duration: "142 min",
          rating: "8.7/10"
        },
        { 
          id: 3, 
          title: "Anora", 
          genre: "Drama", 
          image: "/img/img3.jpg",
          badge: "PREMIO DE CANNES",
          duration: "116 min",
          rating: "9.0/10"
        },
        { 
          id: 4, 
          title: "Paddington: Aventura en la Selva", 
          genre: "Familiar/Aventura", 
          image: "/img/img4.jpeg",
          badge: "PRÓXIMAMENTE",
          duration: "98 min",
          rating: "7.9/10"
        }
      ]
    }
  }
}
</script>

<style scoped>
/* Carousel Container */
.carousel {
  height: 90vh;
  overflow: hidden;
  position: relative;
}

/* Image Container */
.carousel-image-container {
  width: 100%;
  height: 90vh;
  overflow: hidden;
  position: relative;
}

.carousel-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transform: scale(1.05);
  transition: transform 10s ease-in-out;
}

.carousel-item.active .carousel-image-container img {
  transform: scale(1);
}

/* Gradient overlay para que el texto sea más legible */
.overlay-gradient {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(0, 5, 35, 0.9) 0%,
    rgba(0, 5, 35, 0.7) 30%,
    rgba(0, 5, 35, 0.4) 60%,
    rgba(0, 5, 35, 0.2) 80%,
    rgba(0, 5, 35, 0) 100%
  );
}

/* Caption styles */
.carousel-caption {
  left: 10%;
  right: auto;
  bottom: 0;
  top: 0;
  transform: none;
  width: 60%;
  max-width: 800px;
  text-align: left;
  padding: 0;
}

.content-wrapper {
  position: absolute;
  bottom: 25%;
  width: 80%;
}

.badge-featured {
  display: inline-block;
  background-color: #0078C8;
  padding: 8px 15px;
  font-size: 0.85rem;
  font-weight: bold;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.movie-title {
  font-size: 4rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  color: white;
}

.movie-genre {
  font-size: 1.3rem;
  margin-bottom: 1rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
  color: #f0f0f0;
}

.movie-details {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  font-size: 1.1rem;
}

.movie-details span {
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-buttons {
  display: flex;
  margin-top: 20px;
}

.btn-play {
  background-color: transparent;
  border: 2px solid white;
  color: white;
  padding: 12px 22px;
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-play:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}

.btn-buy-tickets {
  background-color: #0078C8;
  border: none;
  color: white;
  padding: 14px 28px;
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-buy-tickets:hover {
  background-color: #00A0E4;
  transform: translateY(-3px);
  box-shadow: 0 4px 15px rgba(0, 160, 228, 0.3);
}

/* Indicadores de carrusel personalizados */
.carousel-indicators {
  bottom: 20px;
  margin-bottom: 2rem;
}

.carousel-indicators button {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: white;
  opacity: 0.5;
  transition: all 0.3s ease;
  margin: 0 5px;
}

.carousel-indicators button.active {
  opacity: 1;
  transform: scale(1.2);
  background-color: #0078C8;
}

/* Controles de carrusel */
.carousel-control-prev,
.carousel-control-next {
  width: 5%;
  opacity: 0.7;
  transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  opacity: 1;
  background-color: rgba(0, 0, 0, 0.2);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 40px;
  height: 40px;
}

/* Responsive */
@media (max-width: 1200px) {
  .movie-title {
    font-size: 3.5rem;
  }
}

@media (max-width: 992px) {
  .carousel-caption {
    width: 80%;
  }
  
  .movie-title {
    font-size: 3rem;
  }
}

@media (max-width: 768px) {
  .carousel {
    height: 80vh;
  }
  
  .carousel-image-container {
    height: 80vh;
  }

  .carousel-caption {
    width: 90%;
  }
  
  .movie-title {
    font-size: 2.5rem;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 15px;
  }
  
  .btn-play {
    margin-right: 0;
  }
}

@media (max-width: 576px) {
  .carousel {
    height: 70vh;
  }
  
  .carousel-image-container {
    height: 70vh;
  }
  
  .content-wrapper {
    bottom: 20%;
  }
  
  .movie-title {
    font-size: 2rem;
  }
  
  .movie-genre {
    font-size: 1rem;
  }
  
  .movie-details {
    font-size: 0.9rem;
  }
}
</style>