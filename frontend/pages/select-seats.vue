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
              <p class="text-gray-300">{{ formatDate(movieInfo.date) }} - {{ movieInfo.time }}</p>
            </div>
            <div class="text-right">
              <p class="text-gray-300">{{ seatsStore.auditorium ? `Sala ${seatsStore.auditorium.number}` : 'Cargando sala...' }}</p>
              <p class="text-blue-400">{{ seatsStore.screening ? (seatsStore.screening.is_special ? 'ESPECIAL' : 'NORMAL') : '' }}</p>
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

          <!-- Asientos organizados por filas -->
          <div v-if="seatsStore.loading" class="flex justify-center items-center py-16">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
          </div>
          
          <div v-else-if="Object.keys(rows).length > 0" class="mb-12">
            <!-- Etiquetas de columnas -->
            <div class="flex justify-center mb-4 text-gray-400">
              <div class="w-12 text-center"></div> <!-- Espacio para la etiqueta de fila -->
              <div v-for="i in 10" :key="'col-'+i" class="w-12 text-center text-sm">{{ i }}</div>
            </div>
            
            <!-- Filas de asientos -->
            <div v-for="(seats, rowKey) in rows" :key="rowKey" class="flex justify-center mb-3">
              <!-- Etiqueta de fila -->
              <div class="w-12 flex items-center justify-center text-gray-400 text-sm font-medium">{{ rowKey }}</div>
              
              <!-- Asientos de la fila -->
              <div v-for="(seat, idx) in seats" :key="seat.id" class="px-1">
                <button
                  @click="toggleSeat(rowKey, idx)"
                  :class="[
                    'relative w-10 h-10 rounded-t-lg transition-all duration-300 flex items-center justify-center',
                    isSeatSelected(seat.id) 
                      ? 'bg-blue-500 text-white transform scale-110 shadow-lg' 
                      : seat.status === 'available'
                        ? (seat.is_vip 
                           ? 'bg-purple-900/60 text-white hover:bg-purple-700'
                           : 'bg-blue-900/60 text-gray-300 hover:bg-blue-800')
                        : 'bg-red-900/60 text-gray-500 cursor-not-allowed'
                  ]"
                  :disabled="seat.status !== 'available'"
                  :title="seat.status !== 'available' ? 'Asiento no disponible' : ''"
                >
                  <span class="text-sm">{{ seat.column }}</span>
                  <!-- Efecto de "pata" del asiento -->
                  <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-6 h-2 bg-gray-700 rounded-b"></div>
                  
                  <!-- Indicador de precio (opcional) -->
                  <div v-if="seat.is_vip && seat.status === 'available'" 
                    class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs text-purple-300 bg-purple-900/70 px-2 py-1 rounded-t-lg">
                    VIP
                  </div>
                </button>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-400">
            No hay asientos disponibles para esta proyección.
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
              <div class="w-6 h-6 bg-purple-900/60 rounded-t-lg relative mr-3">
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-4 h-2 bg-gray-700 rounded-b"></div>
              </div>
              <span class="text-gray-300">VIP</span>
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
                <p class="text-sm">{{ selectedSeats.map(seat => seat.number).join(', ') || 'Ninguno' }}</p>
              </div>
              <div class="text-right">
                <p class="text-gray-300">Total:</p>
                <p class="text-2xl font-bold text-white">${{ totalPrice }}</p>
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
import { ref, computed, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { useMoviesStore } from '@/stores/movies';
import { useSeatsStore } from '@/stores/seats';

const route = useRoute();

// Stores
const moviesStore = useMoviesStore();
const seatsStore = useSeatsStore();

// Estados locales para la selección de asientos
const selectedSeats = ref([]);
const screeningId = ref(null);

// Cargar la información de la película desde la URL o sessionStorage
const loadMovieInfo = () => {
  // Intentar recuperar del sessionStorage si no tenemos datos
  const storedMovie = sessionStorage.getItem('selectedMovie');
  if (storedMovie) {
    return JSON.parse(storedMovie);
  }
  
  // Si no hay datos en sessionStorage, usar los parámetros de la URL
  if (route.query.movie) {
    return {
      id: parseInt(route.query.movie),
      title: 'Película',  // Se actualizará después
      date: route.query.date,
      time: route.query.time
    };
  }
  
  // Valor por defecto
  return { title: "Cargando película..." };
};

// Información de la película
const movieInfo = reactive(loadMovieInfo());

// Asientos organizados por filas
const rows = computed(() => {
  return seatsStore.seatsByRow;
});

// Determinar si un asiento está ocupado
const isSeatOccupied = (seatId) => {
  // Buscar el asiento en las filas
  for (const row in rows.value) {
    const seat = rows.value[row].find(seat => seat.id === seatId);
    if (seat && seat.status !== 'available') {
      return true;
    }
  }
  return false;
};

// Manejar la selección de un asiento
const toggleSeat = (rowKey, columnIdx) => {
  const seat = rows.value[rowKey][columnIdx];
  
  // Si el asiento no está disponible, no permitir seleccionarlo
  if (seat.status !== 'available') {
    return;
  }
  
  const seatId = seat.id;
  const seatNumber = seat.number;
  
  // Verificar si el asiento ya está seleccionado
  const index = selectedSeats.value.findIndex(s => s.id === seatId);
  
  if (index === -1) {
    // Añadir a la selección
    selectedSeats.value.push({
      id: seatId,
      number: seatNumber,
      price: seat.price,
      isVip: seat.is_vip
    });
  } else {
    // Remover de la selección
    selectedSeats.value.splice(index, 1);
  }
};

// Verificar si un asiento está seleccionado
const isSeatSelected = (seatId) => {
  return selectedSeats.value.some(seat => seat.id === seatId);
};

// Calcular el precio total
const totalPrice = computed(() => {
  return selectedSeats.value.reduce((total, seat) => total + seat.price, 0);
});

// Formatear fecha
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

// Confirmar la selección de asientos
const confirmSelection = () => {
  // Guardar los asientos seleccionados en sessionStorage
  sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats.value));
  
  // Aquí iría la lógica para confirmar la selección
  console.log('Asientos confirmados:', selectedSeats.value);
  
  // Navegar a la siguiente página (por ejemplo, una página de pago o confirmación)
  // navigateTo('/payment');
};

// Cargar datos de la película y proyección desde la API
const loadScreeningData = async () => {
  try {
    // Obtener el ID de la proyección
    // En un sistema real, aquí obtendrías el ID de proyección basado en película, fecha y hora
    // Para la demostración, usamos un valor de ejemplo o generamos uno
    const movieId = parseInt(route.query.movie) || 1;
    screeningId.value = route.query.screening_id || 1; // Valor temporal para demostración
    
    // Cargar información de la película si no la tenemos completa
    if (!movieInfo.title || movieInfo.title === 'Película' || movieInfo.title === 'Cargando película...') {
      // Si no tenemos películas cargadas, las cargamos
      if (!moviesStore.movies.length) {
        await moviesStore.fetchCurrentMovies();
      }
      
      // Obtener la película por ID
      const movie = moviesStore.getMovieById(movieId);
      if (movie) {
        movieInfo.id = movie.id;
        movieInfo.title = movie.title;
      }
    }
    
    // Cargar asientos para la proyección
    await seatsStore.fetchSeatsForScreening(screeningId.value);
  } catch (error) {
    console.error('Error cargando datos:', error);
  }
};

// Al cargar la página
onMounted(async () => {
  // Cargar datos de la proyección y asientos
  await loadScreeningData();
});
</script>
