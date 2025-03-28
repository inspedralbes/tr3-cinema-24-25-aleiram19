<template>
  <div>
    <div v-if="loading" class="min-h-screen flex items-center justify-center py-12 px-4 bg-blue-900 bg-opacity-90">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
    </div>
    <login-UserLoginOptions v-else-if="!isAuthenticated" />
    <login-UserLoggedForm v-else-if="isAuthenticated && ticketsStore.selectedSeats.length > 0" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '~/stores/auth';
import { useTicketsStore } from '~/stores/tickets';

const router = useRouter();
const authStore = useAuthStore();
const ticketsStore = useTicketsStore();
const loading = ref(true);

const isAuthenticated = ref(false);

onMounted(async () => {
  // Asegurarse de que tenemos la información de autenticación actualizada
  await authStore.fetchUser().catch(() => {});
  
  isAuthenticated.value = authStore.isAuthenticated;
  
  // Verificar si el usuario está autenticado
  if (isAuthenticated.value) {
    // Si está autenticado, verificar si hay datos de asientos seleccionados
    if (ticketsStore.selectedSeats.length === 0) {
      // Si no hay asientos seleccionados, redirigir a la selección de película
      router.push('/select-movie');
    }
    // Si hay asientos seleccionados, mostrará el UserLoggedForm que hemos definido en el template
  }
  
  loading.value = false;
});
</script>

<script>
export default {
  name: 'CheckoutOptionsPage',
  head() {
    return {
      title: 'Completar Compra - CineXperience',
      meta: [
        { hid: 'description', name: 'description', content: 'Elige cómo deseas continuar con tu compra en CineXperience' }
      ]
    }
  }
}
</script>
