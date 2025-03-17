<template>
  <section class="py-20">
    <div class="container mx-auto px-4">
      <div class="text-center mb-14">
        <h1 class="text-4xl font-extrabold text-white uppercase tracking-wide animate-fade-in">CARTELERA</h1>
        <div class="h-1 w-24 bg-gradient-to-r from-blue-600 to-blue-400 mx-auto mt-4 rounded shadow-md"></div>
        <p class="text-gray-300 mt-4">Disfruta de los mejores estrenos en CineXeperience</p>
      </div>
      
      <!-- Filtros -->
      <div class="mb-10">
        <div v-if="loading" class="text-center mb-8">
          <div class="border-4 border-blue-600/30 border-t-blue-600 rounded-full w-10 h-10 animate-spin mx-auto"></div>
          <p class="text-white mt-4">Cargando...</p>
        </div>
        <div v-else-if="error" class="text-center text-red-500 mb-8">
          <p>{{ error }}</p>
          <button @click="$emit('recargar')" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
            Reintentar
          </button>
        </div>
        <div v-else class="flex flex-wrap justify-center gap-4">
          <button 
            @click="$emit('filtrar', null)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all shadow-md',
              !filtroActivo 
                ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' 
                : 'bg-gradient-to-r from-gray-800 to-gray-700 text-gray-300 hover:from-gray-700 hover:to-gray-600'
            ]"
          >
            Todos
          </button>
          <button 
            v-for="genero in generos" 
            :key="genero.id"
            @click="$emit('filtrar', genero.id)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all shadow-md',
              filtroActivo === genero.id 
                ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white' 
                : 'bg-gradient-to-r from-gray-800 to-gray-700 text-gray-300 hover:from-gray-700 hover:to-gray-600'
            ]"
          >
            {{ genero.name }}
          </button>
        </div>
      </div>
      
      <!-- GRID DE PELÍCULAS - COMPLETAMENTE SIMPLIFICADO -->
      <div v-if="!loading && !error && peliculas" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Tarjeta de Película -->
        <div 
          v-for="pelicula in peliculas" 
          :key="pelicula.id"
          class="movie-card rounded-lg overflow-hidden shadow-lg hover:shadow-blue-500/30 transition duration-300 hover:-translate-y-2"
        >
          <!-- Imagen -->
          <div class="relative h-[360px] md:h-[320px]">
            <!-- Fondo como respaldo -->
            <div class="w-full h-full bg-gradient-to-br from-blue-900/50 to-blue-800/60">
              <div v-if="!pelicula.image || pelicula.image === ''" class="flex items-center justify-center h-full p-5 text-center">
                <span class="text-white text-lg font-bold">{{ pelicula.title }}</span>
              </div>
              <img 
                v-else 
                :src="getImagePath(pelicula.image)" 
                :alt="pelicula.title"
                class="w-full h-full object-cover"
                @error="$emit('imageError', $event)"
              />
            </div>
            
            <!-- Overlay de botones -->
            <div class="movie-overlay absolute inset-0 flex flex-col justify-end items-center p-6 z-20">
              <!-- Puntuación -->
              <div class="absolute top-2 right-2 bg-gradient-to-r from-blue-900/80 to-blue-800/80 text-white rounded-full px-3 py-1 font-bold flex items-center gap-1 shadow-md">
                <i class="fas fa-star text-yellow-400"></i>
                <span>{{ Math.floor(Math.random() * 3) + 7 }}.{{ Math.floor(Math.random() * 10) }}</span>
              </div>
              
              <!-- Botón COMPRAR -->
              <NuxtLink 
                :to="'/select-movie?id=' + pelicula.id" 
                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white uppercase font-bold py-2 px-6 rounded-full mb-2 hover:from-blue-500 hover:to-blue-400 hover:scale-105 transition-all shadow-md text-center"
              >
                COMPRAR
              </NuxtLink>
              
              <!-- Botón Ver Trailer -->
              <button 
                v-if="pelicula.trailer" 
                class="text-white font-medium flex items-center gap-1 hover:text-blue-400 transition-colors" 
                @click="$emit('verTrailer', pelicula.trailer)"
              >
                <i class="fas fa-play-circle"></i> Ver Trailer
              </button>
            </div>
          </div>
          
          <!-- Información de la película -->
          <div class="p-4 bg-gradient-to-br from-blue-900/50 to-blue-800/60">
            <h3 class="font-bold text-white text-lg mb-1">{{ pelicula.title }}</h3>
            <div class="flex justify-between items-center">
              <span class="text-gray-400 text-sm flex items-center">
                <i class="fas fa-clock mr-1 text-blue-400"></i>{{ pelicula.duration }} min
              </span>
              <span class="bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-full px-3 py-0.5 text-xs font-medium shadow-sm">
                {{ getGeneroNombre(pelicula.movie_genre_id) }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Mensaje si no hay películas -->
      <div v-if="!loading && !error && peliculas && peliculas.length === 0" class="text-center text-white p-10">
        <p class="text-xl">No se encontraron películas para el género seleccionado.</p>
      </div>
    </div>
    
    <!-- Modal para trailer -->
    <div v-if="trailerActivo" class="fixed inset-0 bg-black/90 flex justify-center items-center z-50 backdrop-blur-sm" @click="$emit('cerrarTrailer')">
      <div class="relative w-[90%] max-w-4xl transform transition-all duration-300 scale-100" style="aspect-ratio: 16/9;">
        <button 
          class="absolute -top-12 right-0 text-white text-2xl bg-gradient-to-r from-red-600/30 to-red-700/30 rounded-full w-10 h-10 flex items-center justify-center hover:bg-red-500/50 transition-all duration-300 cursor-pointer shadow-lg" 
          @click.stop="$emit('cerrarTrailer')"
        >
          <i class="fas fa-times"></i>
        </button>
        <div class="w-full h-full shadow-2xl rounded-lg overflow-hidden">
          <iframe 
            :src="trailerUrl + '?autoplay=1'" 
            frameborder="0" 
            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen
            class="w-full h-full"
          ></iframe>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  peliculas: {
    type: Array,
    default: () => []
  },
  generos: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  filtroActivo: {
    type: [Number, null],
    default: null
  },
  trailerActivo: {
    type: Boolean,
    default: false
  },
  trailerUrl: {
    type: String,
    default: ''
  }
});

