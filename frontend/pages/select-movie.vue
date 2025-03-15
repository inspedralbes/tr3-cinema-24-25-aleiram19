<template>
  <div class="min-h-screen bg-[#051D40] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Función</h1>
      
      <!-- Mensaje de carga -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        <p class="ml-4 text-white">Cargando información de la película...</p>
      </div>
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="bg-red-500/20 border border-red-500 text-white p-4 rounded-lg mb-8">
        <p class="font-medium">{{ error }}</p>
        <button 
          @click="loadMovieData" 
          class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
          Reintentar
        </button>
      </div>
      
      <!-- Contenido principal (solo se muestra si no hay error y no está cargando) -->
      <div v-if="!loading && !error">
        <!-- Selector de fecha -->
        <div class="bg-blue-900/50 backdrop-blur-sm rounded-xl p-6 mb-8">
        <div class="flex flex-wrap gap-4">
          <button 
            v-for="date in availableDates" 
            :key="date.value"
            @click="selectedDate = date.value"
            :class="[
              'px-6 py-4 rounded-xl transition-all duration-300 flex flex-col items-center',
              selectedDate === date.value 
                ? 'bg-blue-600 text-white transform scale-105 shadow-lg' 
                : 'bg-blue-900/30 text-gray-300 hover:bg-blue-800/50'
            ]"
          >
            <span class="text-sm font-medium">{{ date.dayName }}</span>
            <span class="text-2xl font-bold mt-1">{{ date.day }}</span>
            <span class="text-sm">{{ date.month }}</span>
          </button>
        </div>
      </div>

      <!-- Película seleccionada -->
      <div v-if="selectedMovie" class="bg-blue-900/50 backdrop-blur-sm rounded-xl overflow-hidden mb-8">
        <div class="flex flex-col md:flex-row">
          <!-- Imagen de la película -->
          <div class="md:w-1/3 relative">
            <div v-if="currentMovie && currentMovie.image" class="aspect-[2/3] bg-gradient-to-r from-blue-800 to-blue-600 relative overflow-hidden">
              <img 
                :src="currentMovie.image.startsWith('/') ? currentMovie.image : `/storage/movies/${currentMovie.image}`" 
                :alt="selectedMovie.title" 
                class="absolute inset-0 w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            <div v-else class="aspect-[2/3] bg-gradient-to-r from-blue-800 to-blue-600">
              <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xl">
                {{ selectedMovie.title }}
              </span>
            </div>
          </div>
          
          <!-- Detalles de la película -->
          <div class="md:w-2/3 p-6">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-2xl font-bold text-white mb-2">{{ selectedMovie.title }}</h2>
                <p class="text-gray-300 mb-4">{{ selectedMovie.genre }}</p>
              </div>
              <button 
                v-if="currentMovie && currentMovie.trailer" 
                class="bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2 hover:bg-blue-600 transition-colors"
                @click="openTrailer(currentMovie.trailer)"
              >
                <i class="fas fa-play-circle"></i> Ver Trailer
              </button>
            </div>
            
            <!-- Descripción -->
            <div class="mb-6 bg-blue-900/40 p-4 rounded-lg">
              <h3 class="text-lg font-semibold text-white mb-2">Sinopsis</h3>
              <p class="text-gray-300 text-sm leading-relaxed">
                {{ currentMovie?.description || 'No hay descripción disponible para esta película.' }}
              </p>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-white mb-3">Horarios Disponibles</h3>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <button 
                  v-for="time in selectedMovie.times" 
                  :key="time"
                  @click="selectShowtime(selectedMovie, time)"
                  class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-colors flex items-center justify-center gap-2"
                >
                  <i class="fas fa-clock"></i>
                  {{ time }}
                </button>
              </div>
            </div>

            <div class="flex items-center gap-4 text-gray-300 text-sm">
              <span><i class="fas fa-film mr-2"></i>{{ selectedMovie.duration }}</span>
              <span><i class="fas fa-star text-yellow-500 mr-2"></i>{{ selectedMovie.rating }}</span>
              <span><i class="fas fa-closed-captioning mr-2"></i>{{ selectedMovie.language }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de películas si no hay una seleccionada -->
      <div v-else-if="!selectedMovie && moviesForSelectedDate.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="movie in moviesForSelectedDate" 
          :key="movie.id" 
          @click="selectMovie(movie)"
          class="bg-blue-900/50 backdrop-blur-sm rounded-xl overflow-hidden cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
        >
          <div class="aspect-[16/9] bg-gradient-to-r from-blue-800 to-blue-600 relative">
            <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xl">
              {{ movie.title }}
            </span>
          </div>
          <div class="p-4">
            <h3 class="text-xl font-bold text-white mb-2">{{ movie.title }}</h3>
            <p class="text-gray-300 mb-4">{{ movie.genre }}</p>
            <div class="flex items-center gap-4 text-gray-400 text-sm">
              <span><i class="fas fa-clock mr-1"></i>{{ movie.duration }}</span>
              <span><i class="fas fa-star text-yellow-500 mr-1"></i>{{ movie.rating }}</span>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
    
    <!-- Modal para mostrar el trailer -->
    <div v-if="showTrailer" class="fixed inset-0 bg-black/90 flex justify-center items-center z-50" @click="closeTrailer">
      <div class="relative w-[90%] max-w-4xl" style="aspect-ratio: 16/9;">
        <button 
          class="absolute -top-10 right-0 text-white text-2xl bg-transparent border-none cursor-pointer" 
          @click.stop="closeTrailer"
        >
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useMoviesStore } from '@/stores/movies';
import { useGenresStore } from '@/stores/genres';

