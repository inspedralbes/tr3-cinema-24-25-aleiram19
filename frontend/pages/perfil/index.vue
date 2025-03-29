<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    
    <div class="container mx-auto px-4 pt-28 pb-16">
      <div class="section-header text-center mb-10">
        <h1 class="text-3xl font-bold text-white">Mi Perfil</h1>
        <div class="h-1 w-20 bg-blue-500 mx-auto mt-4 rounded"></div>
      </div>
      
      <!-- Si se está cargando, mostrar spinner -->
      <div v-if="authStore.loading || ticketsStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
      
      <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Menú lateral -->
        <div class="lg:col-span-1">
          <div class="bg-navy-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Avatar y datos de usuario -->
            <div class="p-6 bg-gradient-to-r from-blue-900 to-indigo-900 text-center">
              <div class="w-24 h-24 mx-auto bg-blue-600 rounded-full flex items-center justify-center text-white text-3xl mb-4">
                <i class="fas fa-user"></i>
              </div>
              <h2 class="text-xl font-bold text-white mb-1">{{ authStore.user?.name || 'Usuario' }} {{ authStore.user?.last_name || '' }}</h2>
              <p class="text-gray-300 text-sm mb-3">{{ authStore.user?.email || 'usuario@ejemplo.com' }}</p>
              <div class="bg-blue-600/30 rounded-full py-1 px-3 text-blue-300 text-xs inline-flex items-center">
                <i class="fas fa-ticket-alt mr-1"></i> 
                <span>{{ ticketsStore.userTickets.length }} entradas compradas</span>
              </div>
            </div>
            
            <!-- Opciones de navegación -->
            <div class="p-4">
              <button 
                @click="activeTab = 'perfil'" 
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg mb-2 flex items-center transition-colors', 
                  activeTab === 'perfil' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-blue-900/50'
                ]"
              >
                <i class="fas fa-user-circle mr-3"></i> Información Personal
              </button>
              
              <button 
                @click="activeTab = 'tickets'" 
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg mb-2 flex items-center transition-colors', 
                  activeTab === 'tickets' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-blue-900/50'
                ]"
              >
                <i class="fas fa-ticket-alt mr-3"></i> Mis Entradas
              </button>
              
              <button 
                @click="activeTab = 'promociones'" 
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg mb-2 flex items-center transition-colors', 
                  activeTab === 'promociones' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-blue-900/50'
                ]"
              >
                <i class="fas fa-tags mr-3"></i> Promociones Exclusivas
              </button>
              
              <div class="border-t border-gray-700 my-4"></div>
              
              <button 
                @click="handleLogout" 
                class="w-full text-left px-4 py-3 rounded-lg flex items-center text-red-400 hover:bg-red-900/20 transition-colors"
              >
                <i class="fas fa-sign-out-alt mr-3"></i> Cerrar Sesión
              </button>
            </div>
          </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="lg:col-span-3">
          <div class="bg-navy-800 rounded-lg shadow-lg p-6">
            <!-- Vista de información personal -->
            <div v-if="activeTab === 'perfil'">
              <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-user-circle mr-3 text-blue-500"></i> 
                Información Personal
              </h2>
              
              <form @submit.prevent="updateProfile" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-gray-300 text-sm font-medium mb-2">Nombre</label>
                    <input 
                      v-model="profileData.name" 
                      type="text" 
                      class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                      placeholder="Tu nombre"
                    >
                  </div>
                  
                  <div>
                    <label class="block text-gray-300 text-sm font-medium mb-2">Apellido</label>
                    <input 
                      v-model="profileData.last_name" 
                      type="text" 
                      class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                      placeholder="Tu apellido"
                    >
                  </div>
                  
                  <div class="md:col-span-2">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Email</label>
                    <input 
                      v-model="profileData.email" 
                      type="email" 
                      class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                      placeholder="Tu email"
                      disabled
                    >
                    <p class="text-gray-400 text-xs mt-1">El email no puede ser modificado</p>
                  </div>
                </div>
              </form>
            </div>
            
            <!-- Vista de tickets/entradas -->
            <div v-else-if="activeTab === 'tickets'">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                  <i class="fas fa-ticket-alt mr-3 text-blue-500"></i> 
                  Mis Entradas
                </h2>
                <button 
                  @click="refreshTickets" 
                  class="bg-blue-700 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors flex items-center"
                  :disabled="ticketsStore.loading"
                >
                  <i class="fas fa-sync-alt mr-2" :class="{'animate-spin': ticketsStore.loading}"></i> 
                  Actualizar
                </button>
              </div>
              
              <div v-if="ticketsStore.loading" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
              </div>
              
              <div v-else-if="ticketsStore.error" class="text-center py-8">
                <div class="text-4xl text-red-500 mb-4">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Error al cargar entradas</h3>
                <p class="text-gray-400 mb-6">{{ ticketsStore.error }}</p>
                <button 
                  @click="ticketsStore.fetchUserTickets()" 
                  class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center"
                >
                  <i class="fas fa-sync-alt mr-2"></i> Reintentar
                </button>
              </div>
              
              <div v-else-if="ticketsStore.userTickets.length === 0" class="text-center py-12">
                <div class="text-5xl text-gray-600 mb-4">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No tienes entradas</h3>
                <p class="text-gray-400 mb-6">¡Compra tu primera entrada y disfruta de la mejor experiencia cinematográfica!</p>
                <NuxtLink 
                  to="/cartelera" 
                  class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-6 rounded-lg transition-colors inline-flex items-center"
                >
                  <i class="fas fa-film mr-2"></i> Ver Cartelera
                </NuxtLink>
              </div>
              
              <div v-else class="space-y-4">
                <!-- Tickets/Entradas -->
                <div 
                  v-for="ticket in ticketsStore.userTickets" 
                  :key="ticket.id" 
                  class="bg-navy-900/80 border border-gray-800 rounded-lg overflow-hidden hover:border-blue-700 transition-all duration-300"
                >
                  <div class="p-5">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                      <div class="mb-4 md:mb-0">
                        <!-- Título de la película con mejor visualización -->
                        <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                          <i class="fas fa-film text-blue-500 mr-2"></i>
                          {{ ticket.movie_title || ticket.movie?.title || ticket.screening?.movie?.title || 'Película' }}
                        </h3>
                        
                        <!-- Información de fecha y hora -->
                        <div class="flex items-center text-gray-400 text-sm mb-2">
                          <i class="fas fa-calendar-alt mr-2"></i>
                          <span>{{ formatDate(ticket.screening_date || ticket.screening?.date || ticket.date) }}</span>
                        </div>
                        
                        <!-- Hora de la proyección -->
                        <div class="flex items-center text-gray-400 text-sm mb-2">
                          <i class="fas fa-clock mr-2"></i>
                          <span>{{ ticket.screening_time || ticket.screening?.time || ticket.time }}</span>
                        </div>
                        
                        <!-- Sala -->
                        <div class="flex items-center text-gray-400 text-sm">
                          <i class="fas fa-map-marker-alt mr-2"></i>
                          <span>{{ ticket.hall_name || ticket.screening?.hall_name || ticket.screening?.auditorium?.name || 'Sala' }}</span>
                        </div>
                      </div>
                      
                      <div class="flex flex-col md:flex-row items-start md:items-center md:space-x-4">
                        <!-- Número de asientos -->
                        <div class="bg-blue-900 py-1 px-3 rounded-full text-sm text-blue-300 flex items-center mb-3 md:mb-0">
                          <i class="fas fa-couch mr-1"></i> {{ ticket.seat_count || (ticket.seats ? ticket.seats.length : 1) }} asientos
                        </div>
                        
                        <!-- Botón para ver detalles -->
                        <button 
                          @click="viewTicketDetails(ticket.id)"
                          class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium py-2 px-4 rounded-full transition-colors flex items-center"
                        >
                          <i class="fas fa-eye mr-2"></i> Ver Detalles
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Sección expandible con detalles del ticket -->
                  <div v-if="expandedTicketId === ticket.id" class="border-t border-gray-800 bg-navy-950/50 p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <!-- Columna izquierda: Información de la película -->
                      <div>
                        <h4 class="text-lg font-bold text-white mb-3">Detalles de la película</h4>
                        
                        <!-- Póster de la película si existe -->
                        <div v-if="ticket.movie?.poster || ticket.screening?.movie?.poster" class="mb-4">
                          <img 
                            :src="ticket.movie?.poster || ticket.screening?.movie?.poster" 
                            :alt="ticket.movie_title || ticket.movie?.title || ticket.screening?.movie?.title" 
                            class="rounded-lg w-full max-w-xs mx-auto md:mx-0"
                          />
                        </div>
                        
                        <!-- Sinopsis -->
                        <div v-if="ticket.movie?.description || ticket.screening?.movie?.description" class="mb-4">
                          <h5 class="text-sm font-bold text-gray-300 mb-1">Sinopsis:</h5>
                          <p class="text-gray-400 text-sm">
                            {{ ticket.movie?.description || ticket.screening?.movie?.description }}
                          </p>
                        </div>
                        
                        <!-- Director -->
                        <div v-if="ticket.movie?.director || ticket.screening?.movie?.director" class="mb-2">
                          <h5 class="text-sm font-bold text-gray-300 mb-1">Director:</h5>
                          <p class="text-gray-400 text-sm">
                            {{ ticket.movie?.director || ticket.screening?.movie?.director }}
                          </p>
                        </div>
                        
                        <!-- Duración -->
                        <div v-if="ticket.movie?.duration || ticket.screening?.movie?.duration" class="mb-2">
                          <h5 class="text-sm font-bold text-gray-300 mb-1">Duración:</h5>
                          <p class="text-gray-400 text-sm">
                            {{ ticket.movie?.duration || ticket.screening?.movie?.duration }} minutos
                          </p>
                        </div>
                      </div>
                      
                      <!-- Columna derecha: Detalles de la entrada -->
                      <div>
                        <h4 class="text-lg font-bold text-white mb-3">Detalles de la entrada</h4>
                        
                        <!-- Asientos -->
                        <div class="mb-3">
                          <h5 class="text-sm font-bold text-gray-300 mb-2">Asientos:</h5>
                          <div class="flex flex-wrap gap-2">
                            <span 
                              v-for="(seat, index) in ticket.seats" 
                              :key="index" 
                              class="bg-blue-900/60 text-blue-300 px-3 py-1 rounded-lg text-sm"
                            >
                              {{ seat.row }}{{ seat.column || seat.number }}
                            </span>
                            <!-- Si no hay asientos específicos, mostrar cantidad -->
                            <span v-if="!ticket.seats || ticket.seats.length === 0" class="text-gray-400 text-sm">
                              {{ ticket.seat_count || 1 }} asientos
                            </span>
                          </div>
                        </div>
                        
                        <!-- Precio total -->
                        <div class="mb-3">
                          <h5 class="text-sm font-bold text-gray-300 mb-1">Precio total:</h5>
                          <p class="text-green-400 text-lg font-bold">
                            {{ ticket.total_pay ? `$${ticket.total_pay}` : (ticket.price ? `$${ticket.price}` : 'No disponible') }}
                          </p>
                        </div>
                        
                        <!-- Fecha de compra -->
                        <div class="mb-3" v-if="ticket.purchase_date || ticket.created_at">
                          <h5 class="text-sm font-bold text-gray-300 mb-1">Fecha de compra:</h5>
                          <p class="text-gray-400 text-sm">
                            {{ formatDate(ticket.purchase_date || ticket.created_at) }}
                          </p>
                        </div>
                        
                        <!-- Código QR real -->
                        <div class="mt-4 text-center md:text-left">
                          <h5 class="text-sm font-bold text-gray-300 mb-2">Código QR:</h5>
                          <div class="bg-white inline-block p-4 rounded-lg">
                            <QRCode
                              :value="getTicketUrl(ticket)"
                              :size="128"
                              level="H"
                              class="mx-auto md:mx-0"
                            />
                          </div>
                          <p class="text-gray-400 text-xs mt-2">
                            Presenta este código en la entrada del cine
                          </p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Botón para cerrar detalles -->
                    <div class="mt-5 text-center">
                      <button 
                        @click="expandedTicketId = null" 
                        class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center"
                      >
                        <i class="fas fa-chevron-up mr-2"></i> Cerrar detalles
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Vista de promociones exclusivas -->
            <div v-else-if="activeTab === 'promociones'">
              <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-tags mr-3 text-blue-500"></i> 
                Promociones Exclusivas
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div 
                  v-for="(promo, index) in userPromotions" 
                  :key="index" 
                  class="bg-gradient-to-br from-blue-900/50 to-indigo-900/50 border border-blue-800/50 rounded-lg overflow-hidden shadow-lg"
                >
                  <div class="h-3 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                  
                  <div class="p-5">
                    <div class="categoria-badge mb-3">{{ promo.categoria }}</div>
                    <h3 class="text-xl font-bold text-white mb-2">{{ promo.titulo }}</h3>
                    <p class="text-gray-300 mb-4">{{ promo.descripcion }}</p>
                    
                    <div class="flex items-center text-blue-300 text-sm mb-4">
                      <i class="fas fa-calendar-alt mr-2"></i>
                      <span>{{ promo.validez }}</span>
                    </div>
                    
                    <div class="border-t border-blue-800/30 pt-4 flex items-center justify-between">
                      <div class="text-blue-400 font-bold text-lg">
                        {{ promo.descuento || promo.precio }}
                      </div>
                      
                      <button class="btn-promo">
                        {{ promo.botonTexto }}
                        <i class="fas fa-chevron-right ml-2"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
    
    <LandingPageFooter />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '~/stores/auth';
