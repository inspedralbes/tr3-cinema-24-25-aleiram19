<template>
  <div class="min-h-screen flex flex-col bg-blue-900 bg-opacity-90">
    <!-- Navbar incorporado -->
    <NavBar />
    
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 mt-20">
    <div class="w-full max-w-2xl space-y-8 bg-blue-900 bg-opacity-80 rounded-xl shadow-xl p-8 sm:p-10 text-white">
      <!-- Header -->
      <div class="text-center">
        <h2 class="text-3xl font-bold mb-2">Continuar con tu compra</h2>
        <p class="text-gray-400 text-sm">
          Completa tus datos para continuar con la compra
        </p>
      </div>

      <!-- Resumen de compra -->
      <div v-if="ticketStore.getSelectedScreening" class="bg-blue-800 bg-opacity-50 rounded-lg p-4 border border-blue-700">
        <h3 class="text-xl font-semibold mb-3 flex items-center">
          <i class="fas fa-receipt mr-2"></i> Resumen de tu compra
        </h3>
        
        <div class="space-y-4">
          <!-- Información de película -->
          <div class="flex items-start space-x-4">
            <div v-if="ticketStore.getSelectedScreening.movie?.poster_path" class="flex-shrink-0">
              <img 
                :src="`https://image.tmdb.org/t/p/w92${ticketStore.getSelectedScreening.movie.poster_path}`" 
                :alt="ticketStore.getSelectedScreening.movie?.title" 
                class="w-16 h-24 object-cover rounded-md"
              >
            </div>
            <div>
              <h4 class="font-medium text-lg text-white">{{ ticketStore.getSelectedScreening.movie?.title }}</h4>
              <p class="text-gray-300 text-sm">{{ formatDate(ticketStore.getSelectedScreening.date) }} - {{ formatTime(ticketStore.getSelectedScreening.time) }}</p>
              <p class="text-blue-300 text-sm">{{ ticketStore.getSelectedScreening.movie?.genres?.join(', ') }}</p>
              <p class="text-gray-300 text-sm">Sala {{ ticketStore.getSelectedScreening.room_id }}</p>
            </div>
          </div>
          
          <!-- Información de asientos -->
          <div>
            <h4 class="font-medium text-white mb-1">Asientos seleccionados:</h4>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="(seat, index) in ticketStore.getSelectedSeats" 
                :key="index"
                class="bg-blue-700 text-white px-2 py-1 rounded text-sm">
                Fila {{ seat.row }} - Asiento {{ seat.column }}
              </span>
            </div>
          </div>
          
          <!-- Precio total -->
          <div class="border-t border-blue-700 pt-3 mt-3">
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Precio por entrada:</span>
              <span class="text-white font-medium">{{ formatCurrency(ticketStore.getSelectedScreening.price || 100) }}</span>
            </div>
            <div class="flex justify-between items-center mt-1">
              <span class="text-gray-300">Cantidad de entradas:</span>
              <span class="text-white font-medium">{{ ticketStore.getSelectedSeats.length }}</span>
            </div>
            <div class="flex justify-between items-center mt-2 text-lg font-bold">
              <span>Total a pagar:</span>
              <span class="text-blue-300">{{ formatCurrency(ticketStore.getTotalAmount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Alerta de error -->
      <div v-if="error" class="bg-red-600 bg-opacity-80 text-white p-3 rounded-lg text-sm">
        <div class="flex items-center space-x-2">
          <i class="fas fa-exclamation-circle"></i>
          <div>
            <p class="font-medium">Error</p>
            <p>{{ error }}</p>
          </div>
        </div>
        <button @click="error = null" class="absolute top-2 right-2 text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="mt-8 space-y-6">
        <!-- Nombre -->
        <div class="space-y-2">
          <label for="name" class="block text-sm font-medium text-gray-300">Nombre</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              id="name" 
              v-model="form.name" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu nombre" 
              required
            >
          </div>
        </div>
        
        <!-- Apellido -->
        <div class="space-y-2">
          <label for="last_name" class="block text-sm font-medium text-gray-300">Apellido</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              id="last_name" 
              v-model="form.last_name" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu apellido" 
              required
            >
          </div>
        </div>

        <!-- Email -->
        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium text-gray-300">Correo electrónico</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-500"></i>
            </div>
            <input 
              id="email" 
              v-model="form.email" 
              type="email" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu correo electrónico" 
              required
            >
          </div>
        </div>

        <!-- Teléfono (opcional) -->
        <div class="space-y-2">
          <label for="phone" class="block text-sm font-medium text-gray-300">Teléfono (opcional)</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-phone text-gray-500"></i>
            </div>
            <input 
              id="phone" 
              v-model="form.phone" 
              type="tel" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Ingresa tu número de teléfono" 
            >
          </div>
        </div>

        <!-- Términos y condiciones -->
        <div class="space-y-2">
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input 
                id="terms" 
                v-model="form.acceptTerms" 
                type="checkbox" 
                required
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800"
              >
            </div>
            <div class="ml-3 text-sm">
              <label for="terms" class="text-gray-300">
                Acepto los <a href="#" class="text-blue-500 hover:text-blue-400 transition-colors">Términos y Condiciones</a> y la <a href="#" class="text-blue-500 hover:text-blue-400 transition-colors">Política de Privacidad</a>
              </label>
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col space-y-3">
          <button 
            type="submit" 
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!isFormValid || loading"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-spinner fa-spin text-blue-400"></i>
            </span>
            <span v-else class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-arrow-right group-hover:text-blue-400 text-blue-500 transition-colors"></i>
            </span>
            Continuar con la Compra
          </button>
          
          <button 
            type="button" 
            @click="goBack" 
            class="group relative w-full flex justify-center py-3 px-4 border border-gray-700 rounded-lg text-white font-medium bg-transparent hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i class="fas fa-arrow-left group-hover:text-blue-400 text-gray-500 transition-colors"></i>
            </span>
            Volver
          </button>
        </div>

        <!-- Nota de creación de cuenta -->
        <div class="text-center text-xs text-gray-400 mt-4">
          <p>¿Prefieres crear una cuenta? <a href="/login" class="text-blue-500 hover:text-blue-400 transition-colors">Regístrate aquí</a></p>
        </div>
      </form>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useTicketStore } from '~/stores/ticket';
import NavBar from '~/components/LandingPage/NavBar.vue';

const router = useRouter();
const ticketStore = useTicketStore();
const { $toast } = useNuxtApp();

const loading = ref(false);
const error = ref(null);

const form = ref({
  name: '',
  last_name: '',
  email: '',
  phone: '',
  acceptTerms: false
});

const isFormValid = computed(() => {
  return form.value.name && 
         form.value.last_name &&
         form.value.email && 
         form.value.acceptTerms;
});

const goBack = () => {
  router.back();
};

// Función para formatear moneda
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount);
};

// Funciones para formatear fecha y hora
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const formatTime = (timeString) => {
  return timeString;
};

const submitForm = async () => {
  if (!isFormValid.value) return;
  
  try {
    loading.value = true;
    
    // Guardar los datos del invitado en el store
    ticketStore.setGuestInfo({
      name: form.value.name,
      last_name: form.value.last_name,
      email: form.value.email,
      phone: form.value.phone || null
    });
    
    // Mostrar mensaje de éxito
    $toast.success('Información guardada correctamente');
    
    // Redireccionar a la página de confirmación
    router.push('/checkout/confirmation');
  } catch (err) {
    error.value = err.message || 'Ha ocurrido un error al procesar tu información';
  } finally {
    loading.value = false;
  }
};
</script>
