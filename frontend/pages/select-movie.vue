<template>
  <div class="min-h-screen bg-gradient-to-b from-[#051D40] to-[#03152E] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Función</h1>
      
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
      
      <!-- Mensaje de error -->
      <div v-else-if="error" class="bg-gradient-to-r from-red-500/20 to-red-600/20 border border-red-500/50 text-white p-6 rounded-lg mb-8 shadow-md">
        <div class="flex items-center mb-4">
          <i class="fas fa-exclamation-circle text-red-400 text-2xl mr-3"></i>
          <p class="font-medium text-lg">{{ error }}</p>
        </div>
        <button 
          @click="loadMovieData" 
          class="mt-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-2 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all duration-300 shadow-md flex items-center">
          <i class="fas fa-sync-alt mr-2"></i> Reintentar
        </button>
      </div>
      
      <!-- Contenido principal (solo se muestra si no hay error y no está cargando) -->
      <div v-if="!loading && !error">
        <!-- Selector de fecha -->
        <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/60 backdrop-blur-sm rounded-xl p-6 mb-8 shadow-xl">
        <div class="flex flex-wrap gap-4 justify-center">
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
      <div v-if="selectedMovie" class="bg-gradient-to-br from-blue-900/50 to-blue-800/60 backdrop-blur-sm rounded-xl overflow-hidden mb-8 shadow-xl">
        <div class="flex flex-col md:flex-row">
          <!-- Imagen de la película -->
          <div class="md:w-2/5 relative">
            <div v-if="currentMovie && currentMovie.image" class="h-full bg-gradient-to-r from-blue-800 to-blue-600 relative overflow-hidden">
              <img 
                :src="currentMovie.image.startsWith('/') ? currentMovie.image : `/storage/movies/${currentMovie.image}`" 
                :alt="selectedMovie.title" 
                class="absolute inset-0 w-full h-full object-cover"
                @error="handleImageError"
              />
              <!-- Badge del género -->
              <div class="absolute top-2 right-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs px-3 py-1 rounded-full shadow-md backdrop-blur-sm">
                {{ selectedMovie.genre }}
              </div>
            </div>
            <div v-else class="h-full bg-gradient-to-r from-blue-800 to-blue-600">
              <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xl">
                {{ selectedMovie.title }}
              </span>
            </div>
          </div>
          
          <!-- Detalles de la película -->
          <div class="md:w-3/5 p-6">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-3xl font-bold text-white mb-2 animate-fade-in">{{ selectedMovie.title }}</h2>
                <div class="flex items-center gap-3 mb-4">
                  <span class="bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs px-3 py-1 rounded-full shadow-md">{{ selectedMovie.genre }}</span>
                  <span class="text-yellow-400 flex items-center"><i class="fas fa-star mr-1"></i>{{ selectedMovie.rating }}</span>
                  <span class="text-gray-300 text-sm flex items-center"><i class="fas fa-clock mr-1"></i>{{ selectedMovie.duration }}</span>
                </div>
              </div>
              <button 
                v-if="currentMovie && currentMovie.trailer" 
                class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:from-red-500 hover:to-red-600 transition-all duration-300 shadow-lg transform hover:scale-105"
                @click="openTrailer(currentMovie.trailer)"
              >
                <i class="fas fa-play-circle"></i> Ver Trailer
              </button>
            </div>
            
            <!-- Descripción -->
            <div class="mb-6 bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
              <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-file-alt mr-2 text-blue-400"></i>Sinopsis</h3>
              <p class="text-gray-300 text-sm leading-relaxed">
                {{ selectedMovie?.description || currentMovie?.description || 'No hay descripción disponible para esta película.' }}
              </p>
            </div>
            
            <!-- Director y Actores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div class="bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-video mr-2 text-blue-400"></i>Director</h3>
                <p class="text-gray-300 text-sm">{{ selectedMovie?.director || currentMovie?.director || 'No disponible' }}</p>
              </div>
              <div class="bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
                <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-users mr-2 text-blue-400"></i>Reparto</h3>
                <p class="text-gray-300 text-sm">{{ selectedMovie?.actors || currentMovie?.actors || 'No disponible' }}</p>
              </div>
            </div>
            
            <!-- Fecha de estreno -->
            <div class="mb-6 bg-gradient-to-b from-blue-900/40 to-blue-800/40 p-5 rounded-lg shadow-inner">
              <h3 class="text-lg font-semibold text-white mb-2 flex items-center"><i class="fas fa-calendar-day mr-2 text-blue-400"></i>Fecha de estreno</h3>
              <p class="text-gray-300 text-sm">
                {{ (selectedMovie?.release_date || currentMovie?.release_date) ? new Date(selectedMovie?.release_date || currentMovie?.release_date).toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}) : 'No disponible' }}
              </p>
            </div>
            
            <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-3 flex items-center"><i class="fas fa-ticket-alt mr-2 text-blue-400"></i>Horarios Disponibles</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <button 
              v-for="time in selectedMovie.times" 
              :key="time"
              @click="selectShowtime(selectedMovie, time)"
              class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-500 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2"
            >
            <i class="fas fa-clock"></i>
            {{ time }}
            </button>
            </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-gray-300 text-sm bg-gradient-to-r from-blue-900/30 to-blue-800/30 p-4 rounded-lg shadow-inner">
              <span class="flex items-center"><i class="fas fa-film mr-2 text-blue-400"></i>{{ selectedMovie.duration }}</span>
              <span class="flex items-center"><i class="fas fa-star text-yellow-500 mr-2"></i>{{ selectedMovie.rating }}</span>
              <span class="flex items-center"><i class="fas fa-closed-captioning mr-2 text-blue-400"></i>{{ selectedMovie.language }}</span>
              <span class="flex items-center"><i class="fas fa-calendar-alt mr-2 text-blue-400"></i>{{ selectedDate }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de películas si no hay una seleccionada -->
      <div v-else-if="!selectedMovie && moviesForSelectedDate.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <h2 class="text-2xl font-bold text-white mb-6 col-span-full flex items-center">
          <i class="fas fa-calendar-day text-blue-400 mr-3"></i>
          Películas disponibles para {{ new Date(selectedDate).toLocaleDateString('es-ES', {weekday: 'long', day: 'numeric', month: 'long'}) }}
        </h2>
        <div 
          v-for="movie in moviesForSelectedDate" 
          :key="movie.id" 
          @click="selectMovie(movie)"
          class="bg-gradient-to-br from-blue-900/50 to-blue-800/60 backdrop-blur-sm rounded-xl overflow-hidden cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
        >
          <div class="aspect-[16/9] bg-gradient-to-r from-blue-800 to-blue-600 relative overflow-hidden">
            <!-- Badge del género -->
            <div class="absolute top-2 right-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-xs px-3 py-1 rounded-full z-10 shadow-md backdrop-blur-sm">
              {{ movie.genre }}
            </div>
            <div v-if="getMovieById(movie.id) && getMovieById(movie.id).image" class="absolute inset-0 w-full h-full">
              <img 
                :src="getMovieById(movie.id).image.startsWith('/') ? getMovieById(movie.id).image : `/storage/movies/${getMovieById(movie.id).image}`" 
                :alt="movie.title" 
                class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity"
                @error="handleImageError"
              />
            </div>
            <!-- Solo mostrar el título como texto si no hay imagen o hay error -->
            <div v-if="!getMovieById(movie.id) || !getMovieById(movie.id).image" class="absolute inset-0 flex items-center justify-center">
              <span class="text-white font-bold text-xl">{{ movie.title }}</span>
            </div>
          </div>
          <div class="p-5 relative">
            <h3 class="text-xl font-bold text-white mb-3">{{ movie.title }}</h3>
            <div class="flex items-center gap-4 text-gray-300 text-sm">
              <span class="flex items-center"><i class="fas fa-clock text-blue-400 mr-1"></i>{{ movie.duration }}</span>
              <span class="flex items-center"><i class="fas fa-star text-yellow-500 mr-1"></i>{{ movie.rating }}</span>
              <span class="flex items-center"><i class="fas fa-language text-blue-400 mr-1"></i>{{ movie.language || 'ESP' }}</span>
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

// Películas filtradas para la fecha seleccionada
const moviesForSelectedDate = computed(() => {
  if (!allMovies.value.length) return [];
  
  // En un sistema real, filtrarías por fecha aquí
  // Por ahora, devolvemos todas para demostración
  return allMovies.value.map(movie => ({
    id: movie.id,
    title: movie.title,
    genre: genresStore.genres.find(g => g.id === movie.movie_genre_id)?.name || 'Desconocido',
    duration: `${movie.duration} min`,
    rating: (Math.floor(Math.random() * 3) + 7) + '.' + (Math.floor(Math.random() * 10)),
    language: movie.language || 'ESP',
    times: ['14:30', '17:00', '19:30', '22:00'], // Horarios de ejemplo
    director: movie.director,
    actors: movie.actors,
    description: movie.description,
    release_date: movie.release_date
  }));
});

// Película actual basada en el ID
const currentMovie = computed(() => {
  if (!movieId.value || !allMovies.value.length) return null;
  return allMovies.value.find(movie => movie.id === parseInt(movieId.value));
});

// Función para obtener detalles de una película por ID
const getMovieById = (id) => {
  if (!allMovies.value.length) return null;
  return allMovies.value.find(movie => movie.id === parseInt(id));
};

const selectMovie = (movie) => {
  const fullMovie = getMovieById(movie.id);
  if (fullMovie) {
    // Establece el ID de la película para que currentMovie se actualice
    movieId.value = movie.id;
    
    // Construir el objeto selectedMovie con todos los datos
    selectedMovie.value = {
      ...movie,
      director: fullMovie.director,
      actors: fullMovie.actors,
      description: fullMovie.description,
      release_date: fullMovie.release_date
    };
  } else {
    selectedMovie.value = movie;
  }
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
          times: ['14:30', '17:00', '19:30', '22:00'], // Horarios de ejemplo
          director: foundMovie.director,
          actors: foundMovie.actors,
          description: foundMovie.description,
          release_date: foundMovie.release_date
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