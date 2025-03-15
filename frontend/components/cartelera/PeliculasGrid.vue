<template>
  <section class="py-20">
    <div class="container mx-auto px-4">
      <div class="text-center mb-14">
        <h1 class="text-4xl font-extrabold text-white uppercase tracking-wide">CARTELERA</h1>
        <div class="h-1 w-24 bg-blue-600 mx-auto mt-4 rounded"></div>
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
              'px-4 py-2 rounded-full text-sm font-medium transition-all',
              !filtroActivo 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
            ]"
          >
            Todos
          </button>
          <button 
            v-for="genero in generos" 
            :key="genero.id"
            @click="$emit('filtrar', genero.id)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all',
              filtroActivo === genero.id 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
            ]"
          >
            {{ genero.name }}
          </button>
        </div>
      </div>
      
      <!-- Grid de películas -->
      <div v-if="!loading && !error && peliculas" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="pelicula in peliculas" 
          :key="pelicula.id"
          class="rounded-lg overflow-hidden bg-blue-900/50 shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-2"
        >
          <div class="relative overflow-hidden h-[360px] md:h-[320px]">
            <!-- Fondo como respaldo -->
            <div class="w-full h-full bg-gradient-to-b from-blue-900 to-gray-900">
              <div v-if="!pelicula.image || pelicula.image === ''" class="flex items-center justify-center h-full p-5 text-center">
                <span class="text-white text-lg font-bold">{{ pelicula.title }}</span>
              </div>
              <img 
                v-else 
                :src="pelicula.image.startsWith('/') ? pelicula.image : `/storage/movies/${pelicula.image}`" 
                :alt="pelicula.title"
                class="w-full h-full object-cover"
                @error="$emit('imageError', $event)"
              />
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/10 to-blue-900/90 flex flex-col justify-end items-center p-6 opacity-0 hover:opacity-100 transition-opacity duration-300">
              <div class="absolute top-2 right-2 bg-blue-900/80 text-white rounded-full px-3 py-1 font-bold flex items-center gap-1">
                <i class="fas fa-star text-yellow-400"></i>
                <span>{{ Math.floor(Math.random() * 3) + 7 }}.{{ Math.floor(Math.random() * 10) }}</span>
              </div>
              <NuxtLink 
                :to="'/cartelera/select-movie?id=' + pelicula.id" 
                class="bg-blue-600 text-white uppercase font-bold py-2 px-6 rounded-full mb-2 hover:bg-blue-500 hover:scale-105 transition-all"
              >
                COMPRAR
              </NuxtLink>
              <button 
                v-if="pelicula.trailer" 
                class="text-white font-medium flex items-center gap-1 hover:text-blue-400 transition-colors" 
                @click="$emit('verTrailer', pelicula.trailer)"
              >
                <i class="fas fa-play-circle"></i> Ver Trailer
              </button>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-white text-lg mb-1">{{ pelicula.title }}</h3>
            <div class="flex justify-between items-center">
              <span class="text-gray-400 text-sm">{{ pelicula.duration }} min</span>
              <span class="bg-blue-600 text-white rounded-full px-3 py-0.5 text-xs font-medium">{{ getGeneroNombre(pelicula.movie_genre_id) }}</span>
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
    <div v-if="trailerActivo" class="fixed inset-0 bg-black/90 flex justify-center items-center z-50" @click="$emit('cerrarTrailer')">
      <div class="relative w-[90%] max-w-4xl" style="aspect-ratio: 16/9;">
        <button class="absolute -top-10 right-0 text-white text-2xl bg-transparent border-none cursor-pointer" @click.stop="$emit('cerrarTrailer')">
          <i class="fas fa-times"></i>
        </button>
        <iframe 
          :src="trailerUrl + '?autoplay=1'" 
          frameborder="0" 
          allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
          allowfullscreen
          class="w-full h-full"
        ></iframe>
      </div>
    </div>
  </section>
</template>

<script setup>
// Props para recibir datos desde la página
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

// Emitir eventos para que la página los maneje
defineEmits(['filtrar', 'verTrailer', 'cerrarTrailer', 'recargar', 'imageError']);

// Métodos auxiliares que no requieren estado
const getGeneroNombre = (generoId) => {
  if (!generoId) return 'Sin clasificar';
  const genero = props.generos.find(g => g.id === generoId);
  return genero ? genero.name : 'Desconocido';
};
</script>