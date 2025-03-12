<template>
    <div class="min-h-screen bg-navy-900 py-8">
      <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Función</h1>
        
        <!-- Selector de fecha -->
        <div class="bg-white rounded-lg p-4 mb-8">
          <div class="flex space-x-4 overflow-x-auto">
            <button 
              v-for="date in availableDates" 
              :key="date"
              @click="selectedDate = date"
              :class="[
                'px-4 py-2 rounded-md',
                selectedDate === date ? 'bg-navy-600 text-white' : 'bg-gray-100'
              ]"
            >
              {{ formatDate(date) }}
            </button>
          </div>
        </div>
  
        <!-- Lista de películas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="movie in moviesForSelectedDate" :key="movie.id" class="bg-white rounded-lg overflow-hidden shadow-lg">
            <img :src="movie.image" :alt="movie.title" class="w-full h-48 object-cover">
            <div class="p-4">
              <h3 class="text-xl font-bold mb-2">{{ movie.title }}</h3>
              <p class="text-gray-600 mb-4">{{ movie.genre }}</p>
              
              <!-- Horarios disponibles -->
              <div class="flex flex-wrap gap-2">
                <button 
                  v-for="time in movie.times" 
                  :key="time"
                  @click="selectShowtime(movie, time)"
                  class="px-3 py-1 bg-navy-600 text-white rounded hover:bg-navy-700"
                >
                  {{ time }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  // Generar fechas disponibles (próximos 7 días)
  const availableDates = [...Array(7)].map((_, i) => {
    const date = new Date();
    date.setDate(date.getDate() + i);
    return date.toISOString().split('T')[0];
  });
  
  const selectedDate = ref(availableDates[0]);
  
  const moviesForSelectedDate = ref([
    {
      id: 1,
      title: "Dune: Parte Dos",
      genre: "Ciencia Ficción",
      image: "/img/movies/dune2.jpg",
      times: ["14:30", "17:00", "19:30", "22:00"]
    },
    {
      id: 2,
      title: "Godzilla y Kong: El Nuevo Imperio",
      genre: "Acción",
      image: "/img/movies/godzilla-kong.jpg",
      times: ["15:30", "18:00", "20:30", "23:00"]
    },
    {
      id: 3,
      title: "Kung Fu Panda 4",
      genre: "Animación",
      image: "/img/movies/kungfu-panda4.jpg",
      times: ["16:00", "18:30", "21:00"]
    }
  ]);
  
  // Función simple para formatear fechas sin dependencias externas
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    return `${day}/${month}`;
  };
  
  const selectShowtime = (movie, time) => {
    // Navegar a la página de selección de asientos
    navigateTo(`/select-seats?movie=${movie.id}&date=${selectedDate.value}&time=${time}`);
  };
  </script>