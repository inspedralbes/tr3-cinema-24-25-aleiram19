<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    
    <div class="container mx-auto px-4 pt-28 pb-16">
      <div class="section-header mb-10">
        <h1 class="text-3xl font-bold text-white">Mis Entradas</h1>
        <div class="h-1 w-20 bg-blue-500 mt-4 rounded"></div>
        <p class="text-gray-300 mt-4">Visualiza y gestiona todas tus entradas de cine</p>
      </div>
      
      <!-- Si se está cargando, mostrar spinner -->
      <div v-if="ticketsStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
      
      <div v-else>
        <!-- Panel de filtros y búsqueda -->
        <div class="bg-navy-800 rounded-lg shadow-lg p-6 mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Filtrar por estado</label>
                <select 
                  v-model="filterStatus" 
                  class="bg-navy-900 border border-gray-700 rounded-lg px-4 py-2 text-white w-full sm:w-auto focus:border-blue-500 focus:outline-none"
                >
                  <option value="all">Todos</option>
                  <option value="active">Activos</option>
                  <option value="used">Usados</option>
                </select>
              </div>
              
              <div>
                <label class="block text-gray-300 text-sm font-medium mb-2">Ordenar por</label>
                <select 
                  v-model="sortBy" 
                  class="bg-navy-900 border border-gray-700 rounded-lg px-4 py-2 text-white w-full sm:w-auto focus:border-blue-500 focus:outline-none"
                >
                  <option value="date_desc">Fecha (más reciente)</option>
                  <option value="date_asc">Fecha (más antigua)</option>
                  <option value="title_asc">Título (A-Z)</option>
                  <option value="title_desc">Título (Z-A)</option>
                </select>
              </div>
            </div>
            
            <div class="flex-grow md:max-w-xs">
              <label class="block text-gray-300 text-sm font-medium mb-2">Buscar</label>
              <div class="relative">
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Buscar por título o código..." 
                  class="w-full bg-navy-900 border border-gray-700 rounded-lg pl-10 pr-4 py-2 text-white focus:border-blue-500 focus:outline-none"
                >
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  <i class="fas fa-search"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Lista de tickets -->
        <div v-if="filteredTickets.length === 0" class="text-center py-12 bg-navy-800 rounded-lg shadow-lg">
          <div class="text-5xl text-gray-600 mb-4">
            <i class="fas fa-ticket-alt"></i>
          </div>
          
          <h3 class="text-xl font-bold text-white mb-2">
            <span v-if="searchQuery || filterStatus !== 'all'">No se encontraron entradas</span>
            <span v-else>No tienes entradas</span>
          </h3>
          
          <p class="text-gray-400 mb-6">
            <span v-if="searchQuery || filterStatus !== 'all'">Intenta con otros filtros de búsqueda</span>
            <span v-else>¡Compra tu primera entrada y disfruta de la mejor experiencia cinematográfica!</span>
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
              v-if="searchQuery || filterStatus !== 'all'"
              @click="resetFilters" 
              class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center"
            >
              <i class="fas fa-undo mr-2"></i> Reiniciar filtros
            </button>
            
            <NuxtLink 
              to="/cartelera" 
              class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-6 rounded-lg transition-colors inline-flex items-center justify-center"
            >
              <i class="fas fa-film mr-2"></i> Ver Cartelera
            </NuxtLink>
          </div>
        </div>
        
        <div v-else class="space-y-6">
          <div 
            v-for="ticket in filteredTickets" 
            :key="ticket.id" 
            :class="[
              'bg-navy-800 border rounded-lg overflow-hidden transition-all duration-300 shadow-lg',
              ticket.status === 'active' ? 'border-blue-800 hover:border-blue-600' : 'border-gray-800 opacity-80'
            ]"
          >
            <div class="md:flex">
              <!-- Poster de la película (simulado con gradiente) -->
              <div class="md:w-1/4 h-48 md:h-auto bg-gradient-to-br from-blue-900 to-indigo-900 flex items-center justify-center p-4">
                <div class="w-24 h-36 rounded bg-navy-950/50 flex items-center justify-center">
                  <i class="fas fa-film text-4xl text-gray-400"></i>
                </div>
              </div>
              
              <div class="md:w-3/4 p-6">
                <div class="flex flex-col md:flex-row md:items-start justify-between mb-4">
                  <div>
                    <h3 class="text-xl font-bold text-white mb-1">{{ ticket.movie_title }}</h3>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                      <div class="flex items-center">
                        <i class="fas fa-calendar-day mr-1"></i> {{ formatDate(ticket.screening_date) }}
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-clock mr-1"></i> {{ ticket.screening_time }}
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-1"></i> Sala {{ ticket.room_number || '1' }}
                      </div>
                    </div>
                  </div>
                  
                  <div>
                    <div 
                      :class="[
                        'px-3 py-1 rounded-full text-xs font-medium inline-block',
                        ticket.status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400'
                      ]"
                    >
                      {{ ticket.status === 'active' ? 'Activo' : 'Usado' }}
                    </div>
                  </div>
                </div>
                
                <div class="flex flex-col md:flex-row md:items-center border-t border-gray-700 pt-4 mt-4">
                  <div class="flex-grow mb-4 md:mb-0">
                    <div class="flex flex-wrap gap-2">
                      <div v-for="(seat, index) in ticket.seats" :key="index" class="bg-blue-900/40 border border-blue-800/50 px-3 py-1 rounded-lg text-sm text-blue-300">
                        <i class="fas fa-couch mr-1"></i> Fila {{ seat.row }} - Asiento {{ seat.column }}
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center space-x-3">
                    <button 
                      v-if="ticket.status === 'active'"
                      @click="showQRCode(ticket)" 
                      class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center"
                    >
                      <i class="fas fa-qrcode mr-2"></i> Mostrar QR
                    </button>
                    
                    <button 
                      @click="viewTicketDetails(ticket.id)" 
                      class="bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center"
                    >
                      <i class="fas fa-eye mr-2"></i> Detalles
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-navy-900 px-5 py-3">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between">
                <div class="text-gray-400 text-sm mb-2 sm:mb-0">
                  <span class="text-gray-500">Código:</span> 
                  <span class="font-mono font-medium text-white">{{ ticket.ticket_code || 'TKT-' + ticket.id.toString().padStart(6, '0') }}</span>
                </div>
                
                <div class="text-gray-400 text-sm">
                  <span class="text-gray-500">Comprado:</span> 
                  <span>{{ formatPurchaseDate(ticket.created_at || new Date()) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Paginación -->
        <div v-if="filteredTickets.length > 0" class="flex justify-center mt-8">
          <div class="flex space-x-2">
            <button 
              :disabled="currentPage === 1"
              @click="currentPage--"
              :class="[
                'px-4 py-2 rounded-lg flex items-center justify-center',
                currentPage === 1 
                  ? 'bg-gray-800 text-gray-500 cursor-not-allowed' 
                  : 'bg-gray-700 text-white hover:bg-gray-600'
              ]"
            >
              <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="bg-blue-600 text-white px-4 py-2 rounded-lg">
              {{ currentPage }}
            </div>
            
            <button 
              :disabled="!hasMorePages"
              @click="currentPage++"
              :class="[
                'px-4 py-2 rounded-lg flex items-center justify-center',
                !hasMorePages 
                  ? 'bg-gray-800 text-gray-500 cursor-not-allowed' 
                  : 'bg-gray-700 text-white hover:bg-gray-600'
              ]"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para mostrar QR -->
    <div 
      v-if="showQRModal" 
      class="fixed inset-0 bg-black/80 flex items-center justify-center z-50"
      @click="showQRModal = false"
    >
      <div 
        class="bg-navy-800 rounded-lg shadow-xl max-w-md w-full mx-4"
        @click.stop
      >
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h3 class="text-xl font-bold text-white">Código QR de Entrada</h3>
          <button 
            @click="showQRModal = false"
            class="text-gray-400 hover:text-white"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-6 flex flex-col items-center">
          <div class="mb-4 text-center">
            <h4 class="text-lg font-bold text-white mb-1">{{ selectedTicket.movie_title }}</h4>
            <div class="text-gray-400 text-sm">
              {{ formatDate(selectedTicket.screening_date) }} | {{ selectedTicket.screening_time }}
            </div>
          </div>
          
          <!-- QR Code real -->
          <div class="bg-white p-4 rounded-lg mb-6 w-52 h-52 flex items-center justify-center">
            <QRCode
              :value="getTicketUrl(selectedTicket)"
              :size="180"
              level="H"
              class="mx-auto"
            />
          </div>
          
          <div class="text-center mb-2">
            <div class="text-gray-400 text-sm mb-1">Código de Entrada</div>
            <div class="font-mono font-bold text-xl text-white tracking-wider">
              {{ selectedTicket.ticket_code || 'TKT-' + selectedTicket.id.toString().padStart(6, '0') }}
            </div>
          </div>
          
          <button 
            class="mt-6 bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-6 rounded-lg transition-colors w-full flex items-center justify-center"
            @click="downloadTicket"
          >
            <i class="fas fa-download mr-2"></i> Descargar Entrada
          </button>
        </div>
      </div>
    </div>
    
    <LandingPageFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useAuthStore } from '~/stores/auth';
