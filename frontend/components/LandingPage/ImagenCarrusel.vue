<template>
  <div>
    <NavBar />
    
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
          </div>
          <div class="carousel-caption bg-dark bg-opacity-75 p-5 rounded">
            <h1 class="display-4">{{ movie.title }}</h1>
            <p class="lead">{{ movie.genre }}</p>
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

<script setup>
// Importar el componente NavBar
import NavBar from '@/components/LandingPage/NavBar.vue';

const moviesList = [
  { id: 1, title: "Dune: Parte Dos", genre: "Ciencia Ficción", image: "/img/img1.jpg" },
  { id: 2, title: "Oppenheimer", genre: "Historia", image: "/img/img2.jpg" },
  { id: 3, title: "Spider-Man: No Way Home", genre: "Acción", image: "/img/img3.jpg" },
  { id: 4, title: "Avatar", genre: "Fantasía", image: "/img/img4.jpeg" }
];
</script>

<style scoped>
/* Hace que el carrusel ocupe toda la pantalla */
.carousel {
  height: 100vh;
  overflow: hidden;
}

/* Asegura que las imágenes ocupen toda la pantalla */
.carousel-image-container {
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.carousel-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Evita deformaciones */
  object-position: center;
}

/* Centra el texto en la imagen */
.carousel-caption {
  bottom: 50%;
  left: 50%;
  transform: translate(-50%, 50%);
  width: auto;
  min-width: 300px;
  max-width: 80%;
}

.carousel-indicators {
  bottom: 20px;
}

.carousel-control-prev,
.carousel-control-next {
  width: 5%;
  opacity: 0.8;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  opacity: 1;
}
</style>
