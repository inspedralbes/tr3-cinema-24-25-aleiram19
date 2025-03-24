<template>
  <div class="min-h-screen bg-[#051D40] py-8">
    <LandingPageNavBar />
    <div class="container mx-auto px-4 pt-24">
      <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-3xl font-bold text-white">Seleccionar Asientos</h1>
          <NuxtLink :to="`/screenings`" class="text-white hover:text-blue-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Volver a sesiones
          </NuxtLink>
        </div>

        <!-- Mensaje de error -->
        <div v-if="error" class="bg-gradient-to-r from-red-500/20 to-red-600/20 border border-red-500/50 text-white p-6 rounded-lg mb-8 shadow-md">
          <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-circle text-red-400 text-2xl mr-3"></i>
            <p class="font-medium text-lg">{{ error }}</p>
          </div>
          <button 
            @click="loadScreeningData" 
            class="mt-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-5 py-2 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all duration-300 shadow-md flex items-center"
          >
            <i class="fas fa-sync-alt mr-2"></i> Reintentar
          </button>
        </div>

        <!-- Información de la película -->
        <div class="bg-blue-900/50 backdrop-blur-sm rounded-xl p-6 mb-8">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-xl font-bold text-white">{{ movieInfo.title }}</h2>
              <div class="flex items-center mt-2">
                <div class="bg-blue-500/30 text-blue-100 px-3 py-1 rounded-md inline-flex items-center">
                  <i class="far fa-calendar-alt mr-2"></i>
                  <span>{{ formatDate(movieInfo.date) }}</span>
                </div>
                <div class="bg-blue-500/30 text-blue-100 px-3 py-1 rounded-md ml-3 inline-flex items-center">
                  <i class="far fa-clock mr-2"></i>
                  <span>{{ movieInfo.time }}</span>
                </div>
              </div>
            </div>
            <div class="text-right">
              <p class="text-gray-300">{{ getAuditoriumDisplay() }}</p>
              <p v-if="seatsStore.screening?.is_special" class="text-sm mt-1 bg-red-500/30 text-red-100 px-2 py-1 rounded-md inline-block">
                <i class="fas fa-star mr-1"></i> Día del Espectador
              </p>
              <p v-else class="text-sm mt-1 bg-blue-500/30 text-blue-100 px-2 py-1 rounded-md inline-block">
                Sesión Normal
              </p>
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
                <p class="text-2xl font-bold text-white">{{ formatPrice(totalPrice) }}</p>
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
import { useRoute, useRouter } from 'vue-router';
import { navigateTo } from '#app';
import { useMoviesStore } from '@/stores/movies';
import { useSeatsStore } from '@/stores/seats';
import { useTicketsStore } from '@/stores/tickets';

const route = useRoute();

// Stores
const moviesStore = useMoviesStore();
const seatsStore = useSeatsStore();

// Estados locales para la selección de asientos
const selectedSeats = ref([]);
const screeningId = ref(null);
const error = ref(null);

// Cargar la información de la película desde la URL o sessionStorage
const loadMovieInfo = () => {
  // Intentar recuperar del sessionStorage si no tenemos datos
  const storedMovie = sessionStorage.getItem('selectedMovie');
  if (storedMovie) {
    return JSON.parse(storedMovie);
  }
  
  // Si no hay datos en sessionStorage, usar los parámetros de la URL
  if (route.query.movie) {
    console.log('Usando información de película desde parámetros URL:', route.query.movie);
    return {
      id: parseInt(route.query.movie),
      title: 'Película',  // Se actualizará después
      date: route.query.date,
      time: route.query.time
    };
  }
  
  // Valor por defecto
  console.log('No se encontró información de película, usando valores por defecto');
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
  try {
    const date = new Date(dateString);
    const weekdays = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const day = weekdays[date.getDay()];
    return `${day} ${date.getDate()} de ${date.toLocaleString('es-ES', {month: 'long'})}`;
  } catch (e) {
    console.error('Error al formatear fecha:', e);
    return dateString;
  }
};

// Formatear precio
const formatPrice = (price) => {
  if (price === undefined || price === null) return '0,00 €';
  return Number(price).toLocaleString('es-ES', {
    style: 'currency',
    currency: 'EUR'
  });
};

