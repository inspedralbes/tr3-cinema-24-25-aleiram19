<template>
  <div>
    <!-- Carrusel -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" style="height: 80vh;">
      <div class="carousel-inner h-100">
        <div 
          v-for="(movie, index) in movies" 
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

    <!-- Películas destacadas -->
    <section class="py-10 px-5">
      <h2 class="text-3xl font-bold text-center mb-5">Cartelera</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <div 
          v-for="movie in movies" 
          :key="movie.id" 
          class="bg-gray-800 text-white rounded-lg overflow-hidden hover:transform hover:scale-105 transition duration-300"
        >
          <img :src="movie.image" :alt="movie.title" class="w-full h-60 object-cover">
          <div class="p-3">
            <h3 class="text-xl font-semibold">{{ movie.title }}</h3>
            <p class="text-sm opacity-75">{{ movie.genre }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const movies = [
  { id: 1, title: "Dune: Parte Dos", genre: "Ciencia Ficción", image: "/img/img1.jpeg" },
  { id: 2, title: "Oppenheimer", genre: "Historia", image: "/img/img2.jpg" },
  { id: 3, title: "Spider-Man: No Way Home", genre: "Acción", image: "/img/img3.jpeg" },
  { id: 4, title: "Avatar: El Camino del Agua", genre: "Fantasía", image: "/img/img4.jpeg" },
  { id: 5, title: "Origen", genre: "Ciencia Ficción", image: "/img/img5.jpeg" },
  { id: 6, title: "El Caballero Oscuro", genre: "Acción", image: "/img/img6.jpeg" },
  { id: 7, title: "Interstellar", genre: "Ciencia Ficción", image: "/img/img7.jpg" },
  { id: 8, title: "Gladiador", genre: "Historia", image: "/img/img8.jpg" },
  { id: 9, title: "La Bella y la Bestia", genre: "Fantasía", image: "/img/img9.jpeg" },
  { id: 10, title: "El Gran Gatsby", genre: "Drama", image: "/img/img10.jpg" },
  { id: 11, title: "Parásitos", genre: "Drama", image: "/img/img11.jpeg" },
  { id: 12,title: "Paddington", genre: "Fantasía", image: "/img/img12.jpg" }
];

</script>