import { useTicketsStore } from '~/stores/tickets';
import { useRouter, useRoute } from 'vue-router';
import QRCode from 'qrcode.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const ticketsStore = useTicketsStore();
const { $toast } = useNuxtApp();

// Pestaña activa - por defecto 'perfil', pero puede ser cambiada por parámetros de URL
const activeTab = ref('perfil');

// Variable para controlar qué ticket está expandido
const expandedTicketId = ref(null);

// Datos del formulario de perfil
const profileData = ref({
  name: '',
  last_name: '',
  email: ''
});

// Datos para cambiar contraseña
const passwordData = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

// Promociones exclusivas para usuarios
const userPromotions = ref([
  {
    titulo: 'Descuento especial para miembros',
    categoria: 'Exclusivo',
    descripcion: 'Como miembro registrado, disfruta de un 15% de descuento adicional en todas tus compras de entradas de lunes a jueves.',
    validez: 'Válido todo el año',
    descuento: '15% de descuento',
    botonTexto: 'Usar ahora'
  },
  {
    titulo: 'Cumpleaños Doble',
    categoria: 'Cumpleaños',
    descripcion: 'En el mes de tu cumpleaños, trae a un acompañante gratis al comprar tu entrada. Solo para usuarios registrados.',
    validez: 'Mes de tu cumpleaños',
    descuento: '2x1 en entradas',
    botonTexto: 'Reclamar'
  },
  {
    titulo: 'Prioridad de pre-venta',
    categoria: 'Preventa',
    descripcion: 'Acceso exclusivo a la preventa de los estrenos más esperados 48 horas antes de la apertura al público general.',
    validez: 'Grandes estrenos',
    precio: 'Acceso Exclusivo',
    botonTexto: 'Ver estrenos'
  },
  {
    titulo: 'Descuento en Combo Familiar',
    categoria: 'Snacks',
    descripcion: 'Llévate el combo familiar con un 20% de descuento. Incluye 4 refrescos medianos y 2 palomitas grandes.',
    validez: 'Fines de semana',
    descuento: '20% de descuento',
    botonTexto: 'Usar promo'
  }
]);

