<template>
  <div class="min-h-screen bg-gradient-to-b from-[#051D40] to-[#03152E] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <h1 class="text-3xl font-bold text-white mb-8">Información de Película</h1>
      
      <!-- Mensaje de carga -->
      <div v-if="loading" class="flex flex-col justify-center items-center py-16">
        <div class="relative w-16 h-16 mb-4">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600/30 border-t-blue-600"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <i class="fas fa-film text-blue-400 text-xl animate-pulse"></i>
          </div>
        </div>
        <p class="text-white text-lg">Cargando información de la película...</p>
      </div>
      
      <!-- Contenido principal (solo se muestra si no hay error y no está cargando) -->
      <div v-if="!loading && !error">
        <!-- Película seleccionada -->
        <div v-if="movieData" class="bg-gradient-to-br from-blue-900/50 to-blue-800/60 backdrop-blur-sm rounded-xl overflow-hidden mb-8 shadow-xl">
          <div class="flex flex-col md:flex-row">
            <!-- Imagen de la película -->
            <div class="md:w-2/5 relative">
              <div v-if="movieData.image" class="h-full bg-gradient-to-r from-blue-800 to-blue-600 relative overflow-hidden">
                <img 
                  :src="getImagePath(movieData.image)" 
                  :alt="movieData.title" 
                  class="absolute inset-0 w-full h-full object-cover"
                  @error="handleImageError"
                />
                <!-- Badge del género -->
                <div class="absolute top-2 right-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs px-3 py-1 rounded-full shadow-md backdrop-blur-sm">
                  {{ getGenreName(movieData.movie_genre_id) }}
                </div>
              </div>
              <div v-else class="h-full bg-gradient-to-r from-blue-800 to-blue-600">
                <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xl">
                  {{ movieData.title }}
                </span>
              </div>
            </div>
            
            <!-- Detalles de la película -->
            <div class="md:w-3/5 p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h2 class="text-3xl font-bold text-white mb-2 animate-fade-in">{{ movieData.title }}</h2>
                  <div class="flex items-center gap-3 mb-4">
                    <span class="bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs px-3 py-1 rounded-full shadow-md">{{ getGenreName(movieData.movie_genre_id) }}</span>
                    <span class="text-yellow-400 flex items-center"><i class="fas fa-star mr-1"></i>{{ randomRating }}</span>
                    <span class="text-gray-300 text-sm flex items-center"><i class="fas fa-clock mr-1"></i>{{ movieData.duration }} min</span>
                  </div>
                </div>
                <button 
                  v-if="movieData.trailer" 
                  class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:from-red-500 hover:to-red-600 transition-all duration-300 shadow-lg transform hover:scale-105"
                  @click="openTrailer(movieData.trailer)"
                >
                  <i class="fas fa-play-circle"></i> Ver Trailer
                </button>
              </div>
              
              <!-- Descripción -->
              <div class="mb-6 bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-file-alt mr-2 text-blue-400"></i>Sinopsis</h3>
                <p class="text-gray-300 text-sm leading-relaxed">
                  {{ movieData.description || 'No hay descripción disponible para esta película.' }}
                </p>
              </div>
              
              <!-- Director y Actores -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                  <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-video mr-2 text-blue-400"></i>Director</h3>
                  <p class="text-gray-300 text-sm">{{ movieData.director || 'No disponible' }}</p>
                </div>
                <div class="bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                  <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-users mr-2 text-blue-400"></i>Reparto</h3>
                  <p class="text-gray-300 text-sm">{{ movieData.actors || 'No disponible' }}</p>
                </div>
              </div>
              
              <!-- Fecha de estreno -->
              <div class="mb-6 bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-calendar-day mr-2 text-blue-400"></i>Fecha de estreno</h3>
                <p class="text-gray-300 text-sm">
                  {{ movieData.release_date ? new Date(movieData.release_date).toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}) : 'No disponible' }}
                </p>
              </div>

              <div class="flex flex-wrap items-center gap-4 text-gray-300 text-sm bg-gradient-to-r from-blue-900/30 to-blue-800/30 p-4 rounded-lg shadow-inner">
                <span class="flex items-center"><i class="fas fa-film mr-2 text-blue-400"></i>{{ movieData.duration }} min</span>
                <span class="flex items-center"><i class="fas fa-star text-yellow-500 mr-2"></i>{{ randomRating }}</span>
                <span class="flex items-center"><i class="fas fa-closed-captioning mr-2 text-blue-400"></i>{{ movieData.language || 'ESP' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para mostrar el trailer -->
    <div v-if="showTrailer" class="fixed inset-0 bg-black/90 flex justify-center items-center z-50 backdrop-blur-sm" @click="closeTrailer">
      <div class="relative w-[90%] max-w-4xl transform transition-all duration-300 scale-100" style="aspect-ratio: 16/9;">
        <button 
          class="absolute -top-12 right-0 text-white text-2xl bg-gradient-to-r from-red-600/30 to-red-700/30 rounded-full w-10 h-10 flex items-center justify-center hover:bg-red-500/50 transition-all duration-300 cursor-pointer shadow-lg" 
          @click.stop="closeTrailer"
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useMoviesStore } from '@/stores/movies';
import { useGenresStore } from '@/stores/genres';

// Variables de estado
const movieId = ref(null);
const movieData = ref(null);
const loading = ref(false);
const error = ref(null);
const trailerUrl = ref('');
const showTrailer = ref(false);
const randomRating = computed(() => (Math.floor(Math.random() * 3) + 7) + '.' + (Math.floor(Math.random() * 10)));

// Stores
const moviesStore = useMoviesStore();
const genresStore = useGenresStore();

// Función para obtener el nombre del género por ID
const getGenreName = (genreId) => {
  if (!genreId) return 'Sin clasificar';
  const genre = genresStore.genres.find(g => g.id === genreId);
  return genre ? genre.name : 'Desconocido';
};

// Manejar error al cargar imagen
const handleImageError = (e) => {
  if (e.target) {
    e.target.style.display = 'none';
    if (e.target.parentNode) {
      const fallbackDiv = document.createElement('div');
      fallbackDiv.className = 'absolute inset-0 flex items-center justify-center text-white font-bold text-xl';
      fallbackDiv.innerText = movieData.value?.title || 'Sin imagen';
      e.target.parentNode.appendChild(fallbackDiv);
    }
  }
};

// Determinar la ruta de la imagen
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

// Cargar datos de la película
const loadMovieData = async () => {
  console.log('Iniciando carga de datos de película...');
  loading.value = true;
  error.value = null;
  
  try {
    // Cargar películas y géneros
    await Promise.all([
      moviesStore.fetchCurrentMovies(),
      genresStore.fetchGenres()
    ]);
        
    if (movieId.value && moviesStore.movies.length > 0) {
      // Buscar la película por ID
      const foundMovie = moviesStore.movies.find(movie => movie.id === parseInt(movieId.value));
      
      if (foundMovie) {
        movieData.value = foundMovie;
      } else {
        error.value = 'Película no encontrada';
      }
    } else {
    }
  } catch (e) {
    error.value = 'Error cargando datos de la película';
  } finally {
    loading.value = false;
  }
};

// Funciones para el trailer
const openTrailer = (url) => {
  if (!url) return;
  trailerUrl.value = url;
  showTrailer.value = true;
  document.body.style.overflow = 'hidden';
};

const closeTrailer = () => {
  showTrailer.value = false;
  trailerUrl.value = '';
  document.body.style.overflow = 'auto';
};

// Al montar el componente
onMounted(async () => {
  const route = useRoute();
  
  if (route.query.id) {
    movieId.value = route.query.id;
    await loadMovieData();
  } else {
    error.value = 'No se especificó ninguna película para mostrar';
  }
});
</script>
