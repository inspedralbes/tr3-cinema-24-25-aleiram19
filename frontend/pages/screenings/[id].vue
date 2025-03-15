<template>
  <div class="min-h-screen bg-blue-900 text-white p-6">
    <!-- Loading state -->
    <div v-if="screeningsStore.loading" class="flex justify-center my-10">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
    </div>
    
    <!-- Error state -->
    <div v-else-if="screeningsStore.error" class="bg-red-600 bg-opacity-80 p-4 rounded-lg mb-6">
      {{ screeningsStore.error }}
    </div>
    
    <!-- Content -->
    <div v-else-if="screening" class="max-w-6xl mx-auto">
      <!-- Back button -->
      <NuxtLink to="/screenings" class="inline-flex items-center text-blue-400 hover:text-blue-300 mb-6 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Volver a proyecciones
      </NuxtLink>
      
      <!-- Movie details -->
      <div class="bg-blue-800 rounded-lg overflow-hidden shadow-lg mb-8">
        <div class="md:flex">
          <!-- Movie poster -->
          <div class="md:w-1/3 h-64 md:h-auto bg-gray-700">
            <img 
              v-if="screening.movie && screening.movie.poster_url" 
              :src="screening.movie.poster_url" 
              :alt="screening.movie.title" 
              class="w-full h-full object-cover"
            >
            <div v-else class="w-full h-full flex items-center justify-center bg-gray-800">
              <i class="fas fa-film text-4xl text-gray-500"></i>
            </div>
          </div>
          
          <!-- Movie info -->
          <div class="md:w-2/3 p-6">
            <h1 class="text-3xl font-bold mb-2">{{ screening.movie ? screening.movie.title : 'Película sin título' }}</h1>
            
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-if="screening.movie && screening.movie.genre" class="bg-blue-700 px-3 py-1 rounded-full text-sm">
                {{ screening.movie.genre }}
              </span>
              <span v-if="screening.movie && screening.movie.duration" class="bg-blue-700 px-3 py-1 rounded-full text-sm">
                {{ screening.movie.duration }} min
              </span>
              <span v-if="screening.movie && screening.movie.rating" class="bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-bold">
                {{ screening.movie.rating }}
              </span>
            </div>
            
            <p v-if="screening.movie && screening.movie.description" class="text-gray-300 mb-4">
              {{ screening.movie.description }}
            </p>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                <span>{{ formatDate(screening.date) }}</span>
              </div>
              
              <div class="flex items-center">
                <i class="fas fa-clock text-gray-400 mr-2"></i>
                <span>{{ formatTime(screening.time) }}</span>
              </div>
              
              <div class="flex items-center">
                <i class="fas fa-video text-gray-400 mr-2"></i>
                <span>{{ screening.room ? screening.room.name : 'Sala sin especificar' }}</span>
              </div>
              
              <div class="flex items-center">
                <i class="fas fa-euro-sign text-gray-400 mr-2"></i>
                <span class="font-bold text-green-400">{{ formatPrice(screening.price) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Seats selection section -->
      <div class="bg-blue-800 rounded-lg overflow-hidden shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Selección de Asientos</h2>
        
        <!-- Check if user can buy tickets -->
        <div v-if="!authStore.isLoggedIn" class="bg-yellow-600 bg-opacity-80 p-4 rounded-lg mb-6">
          <p class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Debes iniciar sesión para reservar asientos
          </p>
          <NuxtLink to="/login" class="mt-2 inline-block bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-md transition-colors">
            Iniciar sesión
          </NuxtLink>
        </div>
        
        <div v-else-if="!ticketsStore.canBuyTickets" class="bg-red-600 bg-opacity-80 p-4 rounded-lg mb-6">
          <p class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Ya has comprado entradas para esta proyección
          </p>
        </div>
        
        <!-- Seats loading -->
        <div v-else-if="loadingSeats" class="flex justify-center my-10">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
        </div>
        
        <!-- Seats selection -->
        <div v-else>
          <!-- Legend -->
          <div class="flex items-center justify-center space-x-6 mb-6">
            <div class="flex items-center">
              <div class="w-6 h-6 bg-gray-600 rounded-md mr-2"></div>
              <span>Disponible</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 bg-blue-500 rounded-md mr-2"></div>
              <span>Seleccionado</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 bg-red-600 rounded-md mr-2"></div>
              <span>Ocupado</span>
            </div>
          </div>
          
          <!-- Screen -->
          <div class="relative mb-10">
            <div class="h-6 bg-gray-700 rounded-t-3xl mb-2 w-3/4 mx-auto"></div>
            <p class="text-center text-sm text-gray-400">PANTALLA</p>
          </div>
          
          <!-- Seats grid -->
          <div class="grid grid-cols-10 gap-2 mb-8">
            <div 
              v-for="seat in availableSeats" 
              :key="`${seat.row}-${seat.column}`"
              class="aspect-square flex items-center justify-center rounded-md cursor-pointer transition-colors"
              :class="{
                'bg-gray-600 hover:bg-gray-500': isSeatAvailable(seat),
                'bg-blue-500 hover:bg-blue-400': isSeatSelected(seat),
                'bg-red-600 cursor-not-allowed': !isSeatAvailable(seat) && !isSeatSelected(seat)
              }"
              @click="toggleSeat(seat)"
            >
              {{ seat.row }}{{ seat.column }}
            </div>
          </div>
          
          <!-- Selected seats and action buttons -->
          <div v-if="ticketsStore.getSelectedSeats.length > 0" class="border-t border-gray-700 pt-4">
            <p class="mb-2">Asientos seleccionados: {{ formatSelectedSeats }}</p>
            <p class="mb-4">Total: <span class="font-bold text-green-400">{{ formatPrice(totalPrice) }}</span></p>
            
            <div class="flex space-x-4">
              <button 
                @click="reserveSeats"
                class="bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-md transition-colors"
                :disabled="ticketsStore.loading"
              >
                <span v-if="ticketsStore.loading">
                  <i class="fas fa-spinner fa-spin mr-2"></i> Procesando...
                </span>
                <span v-else>
                  <i class="fas fa-shopping-cart mr-2"></i> Comprar Entradas
                </span>
              </button>
              
              <button 
                @click="ticketsStore.clearSelectedSeats()"
                class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-md transition-colors"
              >
                <i class="fas fa-times mr-2"></i> Cancelar Selección
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Not found -->
    <div v-else-if="!screeningsStore.loading" class="text-center py-10">
      <i class="fas fa-exclamation-circle text-6xl text-gray-500 mb-4"></i>
      <p class="text-xl text-gray-400">Proyección no encontrada</p>
      <NuxtLink to="/screenings" class="inline-block mt-4 bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-md transition-colors">
        Ver todas las proyecciones
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useScreeningsStore } from '~/stores/screenings';
import { useTicketsStore } from '~/stores/tickets';
import { useAuthStore } from '~/stores/auth';
import { format, parseISO } from 'date-fns';
import { es } from 'date-fns/locale';

