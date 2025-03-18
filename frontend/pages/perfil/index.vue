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
              <h2 class="text-xl font-bold text-white mb-1">{{ authStore.user.name }} {{ authStore.user.last_name }}</h2>
              <p class="text-gray-300 text-sm mb-3">{{ authStore.user.email }}</p>
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
                
                <div class="border-t border-gray-700 pt-6">
                  <h3 class="text-xl font-bold text-white mb-4">Cambiar contraseña</h3>
                  
                  <div class="space-y-4">
                    <div>
                      <label class="block text-gray-300 text-sm font-medium mb-2">Contraseña actual</label>
                      <input 
                        v-model="passwordData.current_password" 
                        type="password" 
                        class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                        placeholder="Ingresa tu contraseña actual"
                      >
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Nueva contraseña</label>
                        <input 
                          v-model="passwordData.new_password" 
                          type="password" 
                          class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                          placeholder="Nueva contraseña"
                        >
                      </div>
                      
                      <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Confirmar nueva contraseña</label>
                        <input 
                          v-model="passwordData.new_password_confirmation" 
                          type="password" 
                          class="w-full bg-navy-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                          placeholder="Confirma tu nueva contraseña"
                        >
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="flex justify-end">
                  <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center"
                  >
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                  </button>
                </div>
              </form>
            </div>
            
            <!-- Vista de tickets/entradas -->
            <div v-else-if="activeTab === 'tickets'">
              <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-ticket-alt mr-3 text-blue-500"></i> 
                Mis Entradas
              </h2>
              
              <div v-if="ticketsStore.userTickets.length === 0" class="text-center py-12">
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
                <div 
                  v-for="ticket in ticketsStore.userTickets" 
                  :key="ticket.id" 
                  class="bg-navy-900/80 border border-gray-800 rounded-lg overflow-hidden"
                >
                  <div class="p-5">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                      <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold text-white mb-1">{{ ticket.movie_title }}</h3>
                        <div class="flex items-center text-gray-400 text-sm">
                          <i class="fas fa-calendar-alt mr-2"></i>
                          <span>{{ formatDate(ticket.screening_date) }} | {{ ticket.screening_time }}</span>
                        </div>
                      </div>
                      
                      <div class="flex flex-col md:flex-row items-start md:items-center md:space-x-4">
                        <div class="bg-blue-900 py-1 px-3 rounded-full text-sm text-blue-300 flex items-center mb-3 md:mb-0">
                          <i class="fas fa-couch mr-1"></i> {{ ticket.seat_count }} asientos
                        </div>
                        
                        <button 
                          @click="viewTicketDetails(ticket.id)"
                          class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium py-2 px-4 rounded-full transition-colors flex items-center"
                        >
                          <i class="fas fa-eye mr-2"></i> Ver Detalles
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="bg-navy-950 px-5 py-3 flex flex-col sm:flex-row sm:items-center justify-between">
                    <div class="text-gray-400 text-sm mb-2 sm:mb-0">
                      <span class="text-gray-500">Código:</span> 
                      <span class="font-mono font-medium text-white">{{ ticket.ticket_code }}</span>
                    </div>
                    
                    <div class="text-gray-400 text-sm flex items-center">
                      <span class="mr-2">Estado:</span>
                      <span 
                        :class="[
                          'px-2 py-1 rounded text-xs font-medium',
                          ticket.status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400'
                        ]"
                      >
                        {{ ticket.status === 'active' ? 'Activo' : 'Usado' }}
                      </span>
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
    
    <LandingPageFooter />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '~/stores/auth';
import { useTicketsStore } from '~/stores/tickets';
import { useRouter } from 'vue-router';

const router = useRouter();
const authStore = useAuthStore();
const ticketsStore = useTicketsStore();
const { $toast } = useNuxtApp();

// Pestaña activa
const activeTab = ref('perfil');

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
  if (!authStore.isAuthenticated) {
    $toast.error('Debes iniciar sesión para acceder a esta página');
    router.push('/login');
    return;
  }
  
  // Cargar datos del usuario al perfil
  profileData.value = {
    name: authStore.user.name,
    last_name: authStore.user.last_name,
    email: authStore.user.email
  };
  
  // Cargar tickets del usuario
  await ticketsStore.fetchUserTickets();
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

const viewTicketDetails = (ticketId) => {
  // Aquí iría la lógica para ver los detalles del ticket
  $toast.info(`Viendo detalles del ticket #${ticketId}`);
};

const handleLogout = async () => {
  await authStore.logout();
  $toast.info('Has cerrado sesión correctamente');
  router.push('/');
};

// Formatea la fecha para mostrarla de forma amigable
const formatDate = (dateString) => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
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
