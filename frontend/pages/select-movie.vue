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
          class="mt-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-2 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all duration-300 shadow-md flex items-center"
        >
          <i class="fas fa-sync-alt mr-2"></i> Reintentar
        </button>
      </div>
      
      <!-- Mensaje cuando no hay películas disponibles para la fecha o hay un error -->
      <div v-else-if="(!selectedMovie && !moviesForSelectedDate.length && !loading) || (error && !loading)" class="bg-gradient-to-br from-blue-900/30 to-blue-800/40 backdrop-blur-sm p-8 rounded-xl text-center shadow-lg">
        <div class="flex flex-col items-center gap-4">
          <i class="fas fa-film text-blue-400 text-5xl mb-2"></i>
          <h2 class="text-2xl font-bold text-white">{{ error || 'No hay funciones disponibles' }}</h2>
          <p v-if="!error" class="text-gray-300 text-lg">
            No se encontraron películas con funciones para {{ new Date(selectedDate).toLocaleDateString('es-ES', {weekday: 'long', day: 'numeric', month: 'long'}) }}
          </p>
          <!-- Mensaje cuando no hay películas -->  
          <p v-if="!error" class="text-gray-400 mt-2">Intenta seleccionar otra fecha o vuelve más tarde.</p>
          <!-- Mensaje cuando el error es específico a una película -->
          <p v-if="error && error.includes('No hay funciones programadas')" class="text-gray-400 mt-2">
            Esta película no tiene funciones programadas. Por favor, selecciona otra película o consulta la cartelera más tarde.
          </p>
          <!-- Botones de acción -->
          <div class="flex gap-4 mt-4">
            <button 
              @click="updateMoviesForSelectedDate" 
              class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-3 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all duration-300 shadow-md flex items-center"
            >
              <i class="fas fa-sync-alt mr-2"></i> Actualizar
            </button>
            <button 
              v-if="error" 
              @click="resetSelection" 
              class="bg-gradient-to-r from-gray-600 to-gray-500 text-white px-5 py-3 rounded-lg hover:from-gray-500 hover:to-gray-400 transition-all duration-300 shadow-md flex items-center"
            >
              <i class="fas fa-arrow-left mr-2"></i> Volver
            </button>
          </div>
        </div>
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
                  :src="getImagePath(currentMovie.image)" 
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
              
              <div class="mb-6 horarios-section">
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
                  :src="getImagePath(getMovieById(movie.id).image)" 
                  :alt="movie.title" 
                  class="w-full h-full object-cover opacity-70 hover:opacity-100 transition-opacity"
                  @error="handleImageError"
                />
              </div>
              <!-- Mostrar título si no hay imagen -->
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
import { ref, onMounted, computed, watch } from 'vue';
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

// Seleccionar la fecha actual por defecto
const selectedDate = ref(availableDates[0].value);
console.log('Fecha seleccionada por defecto:', selectedDate.value);
const selectedMovie = ref(null);
const movieId = ref(null);
const loading = ref(false);
const error = ref(null);
const showScreeningMessage = ref(true);
const trailerUrl = ref('');
const showTrailer = ref(false);

// Stores
const moviesStore = useMoviesStore();
const genresStore = useGenresStore();

// Variable para almacenar los screenings por fecha
const screeningsByDate = ref({});
// Variable para almacenar los IDs de screenings por película, fecha y hora
const screeningIdsByMovieAndTime = ref({});

// Películas disponibles
const allMovies = computed(() => moviesStore.movies);

// Lista reactiva de películas filtradas por fecha
const moviesForSelectedDate = ref([]);

