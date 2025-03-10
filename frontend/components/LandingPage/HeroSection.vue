<template>
  <div class="bg-blue-900 min-h-screen">
    <section class="py-10 px-5">
      <div class="mb-12">
        <h2 class="text-4xl font-bold text-center text-white mb-4 py-4">
          Cartelera
        </h2>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="movie in movies" 
          :key="movie.id" 
          class="movie-card rounded-lg overflow-hidden shadow-lg transform hover:scale-105 transition duration-300"
        >
          <div class="movie-image-container">
            <img :src="movie.image" :alt="movie.title" class="w-full h-full object-cover">
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const movies = [
  { id: 1, title: "Origen", genre: "Ciencia Ficción", image: "/img/img5.jpeg" },
  { id: 2, title: "El Caballero Oscuro", genre: "Acción", image: "/img/img6.jpeg" },
  { id: 3, title: "Interstellar", genre: "Ciencia Ficción", image: "/img/img7.jpg" },
  { id: 4, title: "Gladiador", genre: "Historia", image: "/img/img8.jpg" },
  { id: 5, title: "La Bella y la Bestia", genre: "Fantasía", image: "/img/img9.jpeg" },
  { id: 6, title: "El Gran Gatsby", genre: "Drama", image: "/img/img10.jpg" },
  { id: 7, title: "Parásitos", genre: "Drama", image: "/img/img11.jpeg" },
  { id: 8, title: "Paddington", genre: "Fantasía", image: "/img/img12.jpg" }
];
</script>

<style scoped>
/* Grid responsivo */
.grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1024px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Tarjeta de película */
.movie-card {
  width: 100%;
  height: 600px; /* Altura fija para simular un póster de cine */
  transition: transform 0.3s ease-in-out;
}

.movie-card:hover {
  transform: scale(1.05);
}

/* Contenedor de la imagen */
.movie-image-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.movie-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