import { useTicketsStore } from '~/stores/tickets';
import { useRouter } from 'vue-router';
import QRCode from 'qrcode.vue';

const router = useRouter();
const authStore = useAuthStore();
const ticketsStore = useTicketsStore();
const { $toast } = useNuxtApp();

// Estados
const filterStatus = ref('all');
const sortBy = ref('date_desc');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;
const showQRModal = ref(false);
const selectedTicket = ref({});

// Al montar el componente
onMounted(async () => {
  if (!authStore.isAuthenticated) {
    $toast.error('Debes iniciar sesión para acceder a esta página');
    router.push('/login');
    return;
  }
  
  // Cargar tickets del usuario
  await ticketsStore.fetchUserTickets();
  
  // Añadir datos adicionales para simular tickets
  if (ticketsStore.userTickets.length > 0) {
    ticketsStore.userTickets.forEach(ticket => {
      // Si no tiene asientos, añadir algunos simulados
      if (!ticket.seats) {
        ticket.seats = [
          { row: 'G', column: '12' },
          { row: 'G', column: '13' }
        ];
      }
      
      // Si no tiene sala, asignar una
      if (!ticket.room_number) {
        ticket.room_number = Math.floor(Math.random() * 8) + 1;
      }
    });
  }
});

// Resetear la página cuando cambian los filtros
watch([filterStatus, sortBy, searchQuery], () => {
  currentPage.value = 1;
});

