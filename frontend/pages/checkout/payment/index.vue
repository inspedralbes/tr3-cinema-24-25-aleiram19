<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-blue-900 bg-opacity-90">
    <div class="w-full max-w-md space-y-8 bg-blue-900 bg-opacity-80 rounded-xl shadow-xl p-8 sm:p-10 text-white">
      <!-- Header -->
      <div class="text-center">
        <h2 class="text-3xl font-bold mb-2">Método de Pago</h2>
        <p class="text-gray-400 text-sm">
          Por favor, completa la información de pago para finalizar tu compra
        </p>
      </div>

      <!-- Resumen de la compra -->
      <div class="bg-blue-800 bg-opacity-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold mb-2">Resumen de Compra</h3>
        <div v-if="ticketStore.selectedScreening" class="space-y-2">
          <p class="flex justify-between">
            <span>Película:</span>
            <span class="font-medium">{{ ticketStore.selectedScreening.movie?.title || 'Película seleccionada' }}</span>
          </p>
          <p class="flex justify-between">
            <span>Fecha y hora:</span>
            <span class="font-medium">{{ formatDateTime(ticketStore.selectedScreening.start_time) }}</span>
          </p>
          <p class="flex justify-between">
            <span>Sala:</span>
            <span class="font-medium">{{ ticketStore.selectedScreening.room?.name || 'Sala' }}</span>
          </p>
          <p class="flex justify-between">
            <span>Asientos:</span>
            <span class="font-medium">{{ formatSeats(ticketStore.selectedSeats) }}</span>
          </p>
          <div class="border-t border-blue-700 my-2 pt-2">
            <p class="flex justify-between font-bold">
              <span>Total:</span>
              <span>${{ ticketStore.getTotalAmount.toFixed(2) }}</span>
            </p>
          </div>
        </div>
        <div v-else class="text-center text-yellow-400 p-2">
          <p>No hay información de la función seleccionada</p>
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

      <!-- Formulario de pago -->
      <form @submit.prevent="submitPayment" class="mt-8 space-y-6">
        <!-- Tipo de tarjeta -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-300">Tipo de tarjeta</label>
          <div class="flex space-x-4">
            <div 
              v-for="(cardType, index) in cardTypes" 
              :key="index"
              @click="form.cardType = cardType.value"
              class="cursor-pointer p-3 border rounded-lg flex items-center justify-center"
              :class="form.cardType === cardType.value ? 'border-blue-500 bg-blue-800' : 'border-gray-700 bg-blue-900'"
            >
              <i :class="['text-2xl', cardType.icon]"></i>
            </div>
          </div>
        </div>

        <!-- Número de tarjeta -->
        <div class="space-y-2">
          <label for="cardNumber" class="block text-sm font-medium text-gray-300">Número de tarjeta</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-credit-card text-gray-500"></i>
            </div>
            <input 
              id="cardNumber" 
              v-model="form.cardNumber" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="1234 5678 9012 3456" 
              required
              maxlength="19"
              @input="formatCardNumber"
            >
          </div>
        </div>

        <!-- Nombre en la tarjeta -->
        <div class="space-y-2">
          <label for="cardName" class="block text-sm font-medium text-gray-300">Nombre en la tarjeta</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-500"></i>
            </div>
            <input 
              id="cardName" 
              v-model="form.cardName" 
              type="text" 
              class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Nombre del titular" 
              required
            >
          </div>
        </div>

        <!-- Fecha y CVC -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Fecha de expiración -->
          <div class="space-y-2">
            <label for="expiryDate" class="block text-sm font-medium text-gray-300">Fecha de expiración</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-calendar text-gray-500"></i>
              </div>
              <input 
                id="expiryDate" 
                v-model="form.expiryDate" 
                type="text" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="MM/AA" 
                required
                maxlength="5"
                @input="formatExpiryDate"
              >
            </div>
          </div>
          
          <!-- CVC -->
          <div class="space-y-2">
            <label for="cvc" class="block text-sm font-medium text-gray-300">CVC</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-500"></i>
              </div>
              <input 
                id="cvc" 
                v-model="form.cvc" 
                type="text" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-700 rounded-lg bg-blue-800 bg-opacity-50 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="123" 
                required
                maxlength="4"
              >
            </div>
          </div>
        </div>

        <!-- Guardar información (opcional) -->
        <div class="flex items-center">
          <input 
            id="saveInfo" 
            v-model="form.saveInfo" 
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800"
          >
          <label for="saveInfo" class="ml-2 block text-sm text-gray-300">
            Guardar información para futuros pagos
          </label>
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
              <i class="fas fa-credit-card group-hover:text-blue-400 text-blue-500 transition-colors"></i>
            </span>
            Completar Pago
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
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useTicketStore } from '~/stores/ticket';