// Verifica si el usuario está autenticado, si no redirige al login
onMounted(async () => {
  // Intentamos cargar el usuario si no está ya cargado
  if (!authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      console.error('Error al cargar datos del usuario:', error);
    }
  }
  
  // Verificamos si está autenticado
  if (!authStore.isAuthenticated || !authStore.user) {
    $toast.error('Debes iniciar sesión para acceder a esta página');
    router.push('/login');
    return;
  }
  
  // Cargar datos del usuario al perfil si existe
  if (authStore.user) {
    profileData.value = {
      name: authStore.user.name || '',
      last_name: authStore.user.last_name || '',
      email: authStore.user.email || ''
    };
  } else {
    // Si el usuario no existe en el store, inicializamos con valores vacíos
    profileData.value = {
      name: '',
      last_name: '',
      email: ''
    };
  }
  
  // Cargar tickets del usuario
  try {
    await ticketsStore.fetchUserTickets();
  } catch (error) {
    console.error('Error al cargar tickets en onMounted:', error);
  }
  
  // Si hay un parámetro 'tab' en la URL, activamos esa pestaña
  if (route.query.tab && ['perfil', 'tickets', 'promociones'].includes(route.query.tab)) {
    activeTab.value = route.query.tab;
  }
});

// Métodos
const updateProfile = async () => {
  // Aquí iría la lógica para actualizar el perfil del usuario
  // Esta es una simulación
  
  setTimeout(() => {
    $toast.success('Perfil actualizado correctamente');
    
    // Actualizar datos del usuario en la tienda si fuera necesario
    // authStore.user.name = profileData.value.name;
    // authStore.user.last_name = profileData.value.last_name;
    
    // Resetear campos de contraseña
    passwordData.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    };
  }, 1000);
};