// Obtener el nombre de la sala para mostrar
const getAuditoriumDisplay = () => {
  if (!seatsStore.auditorium) {
    return 'Cargando sala...';
  }
  
  // Debug
  
  if (seatsStore.auditorium.number) {
    return `Sala ${seatsStore.auditorium.number}`;
  } else if (seatsStore.auditorium.name) {
    return seatsStore.auditorium.name;
  } else if (seatsStore.auditorium.id) {
    return `Sala ${seatsStore.auditorium.id}`;
  } else {
    return 'Sala';
  }
};

// Confirmar la selección de asientos
const confirmSelection = () => {
  // Guardar los asientos seleccionados en sessionStorage
  sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats.value));
  
  // Guardar los datos de la función seleccionada en el store de tickets
  // para que estén disponibles en el proceso de checkout
  const ticketStore = useTicketsStore();
  // Nota: El método setSelectedScreening no existe en useTicketsStore
  // Guardamos la información del screening en sessionStorage
  sessionStorage.setItem('selectedScreening', JSON.stringify({
    id: screeningId.value,
    movie: {
      id: movieInfo.id,
      title: movieInfo.title
    },
    start_time: movieInfo.date + ' ' + movieInfo.time,
    room: { name: getAuditoriumDisplay() },
    price: selectedSeats.value.length > 0 ? selectedSeats.value[0].price : 0
  }));
  
  // Configurar los asientos seleccionados en el store
  ticketStore.clearSelectedSeats();
  selectedSeats.value.forEach(seat => {
    const [row, column] = seat.number.split('');
    ticketStore.selectSeat({
      id: seat.id,
      row: row,
      column: column,
      price: seat.price
    });
  });
  
  // Guardamos el screening_id en el store
  ticketStore.currentTicket = {
    screening_id: screeningId.value,
    screening: {
      id: screeningId.value,
      movie: {
        id: movieInfo.id,
        title: movieInfo.title
      }
    }
  };

  // Navegar a la página de checkout
  navigateTo('/checkout');
};

// Cargar datos de la película y proyección desde la API
const loadScreeningData = async () => {
  try {
    // Obtener el ID de la proyección de los parámetros de la URL
    screeningId.value = route.query.screening_id;
    
    if (!screeningId.value) {
      console.error('Error: No se ha especificado un ID de proyección');
      // Intentar recuperar del sessionStorage
      const storedMovie = sessionStorage.getItem('selectedMovie');
      if (storedMovie) {
        const movieData = JSON.parse(storedMovie);
        screeningId.value = movieData.screening_id;
        console.log('ID de proyección recuperado del sessionStorage:', screeningId.value);
      }
      
      if (!screeningId.value) {
        error.value = 'Error: No se ha especificado un ID de proyección';
        return;
      }
    }

    // Cargar la información de la proyección y los asientos
    const screeningData = await seatsStore.fetchSeatsForScreening(screeningId.value);
    
    if (screeningData && screeningData.screening && screeningData.screening.movie) {
      // Actualizar la información de la película
      movieInfo.id = screeningData.screening.movie.id;
      movieInfo.title = screeningData.screening.movie.title;
      movieInfo.date = screeningData.screening.date_time;
      
      // Formatear la hora para mostrarla
      const timeDate = new Date(screeningData.screening.date_time);
      movieInfo.time = timeDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
      
      // Guardar la información actualizada en sessionStorage para uso futuro
      sessionStorage.setItem('selectedMovie', JSON.stringify({
        id: movieInfo.id,
        screening_id: screeningId.value,
        title: movieInfo.title,
        date: movieInfo.date,
        time: movieInfo.time
      }));
      console.log('Película cargada correctamente:', movieInfo.title);
    } else {
      //console.error('No se encontró información completa de proyección o película');
      // Si falla la carga desde la API pero tenemos el ID de la película en la URL
      if (route.query.movie && !movieInfo.id) {
        const movieId = parseInt(route.query.movie);
        // Intentar cargar la película desde el store de películas
        const moviesData = await moviesStore.fetchMovies();
        const foundMovie = moviesStore.movies.find(m => m.id === movieId);
        
        if (foundMovie) {
          movieInfo.id = foundMovie.id;
          movieInfo.title = foundMovie.title;
          console.log('Película recuperada del store:', foundMovie.title);
        }
      }
    }
  } catch (error) {
    console.error('Error cargando datos:', error);
    error.value = 'Error al cargar la información de la película y asientos';
  }
};

// Al cargar la página
onMounted(async () => {
  // Cargar datos de la proyección y asientos
  await loadScreeningData();
});
</script>