defineEmits(['filtrar', 'verTrailer', 'cerrarTrailer', 'recargar', 'imageError']);

const getGeneroNombre = (generoId) => {
  if (!generoId) return 'Sin clasificar';
  const genero = props.generos.find(g => g.id === generoId);
  return genero ? genero.name : 'Desconocido';
};

const getImagePath = (imagePath) => {
  if (!imagePath) return '';
  
  if (imagePath.startsWith('/img/')) {
    return imagePath;
  } else if (imagePath.startsWith('/')) {
    return imagePath;
  } else {
    return `/storage/movies/${imagePath}`;
  }
};
</script>

<style scoped>
/* Estilos para la tarjeta de película */
.movie-card {
  position: relative;
  cursor: pointer;
}

/* Estilos para el overlay */
.movie-overlay {
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease-in-out;
  background: linear-gradient(to bottom, rgba(13, 37, 77, 0.1), rgba(13, 37, 77, 0.9));
  z-index: 10;
}

/* Cuando se hace hover sobre la tarjeta, el overlay se vuelve visible */
.movie-card:hover .movie-overlay {
  opacity: 1;
  visibility: visible;
}

/* Efectos para los botones dentro del overlay */
.movie-overlay a, 
.movie-overlay button {
  transform: translateY(20px);
  opacity: 0;
  transition: all 0.3s ease-in-out;
  transition-delay: 0.1s;
  cursor: pointer;
}

.movie-card:hover .movie-overlay a,
.movie-card:hover .movie-overlay button {
  transform: translateY(0);
  opacity: 1;
}

/* Aseguramos que los botones sean claramente visibles */
.movie-overlay a {
  display: block;
  text-align: center;
  font-weight: bold;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.movie-overlay button {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}
</style>