// Generar fechas disponibles con formato mejorado
const availableDates = [...Array(7)].map((_, i) => {
  const date = new Date();
  date.setDate(date.getDate() + i);
  return {
    value: date.toISOString().split('T')[0],
    dayName: new Intl.DateTimeFormat('es-ES', { weekday: 'short' }).format(date),
    day: date.getDate(),
    month: new Intl.DateTimeFormat('es-ES', { month: 'short' }).format(date)
  };
});

const selectedDate = ref(availableDates[0].value);
const selectedMovie = ref(null);
const movieId = ref(null);
const loading = ref(false);
const error = ref(null);
const trailerUrl = ref('');
const showTrailer = ref(false);

// Stores
const moviesStore = useMoviesStore();
const genresStore = useGenresStore();

// Películas disponibles
const allMovies = computed(() => moviesStore.movies);

// Película actual basada en el ID
const currentMovie = computed(() => {
  if (!movieId.value || !allMovies.value.length) return null;
  return allMovies.value.find(movie => movie.id === parseInt(movieId.value));
});

const selectMovie = (movie) => {
  selectedMovie.value = movie;
};

// Cargar datos de películas
// Manejar error al cargar imagen
const handleImageError = (e) => {
  if (e.target) {
    e.target.style.display = 'none';
    if (e.target.parentNode) {
      const fallbackDiv = document.createElement('div');
      fallbackDiv.className = 'absolute inset-0 flex items-center justify-center text-white font-bold text-xl';
      fallbackDiv.innerText = selectedMovie.value?.title || 'Sin imagen';
      e.target.parentNode.appendChild(fallbackDiv);
    }
  }
};

const loadMovieData = async () => {
  loading.value = true;
  error.value = null;
  try {
    await Promise.all([
      moviesStore.fetchCurrentMovies(),
      genresStore.fetchGenres()
    ]);
    
    // Si tenemos un ID en la URL, cargamos esa película específica
    if (movieId.value && allMovies.value.length > 0) {
      const foundMovie = allMovies.value.find(movie => movie.id === parseInt(movieId.value));
      if (foundMovie) {
        // Adaptamos el formato de la película a lo que espera nuestra interfaz
        selectedMovie.value = {
          id: foundMovie.id,
          title: foundMovie.title,
          genre: genresStore.genres.find(g => g.id === foundMovie.movie_genre_id)?.name || 'Desconocido',
          duration: `${foundMovie.duration} min`,
          rating: (Math.floor(Math.random() * 3) + 7) + '.' + (Math.floor(Math.random() * 10)), // Simulamos un rating
          language: foundMovie.language || 'ESP',
          times: ['14:30', '17:00', '19:30', '22:00'] // Horarios de ejemplo
        };
      } else {
        error.value = 'Película no encontrada';
      }
    }
  } catch (e) {
    console.error('Error cargando datos:', e);
    error.value = 'Error cargando datos de películas';
  } finally {
    loading.value = false;
  }
};

const selectShowtime = (movie, time) => {
  navigateTo(`/select-seats?movie=${movie.id}&date=${selectedDate.value}&time=${time}`);
};

// Abrir el trailer
const openTrailer = (url) => {
  if (!url) return;
  trailerUrl.value = url;
  showTrailer.value = true;
  document.body.style.overflow = 'hidden'; // Evitar scroll mientras el modal está abierto
};

// Cerrar el trailer
const closeTrailer = () => {
  showTrailer.value = false;
  trailerUrl.value = '';
  document.body.style.overflow = 'auto'; // Restaurar scroll
};

// Al cargar la página, extraemos el ID de la URL y cargamos los datos
onMounted(async () => {
  const route = useRoute();
  if (route.query.id) {
    movieId.value = route.query.id;
  }
  await loadMovieData();
});
</script>