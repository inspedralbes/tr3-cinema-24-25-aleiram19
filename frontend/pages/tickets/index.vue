<template>
  <div class="min-h-screen bg-blue-900 text-white p-6">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold mb-6">Mis Tickets</h1>
      
      <!-- Authentication check -->
      <div v-if="!authStore.isLoggedIn" class="bg-yellow-600 bg-opacity-80 p-6 rounded-lg text-center">
        <p class="mb-4 text-lg">Debes iniciar sesión para ver tus tickets</p>
        <NuxtLink to="/login" class="inline-block bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-md transition-colors">
          Iniciar sesión
        </NuxtLink>
      </div>
      
      <!-- Loading state -->
      <div v-else-if="ticketsStore.loading" class="flex justify-center my-10">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
      </div>
      
      <!-- Error state -->
      <div v-else-if="ticketsStore.error" class="bg-red-600 bg-opacity-80 p-4 rounded-lg mb-6">
        {{ ticketsStore.error }}
      </div>
      
      <!-- Tickets list -->
      <div v-else-if="userTickets.length > 0" class="space-y-6">
        <div 
          v-for="ticket in userTickets" 
          :key="ticket.id" 
          class="bg-blue-800 rounded-lg overflow-hidden shadow-lg transition-transform hover:scale-[1.01]"
        >
          <div class="md:flex">
            <!-- Movie poster -->
            <div class="md:w-1/4 h-48 md:h-auto bg-gray-700">
              <img 
                v-if="ticket.screening && ticket.screening.movie && ticket.screening.movie.poster_url" 
                :src="ticket.screening.movie.poster_url" 
                :alt="ticket.screening.movie.title" 
                class="w-full h-full object-cover"
              >
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-800">
                <i class="fas fa-film text-4xl text-gray-500"></i>
              </div>
            </div>
            
            <!-- Ticket info -->
            <div class="md:w-3/4 p-6">
              <div class="flex flex-wrap justify-between items-start mb-4">
                <h2 class="text-2xl font-bold">
                  {{ ticket.screening?.movie?.title || 'Película sin título' }}
                </h2>
                
                <div class="bg-blue-700 px-3 py-1 rounded-full text-sm">
                  {{ ticket.status }}
                </div>
              </div>
              
              <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="flex items-center">
                  <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                  <span>{{ formatDate(ticket.screening?.date) }}</span>
                </div>
                
                <div class="flex items-center">
                  <i class="fas fa-clock text-gray-400 mr-2"></i>
                  <span>{{ formatTime(ticket.screening?.time) }}</span>
                </div>
                
                <div class="flex items-center">
                  <i class="fas fa-video text-gray-400 mr-2"></i>
                  <span>{{ ticket.screening?.room?.name || 'Sala sin especificar' }}</span>
                </div>
                
                <div class="flex items-center">
                  <i class="fas fa-chair text-gray-400 mr-2"></i>
                  <span>Asiento: {{ formatSeat(ticket.seat) }}</span>
                </div>
              </div>
              
              <div class="flex flex-wrap md:justify-between items-center gap-4">
                <div>
                  <span class="text-sm text-gray-400">Precio:</span>
                  <span class="ml-2 font-bold text-green-400">{{ formatPrice(ticket.price) }}</span>
                </div>
                
                <div class="flex gap-3">
                  <button 
                    @click="viewTicketDetails(ticket)"
                    class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-md transition-colors"
                  >
                    <i class="fas fa-eye mr-2"></i> Ver detalles
                  </button>
                  
                  <button 
                    v-if="canCancelTicket(ticket)"
                    @click="confirmCancelTicket(ticket)"
                    class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-md transition-colors"
                  >
                    <i class="fas fa-times mr-2"></i> Cancelar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty state -->
      <div v-else-if="userTickets.length === 0" class="text-center py-10">
        <i class="fas fa-ticket-alt text-6xl text-gray-500 mb-4"></i>
        <p class="text-xl text-gray-400 mb-6">No tienes tickets en este momento</p>
        <NuxtLink to="/screenings" class="inline-block bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-md transition-colors">
          Ver proyecciones disponibles
        </NuxtLink>
      </div>
      
      <!-- Cancel confirmation modal -->
      <div v-if="showCancelModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-blue-800 rounded-lg p-6 max-w-md w-full">
          <h3 class="text-xl font-bold mb-4">Confirmar cancelación</h3>
          <p class="mb-6">¿Estás seguro de que deseas cancelar este ticket? Esta acción no se puede deshacer.</p>
          
          <div class="flex justify-end space-x-4">
            <button 
              @click="showCancelModal = false"
              class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-md transition-colors"
            >
              Cancelar
            </button>
            <button 
              @click="cancelTicket"
              class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-md transition-colors"
              :disabled="ticketsStore.loading"
            >
              <span v-if="ticketsStore.loading">
                <i class="fas fa-spinner fa-spin mr-2"></i> Procesando...
              </span>
              <span v-else>
                Confirmar cancelación
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useTicketsStore } from '~/stores/tickets';
import { useAuthStore } from '~/stores/auth';
import { format, parseISO, addHours } from 'date-fns';
import { es } from 'date-fns/locale';

const router = useRouter();
const ticketsStore = useTicketsStore();
const authStore = useAuthStore();

const showCancelModal = ref(false);
const ticketToCancel = ref(null);

// Cargar tickets al montar la página
onMounted(async () => {
  if (authStore.isLoggedIn) {
    await ticketsStore.fetchUserTickets();
  }
});

// Acceder a los tickets del usuario
const userTickets = computed(() => ticketsStore.getUserTickets);

// Funciones para manejar la cancelación
const canCancelTicket = (ticket) => {
  if (!ticket || !ticket.screening || !ticket.screening.date || !ticket.screening.time) {
    return false;
  }
  
  // Verificar que el ticket no esté ya cancelado
  if (ticket.status === 'Cancelado') {
    return false;
  }
  
  // Verificar que la proyección no haya comenzado ya
  try {
    const screeningDate = parseISO(`${ticket.screening.date}T${ticket.screening.time}`);
    const now = new Date();
    
    // Permitir cancelar hasta 2 horas antes de la proyección
    const cancellationDeadline = addHours(now, 2);
    return screeningDate > cancellationDeadline;
  } catch (error) {
    console.error('Error verificando fecha de cancelación:', error);
    return false;
  }
};

const confirmCancelTicket = (ticket) => {
  ticketToCancel.value = ticket;
  showCancelModal.value = true;
};

const cancelTicket = async () => {
  if (!ticketToCancel.value) return;
  
  const success = await ticketsStore.cancelTickets([ticketToCancel.value.id]);
  
  if (success) {
    showCancelModal.value = false;
    ticketToCancel.value = null;
    // Opcionalmente mostrar un mensaje de éxito
  }
};

// Ver detalles del ticket
const viewTicketDetails = (ticket) => {
  router.push(`/tickets/${ticket.id}`);
};

// Funciones de formateo
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  try {
    return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
  } catch (error) {
    return dateString;
  }
};

const formatTime = (timeString) => {
  if (!timeString) return 'Hora no disponible';
  return timeString.substring(0, 5); // Formato HH:MM
};

const formatSeat = (seat) => {
  if (!seat) return 'No disponible';
  return `${seat.row}${seat.column}`;
};

const formatPrice = (price) => {
  if (price === null || price === undefined) return '0€';
  return `${price}€`;
};
</script>
