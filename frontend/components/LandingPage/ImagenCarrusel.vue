<template>  
    <div>
        <!-- Carrusel -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" style="height: 80vh;">
      <div class="carousel-inner h-100">
        <div 
          v-for="(movie, index) in moviesList" 
          :key="movie.id" 
          :class="['carousel-item h-100', { 'active': index === 0 }]"
        >
          <img 
            :src="movie.image" 
            class="d-block w-100 h-100" 
            style="object-fit: cover;" 
            :alt="movie.title"
          >
          <div class="carousel-caption bg-dark bg-opacity-75 p-5 rounded">
            <h1 class="display-4">Bienvenido a CineXperience</h1>
          </div>
        </div>
      </div>
      <!-- Controles -->
      <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
    </div>
</template>

<script setup>
const moviesList = [
  { id: 1, title: "Minecraft", genre: "Ciencia Ficción", image: "/img/img1.jpeg" },
  { id: 2, title: "Capitan America", genre: "Acción", image: "/img/img2.jpg" },
  { id: 3, title: "Anora", genre: "Drama", image: "/img/img3.jpeg" },
  { id: 4,title: "Paddington", genre: "Fantasía", image: "/img/img4.jpg" }
];

</script>