const refreshTickets = async () => {
  try {
    await ticketsStore.fetchUserTickets();
    $toast.success('Entradas actualizadas correctamente');
    
    // Depuración: Imprime la estructura de los tickets en la consola
    console.log('Tickets cargados:', JSON.stringify(ticketsStore.userTickets, null, 2));
  } catch (error) {
    $toast.error('Error al actualizar entradas');
    console.error('Error al actualizar entradas:', error);
  }
};

const viewTicketDetails = (ticketId) => {
  // Si ya está expandido, colapsarlo
  if (expandedTicketId.value === ticketId) {
    expandedTicketId.value = null;
    return;
  }
  
  // Expandir el ticket y mostrar mensaje de carga
  expandedTicketId.value = ticketId;
  $toast.info(`Cargando detalles del ticket #${ticketId}...`);
  
  // Si el store tiene una función para obtener detalles de tickets, la usamos
  if (ticketId && typeof ticketsStore.fetchTicketDetails === 'function') {
    ticketsStore.fetchTicketDetails(ticketId)
      .then(ticketDetails => {
        // El ticket ya se actualizará automáticamente en el store
        $toast.success('Detalles cargados correctamente');
      })
      .catch(error => {
        console.error('Error al obtener detalles del ticket:', error);
        $toast.error('Error al cargar los detalles del ticket');
      });
  }
};

