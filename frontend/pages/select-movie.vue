<template>
  <div class="min-h-screen bg-[#051D40] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Función</h1>
      
      <!-- Selector de fecha-->
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
            <div class="aspect-[2/3] bg-gradient-to-r from-blue-800 to-blue-600">
              <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-xl">
                {{ selectedMovie.title }}
              </span>
            </div>
          </div>
          
          <!-- Detalles de la película -->
          <div class="md:w-2/3 p-6">
            <h2 class="text-2xl font-bold text-white mb-2">{{ selectedMovie.title }}</h2>
            <p class="text-gray-300 mb-4">{{ selectedMovie.genre }}</p>
            
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
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
</template>

<script setup>
import { ref } from 'vue';

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

const moviesForSelectedDate = ref([
  {
    id: 1,
    title: "Dune: Parte Dos",
    genre: "Ciencia Ficción",
    duration: "166 min",
    rating: "8.7",
    language: "ESP/VOSE",
    times: ["14:30", "17:00", "19:30", "22:00"]
  },
  {
    id: 2,
    title: "Godzilla y Kong: El Nuevo Imperio",
    genre: "Acción",
    duration: "115 min",
    rating: "7.5",
    language: "ESP",
    times: ["15:30", "18:00", "20:30", "23:00"]
  },
  {
    id: 3,
    title: "Kung Fu Panda 4",
    genre: "Animación",
    duration: "94 min",
    rating: "7.2",
    language: "ESP",
    times: ["16:00", "18:30", "21:00"]
  }
]);

const selectMovie = (movie) => {
  selectedMovie.value = movie;
};

const selectShowtime = (movie, time) => {
  navigateTo(`/select-seats?movie=${movie.id}&date=${selectedDate.value}&time=${time}`);
};
</script>