// Función para actualizar las películas disponibles para la fecha seleccionada
const updateMoviesForSelectedDate = async () => {
  if (!allMovies.value.length) {
    console.log('No hay películas cargadas aún');
    moviesForSelectedDate.value = [];
    return;
  }
  
  console.log(`Actualizando películas para la fecha ${selectedDate.value}`);
  console.log('Total de películas disponibles:', allMovies.value.length);
  loading.value = true;
  error.value = null; // Resetear cualquier error previo
  
  if (!screeningsByDate.value[selectedDate.value]) {
    console.log('Obteniendo screenings del backend porque no están en cache');
    await fetchScreeningsByDate(selectedDate.value);
  } else {
    console.log('Usando screenings en cache:', screeningsByDate.value[selectedDate.value]);
    console.log('Cantidad de películas con screenings:', Object.keys(screeningsByDate.value[selectedDate.value]).length);
  }
  
  const screeningsForDate = screeningsByDate.value[selectedDate.value] || {};
  const moviesWithScreenings = [];
  
  console.log('Películas totales:', allMovies.value.length);
  console.log('Screenings para la fecha:', screeningsForDate);
  
  // Solo mostrar las películas que tienen proyecciones en esta fecha
  allMovies.value.forEach(movie => {
    const movieTimes = screeningsForDate[movie.id] || [];
    console.log(`Verificando película ${movie.title} (ID: ${movie.id}) - Horarios disponibles: ${movieTimes.length}`);
    
    if (movieTimes.length > 0) {
      console.log(`La película ${movie.title} (ID: ${movie.id}) tiene ${movieTimes.length} horarios: ${movieTimes.join(', ')}`);
      moviesWithScreenings.push({
        id: movie.id,
        title: movie.title,
        genre: genresStore.genres.find(g => g.id === movie.movie_genre_id)?.name || 'Desconocido',
        duration: `${movie.duration} min`,
        rating: (Math.floor(Math.random() * 3) + 7) + '.' + (Math.floor(Math.random() * 10)),
        language: movie.language || 'ESP',
        times: movieTimes,
        director: movie.director,
        actors: movie.actors,
        description: movie.description,
        release_date: movie.release_date
      });
    } else {
      console.log(`La película ${movie.title} (ID: ${movie.id}) NO tiene horarios disponibles para esta fecha`);
    }
  });
  
  console.log(`Total de películas con screenings encontradas: ${moviesWithScreenings.length}`);
  
  // Limitar a máximo 2 películas por fecha
  if (moviesWithScreenings.length > 2) {
    console.warn(`Se encontraron ${moviesWithScreenings.length} películas con proyecciones, limitando a 2`);
    // Ordenar por ID para asegurar consistencia
    moviesWithScreenings.sort((a, b) => a.id - b.id);
    // Mantener solo las primeras 2
    moviesWithScreenings.splice(2);
  }
  
  console.log(`Películas finales que se mostrarán (${moviesWithScreenings.length}):`, 
    moviesWithScreenings.map(m => `${m.title} (ID: ${m.id}, Horarios: ${m.times.join(', ')})`))
  
  moviesForSelectedDate.value = moviesWithScreenings;
  loading.value = false;
};

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

const selectMovie = async (movie) => {
  console.log('Seleccionando película:', movie.title, 'ID:', movie.id);
  const fullMovie = getMovieById(movie.id);
  if (fullMovie) {
    movieId.value = movie.id;
    
    // Verificar si hay horarios disponibles en la fecha seleccionada actualmente
    const screeningsForCurrentDate = screeningsByDate.value[selectedDate.value] || {};
    const movieTimesForCurrentDate = screeningsForCurrentDate[movie.id] || [];
    
    console.log(`Horarios disponibles en la fecha actual (${selectedDate.value}):`, movieTimesForCurrentDate);
    
    // Si no hay horarios para la fecha actual, buscar en todas las fechas disponibles
    if (movieTimesForCurrentDate.length === 0) {
      console.log('No hay horarios para esta película en la fecha actual. Buscando en otras fechas...');
      
      // Buscar en todas las fechas disponibles
      let foundDate = null;
      
      // Precargar datos de todas las fechas primero para una búsqueda completa
      for (const date of availableDates) {
        if (!screeningsByDate.value[date.value]) {
          await fetchScreeningsByDate(date.value);
        }
      }
      
      // Ahora buscar la fecha que tenga esta película
      for (const date of availableDates) {
        const screeningsForDate = screeningsByDate.value[date.value] || {};
        const movieTimes = screeningsForDate[movie.id] || [];
        
        if (movieTimes.length > 0) {
          console.log(`¡Encontrada fecha con proyecciones para película ${movie.id}: ${date.value}!`);
          foundDate = date.value;
          break;
        }
      }
      
      // Si encontramos una fecha con proyecciones, cambiar a esa fecha
      if (foundDate) {
        console.log(`Cambiando a fecha ${foundDate} para mostrar proyecciones de la película ${movie.id}`);
        selectedDate.value = foundDate;
        // Esperar a que se actualicen los datos
        await updateMoviesForSelectedDate();
      } else {
        console.log(`No se encontraron proyecciones para la película ${movie.id} en ninguna fecha`);
        // Mostrar un mensaje al usuario
        error.value = 'No hay funciones programadas para esta película actualmente';
        selectedMovie.value = null; // Limpiar la selección
        return;
      }
    }
    
    // Después de posiblemente cambiar la fecha, obtener los horarios actualizados
    const screeningsForDate = screeningsByDate.value[selectedDate.value] || {};
    const movieTimes = screeningsForDate[movie.id] || [];
    
    console.log(`Horarios disponibles para la película ${movie.id}:`, movieTimes);
    
    // Verificar una vez más que realmente hay horarios disponibles
    if (movieTimes.length === 0) {
      console.error(`No se encontraron horarios para la película ${movie.id} en la fecha ${selectedDate.value} después de la búsqueda`);
      error.value = 'Ha ocurrido un error al cargar los horarios de esta película';
      selectedMovie.value = null;
      return;
    }
    
    selectedMovie.value = {
      ...movie,
      director: fullMovie.director,
      actors: fullMovie.actors,
      description: fullMovie.description,
      release_date: fullMovie.release_date,
      times: movieTimes
    };
    
    console.log('Película seleccionada:', selectedMovie.value);
    
    if (movieTimes.length > 0) {
      setTimeout(() => {
        const horarioSection = document.querySelector('.horarios-section');
        if (horarioSection) {
          horarioSection.scrollIntoView({ behavior: 'smooth' });
        }
      }, 100);
    }
  } else {
    selectedMovie.value = movie;
  }
};

