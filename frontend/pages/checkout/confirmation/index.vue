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
import { useTicketsStore } from '~/stores/tickets';
import { useAuthStore } from '~/stores/auth';

const router = useRouter();
const { $toast } = useNuxtApp();
const ticketsStore = useTicketsStore();
const authStore = useAuthStore();

// Usamos un código de confirmación provisional que se actualizará
const confirmationCode = ref('Generando...');

// Datos del ticket iniciales
const ticketData = ref({
  movieTitle: 'Película no disponible',
  showtime: 'Fecha no disponible',
  room: 'Sala no disponible',
  seats: 'Asientos no disponibles',
  buyerName: 'Invitado',
  buyerEmail: 'invitado@example.com'
});

// Comprobar que hay datos válidos y generar confirmación
onMounted(() => {
  
  // Verificar si hay asientos seleccionados y una sesión seleccionada
  if (!ticketsStore.currentTicket || !ticketsStore.selectedSeats || ticketsStore.selectedSeats.length === 0) {
    $toast.error('No hay información de compra para confirmar');
    router.push('/select-movie');
    return;
  }

  // Actualizar los datos del ticket con la información real del store
  ticketData.value = {
    movieTitle: ticketsStore.currentTicket.screening?.movie?.title || 
               ticketsStore.currentTicket.screening?.title || 
               'No disponible',
    showtime: formatDateTime(ticketsStore.currentTicket.screening?.date_time) || 
              formatDateTime(ticketsStore.currentTicket.screening?.date + ' ' + ticketsStore.currentTicket.screening?.time) || 
              'Fecha no disponible',
    room: ticketsStore.currentTicket.screening?.auditorium?.name || 
          `Sala ${ticketsStore.currentTicket.screening?.room_id}` || 
          'No disponible',
    seats: formatSeats(ticketsStore.selectedSeats),
    buyerName: authStore.user?.name || 'Invitado',
    buyerEmail: authStore.user?.email || 'invitado@example.com'
  };

  // Crear un ID de confirmación solo para mostrar al usuario en la UI
  // pero no lo usaremos para el backend
  confirmationCode.value = generateRandomCode(8);
  
  // Procesar compra directamente sin método de pago
  processDirectPurchase();
});

// Formatear la fecha y hora
function formatDateTime(dateTimeStr) {
  if (!dateTimeStr) return null;
  
  try {
    const date = new Date(dateTimeStr);
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) {
      return null;
    }
    
    return date.toLocaleString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    console.error('Error al formatear fecha:', error);
    return null;
  }
}

// Formatear los asientos seleccionados
function formatSeats(seats) {
  if (!seats || seats.length === 0) return 'Ninguno seleccionado';
  
  return seats.map(seat => {
    // Manejar diferentes estructuras de asientos
    if (seat.number) {
      return seat.number;
    } else if (seat.row && seat.column) {
      return `${seat.row}${seat.column}`;
    } else {
      return 'Desconocido';
    }
  }).join(', ');
}

// Procesar compra directamente sin método de pago
async function processDirectPurchase() {
  try {
    // Mostrar estado de carga
    $toast.info('Procesando compra...');
    
    // Ya generamos el código de confirmación para la UI, pero no lo usamos para el backend
    
    // Calcular el total a pagar (si no lo tenemos ya)
    let totalPay;
    if (ticketsStore.currentTicket?.screening?.price) {
      totalPay = ticketsStore.selectedSeats.length * ticketsStore.currentTicket.screening.price;
    } else {
      // Valor por defecto si no hay precio definido
      totalPay = ticketsStore.selectedSeats.length * 100;
    }
    
    // Utilizar el método de confirmación de tickets del store consolidado
    const reservationData = {
      screening_id: ticketsStore.currentTicket?.screening_id,
      seats: ticketsStore.selectedSeats.map(seat => ({
        number: seat.number || `${seat.row}${seat.column}`
      })),
      // eliminamos confirmation_code
      quantity: ticketsStore.selectedSeats.length,
      total_pay: totalPay
    };
    
    // Confirmar tickets
    const result = await ticketsStore.confirmTickets(reservationData);
    
    if (result) {
      // Notificar al usuario que la compra fue exitosa
      $toast.success('¡Compra realizada con éxito!');
    } else {
      // Manejar error
      $toast.error(ticketsStore.error || 'Error al procesar la compra');
    }
  } catch (error) {
    // Manejar error inesperado
    $toast.error('Ha ocurrido un error inesperado');
    console.error('Error al procesar la compra:', error);
  }
}

// Función auxiliar para generar códigos de confirmación
function generateRandomCode(length) {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  let code = '';
  for (let i = 0; i < length; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}

// Ya no necesitamos una función de generación de código, el backend se encarga de eso

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
