<template>
  <div class="min-h-screen bg-[#051D40] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Seleccionar Asientos</h1>

        <!-- Información de la película -->
        <div class="bg-blue-900/50 backdrop-blur-sm rounded-xl p-6 mb-8">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-xl font-bold text-white">{{ movieInfo.title }}</h2>
              <p class="text-gray-300">{{ formatDate(route.query.date) }} - {{ route.query.time }}</p>
            </div>
            <div class="text-right">
              <p class="text-gray-300">Sala 1</p>
              <p class="text-blue-400">VOSE</p>
            </div>
          </div>
        </div>

        <div class="bg-blue-900/50 backdrop-blur-sm rounded-xl p-8">
          <!-- Pantalla -->
          <div class="relative mb-16">
            <div class="w-full h-8 bg-gray-700 rounded-lg mb-4 flex items-center justify-center">
              <span class="text-gray-400 text-sm font-medium">PANTALLA</span>
            </div>
            <!-- Efecto de brillo de la pantalla -->
            <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 w-3/4 h-8 bg-blue-500/20 blur-xl"></div>
          </div>

          <!-- Asientos -->
          <div class="grid grid-cols-10 gap-3 mb-12">
            <template v-for="row in 8" :key="row">
              <template v-for="seat in 10" :key="`${row}-${seat}`">
                <button
                  @click="toggleSeat(`${row}-${seat}`)"
                  :class="[
                    'relative w-10 h-10 rounded-t-lg transition-all duration-300',
                    selectedSeats.includes(`${row}-${seat}`) 
                      ? 'bg-blue-500 text-white transform scale-110' 
                      : 'bg-blue-900/60 text-gray-300 hover:bg-blue-800',
                    occupiedSeats.includes(`${row}-${seat}`) 
                      ? 'bg-red-900/60 cursor-not-allowed' 
                      : ''
                  ]"
                  :disabled="occupiedSeats.includes(`${row}-${seat}`)"
                >
                  <span class="text-sm">{{ seat }}</span>
                  <!-- Efecto de "pata" del asiento -->
                  <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-6 h-2 bg-gray-700 rounded-b"></div>
                </button>
              </template>
            </template>
          </div>

          <!-- Leyenda -->
          <div class="flex justify-center space-x-8 mb-8 bg-blue-950/30 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="w-6 h-6 bg-blue-900/60 rounded-t-lg relative mr-3">
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-4 h-2 bg-gray-700 rounded-b"></div>
              </div>
              <span class="text-gray-300">Disponible</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 bg-blue-500 rounded-t-lg relative mr-3">
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-4 h-2 bg-gray-700 rounded-b"></div>
              </div>
              <span class="text-gray-300">Seleccionado</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 bg-red-900/60 rounded-t-lg relative mr-3">
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-4 h-2 bg-gray-700 rounded-b"></div>
              </div>
              <span class="text-gray-300">Ocupado</span>
            </div>
          </div>

          <!-- Resumen y botón de confirmación -->
          <div class="bg-blue-950/30 rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
              <div class="text-gray-300">
                <p>Asientos seleccionados: {{ selectedSeats.length }}</p>
                <p class="text-sm">{{ selectedSeats.join(', ') || 'Ninguno' }}</p>
              </div>
              <div class="text-right">
                <p class="text-gray-300">Total:</p>
                <p class="text-2xl font-bold text-white">${{ selectedSeats.length * 10 }}</p>
              </div>
            </div>
            <button
              @click="confirmSelection"
              class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              :disabled="selectedSeats.length === 0"
            >
              <i class="fas fa-ticket-alt"></i>
              Confirmar selección
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const selectedSeats = ref([]);
const occupiedSeats = ref(['1-2', '1-3', '4-5', '4-6', '6-7', '6-8']);

// Simulamos obtener la información de la película
const movieInfo = computed(() => {
  const movies = {
    1: { title: "Dune: Parte Dos" },
    2: { title: "Godzilla y Kong: El Nuevo Imperio" },
    3: { title: "Kung Fu Panda 4" }
  };
  return movies[route.query.movie] || { title: "Película no encontrada" };
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  }).format(date);
};

const toggleSeat = (seatId) => {
  if (occupiedSeats.value.includes(seatId)) return;
  
  const index = selectedSeats.value.indexOf(seatId);
  if (index === -1) {
    selectedSeats.value.push(seatId);
  } else {
    selectedSeats.value.splice(index, 1);
  }
};

const confirmSelection = () => {
  // Aquí iría la lógica para confirmar la selección
  console.log('Asientos confirmados:', selectedSeats.value);
};
</script>