const router = useRouter();
const ticketStore = useTicketStore();
const { $toast } = useNuxtApp();

const loading = ref(false);
const error = ref(null);

// Tipos de tarjeta disponibles
const cardTypes = [
  { value: 'visa', icon: 'fab fa-cc-visa text-blue-500' },
  { value: 'mastercard', icon: 'fab fa-cc-mastercard text-red-500' },
  { value: 'amex', icon: 'fab fa-cc-amex text-blue-400' },
  { value: 'discover', icon: 'fab fa-cc-discover text-orange-500' }
];

const form = ref({
  cardType: 'visa',
  cardNumber: '',
  cardName: '',
  expiryDate: '',
  cvc: '',
  saveInfo: false
});

// Verificar si hay información de compra disponible
onMounted(() => {
  if (!ticketStore.selectedScreening || ticketStore.selectedSeats.length === 0 || !ticketStore.guestInfo) {
    $toast.error('Información de compra incompleta');
    router.push('/checkout');
  }
});

const isFormValid = computed(() => {
  return form.value.cardType &&
         form.value.cardNumber.replace(/\s/g, '').length >= 13 &&
         form.value.cardName &&
         form.value.expiryDate.length === 5 &&
         form.value.cvc.length >= 3;
});

// Formatear el número de tarjeta con espacios
const formatCardNumber = () => {
  let value = form.value.cardNumber.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
  let formattedValue = '';
  for (let i = 0; i < value.length; i++) {
    if (i > 0 && i % 4 === 0) {
      formattedValue += ' ';
    }
    formattedValue += value[i];
  }
  form.value.cardNumber = formattedValue;
};

// Formatear la fecha de expiración
const formatExpiryDate = () => {
  let value = form.value.expiryDate.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
  if (value.length > 2) {
    form.value.expiryDate = value.substring(0, 2) + '/' + value.substring(2);
  } else {
    form.value.expiryDate = value;
  }
};

// Formatear los asientos seleccionados
const formatSeats = (seats) => {
  if (!seats || seats.length === 0) return 'Ninguno seleccionado';
  
  return seats.map(seat => `${seat.row}${seat.column}`).join(', ');
};

// Formatear la fecha y hora
const formatDateTime = (dateTimeStr) => {
  if (!dateTimeStr) return 'Fecha no disponible';
  
  const date = new Date(dateTimeStr);
  return date.toLocaleString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const goBack = () => {
  router.back();
};

const submitPayment = async () => {
  if (!isFormValid.value) return;
  
  try {
    loading.value = true;
    
    // Preparar la información de pago
    const paymentInfo = {
      card_type: form.value.cardType,
      card_number: form.value.cardNumber.replace(/\s/g, ''),
      card_name: form.value.cardName,
      expiry_date: form.value.expiryDate,
      cvc: form.value.cvc,
      save_info: form.value.saveInfo
    };
    
    // Guardar la información de pago en el store
    ticketStore.setPaymentInfo(paymentInfo);
    
    // Completar la compra
    const result = await ticketStore.completeGuestPurchase();
    
    if (result) {
      $toast.success('¡Compra realizada con éxito!');
      
      // Redireccionar a la página de confirmación
      router.push('/checkout/confirmation');
    } else {
      error.value = ticketStore.error || 'Ha ocurrido un error al procesar el pago';
    }
  } catch (err) {
    error.value = err.message || 'Ha ocurrido un error al procesar el pago';
  } finally {
    loading.value = false;
  }
};
</script>