const handleLogout = async () => {
  await authStore.logout();
  $toast.info('Has cerrado sesión correctamente');
  router.push('/');
};

// Formatea la fecha para mostrarla de forma amigable
const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible';
  
  try {
    const date = new Date(dateString);
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) {
      return 'Fecha inválida';
    }
    
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
  } catch (error) {
    console.error('Error al formatear fecha:', error);
    return 'Error en fecha';
  }
};

// Genera una URL para el código QR que incluye la información del ticket
const getTicketUrl = (ticket) => {
  // URL base de la web - modificar según el dominio de producción
  const baseUrl = window.location.origin;
  
  // Crear URL para ver detalles de ticket
  const ticketUrl = `${baseUrl}/ticket/${ticket.id}?code=${ticket.ticket_code || ticket.code || ticket.confirmation_code || 'TKT-' + ticket.id.toString().padStart(6, '0')}`;
  
  return ticketUrl;
};

// Define el middleware de autenticación
definePageMeta({
  middleware: ['auth']
});
</script>

<style scoped>
.categoria-badge {
  display: inline-block;
  background-color: #0078C8;
  color: white;
  border-radius: 20px;
  padding: 4px 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.btn-promo {
  display: inline-flex;
  align-items: center;
  background-color: transparent;
  color: #00A0E4;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid #00A0E4;
  transition: all 0.3s ease;
}

.btn-promo:hover {
  background-color: #00A0E4;
  color: white;
  transform: translateX(5px);
}
</style>