// Tickets filtrados
const filteredTickets = computed(() => {
  let result = [...ticketsStore.userTickets];
  
  // Filtrar por estado
  if (filterStatus.value !== 'all') {
    result = result.filter(ticket => ticket.status === filterStatus.value);
  }
  
  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(ticket => 
      ticket.movie_title.toLowerCase().includes(query) || 
      (ticket.ticket_code && ticket.ticket_code.toLowerCase().includes(query))
    );
  }
  
  // Ordenar
  result.sort((a, b) => {
    switch (sortBy.value) {
      case 'date_desc':
        return new Date(b.screening_date) - new Date(a.screening_date);
      case 'date_asc':
        return new Date(a.screening_date) - new Date(b.screening_date);
      case 'title_asc':
        return a.movie_title.localeCompare(b.movie_title);
      case 'title_desc':
        return b.movie_title.localeCompare(a.movie_title);
      default:
        return 0;
    }
  });
  
  // Paginación
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return result.slice(startIndex, endIndex);
});

// Comprobar si hay más páginas
const hasMorePages = computed(() => {
  const filteredTotal = ticketsStore.userTickets.filter(ticket => {
    if (filterStatus.value !== 'all' && ticket.status !== filterStatus.value) {
      return false;
    }
    
    if (searchQuery.value.trim()) {
      const query = searchQuery.value.toLowerCase();
      return ticket.movie_title.toLowerCase().includes(query) || 
        (ticket.ticket_code && ticket.ticket_code.toLowerCase().includes(query));
    }
    
    return true;
  }).length;
  
  return currentPage.value * itemsPerPage < filteredTotal;
});

// Métodos
const resetFilters = () => {
  filterStatus.value = 'all';
  sortBy.value = 'date_desc';
  searchQuery.value = '';
  currentPage.value = 1;
};

const viewTicketDetails = (ticketId) => {
  // Aquí iría la lógica para ver los detalles del ticket
  $toast.info(`Viendo detalles del ticket #${ticketId}`);
};

const showQRCode = (ticket) => {
  selectedTicket.value = ticket;
  showQRModal.value = true;
};

const downloadTicket = () => {
  $toast.info('Descargando entrada en PDF...');
  setTimeout(() => {
    showQRModal.value = false;
  }, 1500);
};

// Formatea la fecha para mostrarla de forma amigable
const formatDate = (dateString) => {
  try {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  } catch (error) {
    return 'Fecha no disponible';
  }
};

// Formatea la fecha de compra
const formatPurchaseDate = (dateString) => {
  try {
    return new Date(dateString).toLocaleDateString('es-ES', { 
      day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' 
    });
  } catch (error) {
    return 'Fecha no disponible';
  }
};

// Genera una URL para el código QR que incluye la información del ticket
const getTicketUrl = (ticket) => {
  // URL base de la web - modificar según el dominio de producción
  const baseUrl = window.location.origin;
  
  // Crear URL para ver detalles de ticket
  const ticketUrl = `${baseUrl}/ticket/${ticket.id}?code=${ticket.ticket_code || 'TKT-' + ticket.id.toString().padStart(6, '0')}`;
  
  return ticketUrl;
};

// Define el middleware de autenticación
definePageMeta({
  middleware: ['auth']
});
</script>

<style scoped>
.bg-navy-800:hover {
  transform: translateY(-2px);
}
</style>