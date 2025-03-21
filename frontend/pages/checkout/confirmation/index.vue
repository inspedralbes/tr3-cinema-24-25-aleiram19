<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-blue-900 bg-opacity-90">
    <div class="w-full max-w-md space-y-8 bg-blue-900 bg-opacity-80 rounded-xl shadow-xl p-8 sm:p-10 text-white">
      <!-- Icono de confirmación -->
      <div class="text-center">
        <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-green-100 text-green-500 mb-4">
          <i class="fas fa-check-circle text-5xl"></i>
        </div>
        <h2 class="text-3xl font-bold mb-2">¡Compra Exitosa!</h2>
        <p class="text-gray-400">
          Tu pedido ha sido confirmado y tus boletos están listos
        </p>
      </div>

      <!-- Detalles de la compra -->
      <div class="bg-blue-800 bg-opacity-50 rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4 border-b border-blue-700 pb-2">Detalles de la Confirmación</h3>
        
        <!-- Código de confirmación -->
        <div class="bg-blue-700 bg-opacity-50 rounded-lg p-4 text-center mb-6">
          <p class="text-sm text-gray-300">Código de confirmación</p>
          <p class="text-2xl font-mono font-bold tracking-wider">{{ confirmationCode }}</p>
        </div>
        
        <div class="space-y-4">
          <!-- Película -->
          <div>
            <p class="text-sm text-gray-300">Película</p>
            <p class="font-medium">{{ ticketData.movieTitle || 'No disponible' }}</p>
          </div>
          
          <!-- Fecha y Hora -->
          <div>
            <p class="text-sm text-gray-300">Fecha y Hora</p>
            <p class="font-medium">{{ ticketData.showtime || 'No disponible' }}</p>
          </div>
          
          <!-- Sala -->
          <div>
            <p class="text-sm text-gray-300">Sala</p>
            <p class="font-medium">{{ ticketData.room || 'No disponible' }}</p>
          </div>
          
          <!-- Asientos -->
          <div>
            <p class="text-sm text-gray-300">Asientos</p>
            <p class="font-medium">{{ ticketData.seats || 'No disponible' }}</p>
          </div>
          
          <!-- Información del comprador -->
          <div class="border-t border-blue-700 pt-4">
            <p class="text-sm text-gray-300">Comprador</p>
            <p class="font-medium">{{ ticketData.buyerName }}</p>
            <p class="text-sm text-gray-400">{{ ticketData.buyerEmail }}</p>
          </div>
        </div>
      </div>

      <!-- Instrucciones -->
      <div class="bg-yellow-800 bg-opacity-20 border border-yellow-700 rounded-lg p-4 text-yellow-300 text-sm">
        <div class="flex space-x-2">
          <i class="fas fa-info-circle mt-1"></i>
          <div>
            <p class="font-medium mb-1">Información importante</p>
            <p>Se ha enviado una copia de tus boletos a tu correo electrónico. Puedes presentar este código de confirmación en la entrada del cine.</p>
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-col space-y-3">
        <button 
          @click="downloadTicket" 
          class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <i class="fas fa-download group-hover:text-blue-400 text-blue-500 transition-colors"></i>
          </span>
          Descargar Boletos
        </button>
        
        <button 
          @click="goToHome" 
          class="group relative w-full flex justify-center py-3 px-4 border border-gray-700 rounded-lg text-white font-medium bg-transparent hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <i class="fas fa-home group-hover:text-blue-400 text-gray-500 transition-colors"></i>
          </span>
          Volver al Inicio
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const { $toast } = useNuxtApp();

// Generar un código de confirmación aleatorio
const confirmationCode = ref(generateConfirmationCode());

// Datos del ticket (normalmente vendrían de un API o del store)
const ticketData = ref({
  movieTitle: 'Avatar: El camino del agua',
  showtime: '20 de marzo 2025, 19:30',
  room: 'Sala 5 - 3D',
  seats: 'F12, F13',
  buyerName: 'Invitado',
  buyerEmail: 'invitado@example.com'
});

// Comprobar si hay una confirmación real al montar el componente
onMounted(() => {
  // Aquí podríamos obtener los datos reales de la API o del store
  // Si no hay datos de confirmación, redirigir al inicio
  
  // Esta es una simulación, en una implementación real 
  // verificaríamos si hay datos de compra para mostrar
  const hasConfirmation = true;
  
  if (!hasConfirmation) {
    $toast.error('No hay información de confirmación disponible');
    router.push('/');
  }
});

function generateConfirmationCode() {
  // Generar un código alfanumérico de 8 caracteres
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  let code = '';
  for (let i = 0; i < 8; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}

function downloadTicket() {
  // En una implementación real, aquí se generaría un PDF o similar
  $toast.info('Descargando boletos...');
  
  // Simulamos una descarga después de un segundo
  setTimeout(() => {
    $toast.success('Boletos descargados correctamente');
  }, 1000);
}

function goToHome() {
  router.push('/');
}
</script>
