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
  import { format } from 'date-fns';
  
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
      image: "/img/img1.jpg",
      times: ["14:30", "17:00", "19:30", "22:00"]
    },
    // Añade más películas aquí
  ]);
  
  const formatDate = (dateString) => {
    return format(new Date(dateString), 'dd/MM');
  };
  
  const selectShowtime = (movie, time) => {
    // Navegar a la página de selección de asientos
    navigateTo(`/select-seats?movie=${movie.id}&date=${selectedDate.value}&time=${time}`);
  };
  </script>