const route = useRoute();
const router = useRouter();
const screeningsStore = useScreeningsStore();
const ticketsStore = useTicketsStore();
const authStore = useAuthStore();

const loadingSeats = ref(true);
const availableSeats = ref([]);

// Obtener el ID de la proyección de los parámetros de la ruta
const screeningId = computed(() => route.params.id);

// Acceder a la proyección actual a través del getter
const screening = computed(() => screeningsStore.getCurrentScreening);

// Cargar datos cuando se monta el componente
onMounted(async () => {
  // Cargar la proyección
  await screeningsStore.fetchScreeningById(screeningId.value);
  
  // Verificar si el usuario puede comprar tickets
  if (authStore.isLoggedIn) {
    await ticketsStore.checkCanBuyTickets(screeningId.value);
  }
  
  // Cargar los asientos disponibles
  try {
    await screeningsStore.fetchAvailableSeats(screeningId.value);
    availableSeats.value = screeningsStore.getAvailableSeats;
  } finally {
    loadingSeats.value = false;
  }
});

// Funciones para manejo de asientos
const isSeatAvailable = (seat) => {
  return seat.available && !isSeatSelected(seat);
};

const isSeatSelected = (seat) => {
  return ticketsStore.getSelectedSeats.some(
    s => s.row === seat.row && s.column === seat.column
  );
};

const toggleSeat = (seat) => {
  if (!isSeatAvailable(seat) && !isSeatSelected(seat)) {
    return; // No se puede seleccionar un asiento ocupado
  }
  
  if (isSeatSelected(seat)) {
    ticketsStore.unselectSeat(seat);
  } else {
    ticketsStore.selectSeat(seat);
  }
};

// Reservar los asientos seleccionados
const reserveSeats = async () => {
  if (ticketsStore.getSelectedSeats.length === 0) return;
  
  const reservation = await ticketsStore.reserveSeats(screeningId.value);
  
  if (reservation) {
    // Si la reserva fue exitosa, redirigir a la página de confirmación
    router.push({
      path: '/checkout',
      query: { 
        reservation_id: reservation.id,
        screening_id: screeningId.value
      }
    });
  }
};

// Formatear información
const formatDate = (dateString) => {
  try {
    return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
  } catch (error) {
    return dateString || 'Fecha no disponible';
  }
};

const formatTime = (timeString) => {
  if (!timeString) return 'Hora no disponible';
  return timeString.substring(0, 5); // Formato HH:MM
};

const formatPrice = (price) => {
  return `${price}€`;
};

// Total a pagar
const totalPrice = computed(() => {
  if (!screening.value || !screening.value.price) return 0;
  return screening.value.price * ticketsStore.getSelectedSeats.length;
});

// Formatear asientos seleccionados
const formatSelectedSeats = computed(() => {
  return ticketsStore.getSelectedSeats
    .map(seat => `${seat.row}${seat.column}`)
    .join(', ');
});
</script>