// Función para obtener los screenings por fecha
const fetchScreeningsByDate = async (date) => {
  try {
    loading.value = true;
    error.value = null;
    
    console.log(`Obteniendo screenings para la fecha: ${date}`);
    
    // Asegurar que la fecha esté en formato YYYY-MM-DD
    const formattedDate = new Date(date).toISOString().split('T')[0];
    
    const response = await fetch(`http://localhost:8000/api/screenings?date=${formattedDate}`, {
      headers: {
        'Accept': 'application/json'
      }
    });
    
    if (!response.ok) {
      // Intentar obtener más detalles del error
      let errorDetail = response.statusText;
      try {
        const errorData = await response.json();
        if (errorData && errorData.message) {
          errorDetail = errorData.message;
        } else if (errorData && errorData.error) {
          errorDetail = errorData.error;
        }
      } catch (jsonError) {
        console.warn('No se pudo parsear la respuesta de error como JSON', jsonError);
      }
      
      throw new Error(`Error en la petición: ${response.status} ${errorDetail}`);
    }
    
    const data = await response.json();
    console.log(`Screenings recibidos del backend: ${data.length} registros`);
    console.log('Detalle de screenings:', data);
    
    // Verificar si data es un array
    if (!Array.isArray(data)) {
      console.error('La respuesta no es un array:', data);
      throw new Error('Formato de respuesta inesperado');
    }
    
    // Agrupar screenings por película
    const screeningsByMovie = {};
    // Almacenar los IDs de screenings por película y hora
    const screeningIds = {};
    
    // Organizar todos los screenings por película
    data.forEach(screening => {
      // Validar que screening.date_time exista
      if (!screening || !screening.date_time) {
        console.warn('Screening sin fecha/hora:', screening);
        return; // Saltar este screening
      }
      
      const time = new Date(screening.date_time).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      });
      
      console.log(`Procesando screening para película ${screening.movie_id}, hora: ${time}`);
      
      if (!screeningsByMovie[screening.movie_id]) {
        screeningsByMovie[screening.movie_id] = [];
        screeningIds[screening.movie_id] = {};
      }
      
      // Guardar el ID del screening para esta película y hora
      screeningIds[screening.movie_id][time] = screening.id;
      
      // Asegurarse de no duplicar horas
      if (!screeningsByMovie[screening.movie_id].includes(time)) {
        screeningsByMovie[screening.movie_id].push(time);
      }
    });
    
    // Ordenar las horas para cada película (18:00 antes que 20:00)
    Object.keys(screeningsByMovie).forEach(movieId => {
      screeningsByMovie[movieId].sort();
    });
    
    console.log(`Screenings agrupados por película:`, screeningsByMovie);
    console.log(`IDs de screenings por película y hora:`, screeningIds);
    console.log(`Total de películas para la fecha ${date}: ${Object.keys(screeningsByMovie).length}`);
    
    // Guardar en el store
    screeningsByDate.value[date] = screeningsByMovie;
    // Guardar los IDs de screenings
    screeningIdsByMovieAndTime.value[date] = screeningIds;
    
    return { screeningsByMovie, screeningIds };
  } catch (e) {
    console.error('Error obteniendo screenings:', e);
    error.value = `Error al cargar los horarios disponibles: ${e.message}`;
    return {};
  } finally {
    loading.value = false;
  }
};

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

const loadMovieData = async () => {
  console.log('Iniciando carga de datos de películas...');
  loading.value = true;
  error.value = null;
  try {
    await Promise.all([
      moviesStore.fetchCurrentMovies(),
      genresStore.fetchGenres()
    ]);
    
    console.log('Películas cargadas del store:', allMovies.value.length);
    console.log('Géneros cargados:', genresStore.genres.length);
    
    if (movieId.value && allMovies.value.length > 0) {
      console.log(`Buscando película con ID ${movieId.value}...`);
      const foundMovie = allMovies.value.find(movie => movie.id === parseInt(movieId.value));
      
      if (foundMovie) {
        console.log('Película encontrada:', foundMovie.title);
        if (!screeningsByDate.value[selectedDate.value]) {
          console.log(`Cargando screenings para la fecha ${selectedDate.value}...`);
          await fetchScreeningsByDate(selectedDate.value);
        } else {
          console.log('Usando screenings en cache');
        }
        
        
        const screeningsForDate = screeningsByDate.value[selectedDate.value] || {};
        const movieTimes = screeningsForDate[foundMovie.id] || [];
        
        selectedMovie.value = {
          id: foundMovie.id,
          title: foundMovie.title,
          genre: genresStore.genres.find(g => g.id === foundMovie.movie_genre_id)?.name || 'Desconocido',
          duration: `${foundMovie.duration} min`,
          rating: (Math.floor(Math.random() * 3) + 7) + '.' + (Math.floor(Math.random() * 10)),
          language: foundMovie.language || 'ESP',
          times: movieTimes,
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
  // Obtener el ID del screening para esta película y hora
  const screeningIds = screeningIdsByMovieAndTime.value[selectedDate.value] || {};
  const movieScreeningIds = screeningIds[movie.id] || {};
  const screeningId = movieScreeningIds[time];
  
  if (!screeningId) {
    console.error(`No se encontró ID de screening para película ${movie.id} y hora ${time}`);
    error.value = 'Error al seleccionar horario';
    return;
  }
  
  console.log(`Seleccionado screening: ${screeningId} para película ${movie.id} a las ${time}`);
  
  sessionStorage.setItem('selectedMovie', JSON.stringify({
    id: movie.id,
    title: movie.title,
    date: selectedDate.value,
    time: time,
    screening_id: screeningId
  }));
  
  navigateTo(`/select-seats?movie=${movie.id}&date=${selectedDate.value}&time=${time}&screening_id=${screeningId}`);
};

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

// Función para resetear la selección y los errores
const resetSelection = () => {
  selectedMovie.value = null;
  error.value = null;
  movieId.value = null;
  updateMoviesForSelectedDate();
};

watch(selectedDate, () => {
  selectedMovie.value = null;
  error.value = null;
  updateMoviesForSelectedDate();
});

onMounted(async () => {
  console.log('Componente select-movie montado');
  const route = useRoute();
  if (route.query.id) {
    console.log(`ID de película detectado en la URL: ${route.query.id}`);
    movieId.value = route.query.id;
  }
  await loadMovieData();
  await updateMoviesForSelectedDate();
  
  // Verificar si se mostraron películas correctamente
  console.log(`Después de la carga inicial: ${moviesForSelectedDate.value.length} películas mostradas`);
  if (moviesForSelectedDate.value.length === 0) {
    console.warn('No se encontraron películas para mostrar. Intentando con otra fecha...');
    // Si no hay películas en la fecha actual, intentar con la siguiente
    if (availableDates.length > 1) {
      selectedDate.value = availableDates[1].value;
      console.log(`Cambiando a fecha alternativa: ${selectedDate.value}`);
      await updateMoviesForSelectedDate();
    }
  }
  
  setTimeout(() => {
    showScreeningMessage.value = false;
  }, 5000);
});
</script>
