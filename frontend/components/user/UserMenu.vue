<template>
  <div class="relative flex items-center">
    <!-- Botón de perfil con avatar -->
    <button 
      @click="goToProfile" 
      class="flex items-center space-x-2 text-white font-medium rounded-full px-3 py-2 hover:bg-white/10 transition-colors"
    >
      <div class="bg-blue-600 p-2 rounded-full">
        <i class="fas fa-user"></i>
      </div>
      <span class="hidden md:inline">{{ user.name }}</span>
      <i class="fas fa-user-circle text-xs"></i>
    </button>
    
    <!-- Botón para abrir menú desplegable -->
    <button 
      @click="toggleMenu"
      class="ml-1 text-white hover:bg-white/10 transition-colors rounded-full p-2"
    >
      <i class="fas fa-chevron-down text-xs"></i>
    </button>
    
    <!-- Menú desplegable -->
    <div 
      v-show="isOpen" 
      class="absolute right-0 mt-2 w-48 bg-blue-900 rounded-lg shadow-lg py-2 z-50 border border-blue-800"
    >
      <div class="px-4 py-2 border-b border-blue-800">
        <p class="text-white font-medium">{{ user.name }} {{ user.last_name }}</p>
        <p class="text-gray-400 text-sm truncate">{{ user.email }}</p>
      </div>
      
      <NuxtLink to="/perfil" class="block px-4 py-2 text-white hover:bg-blue-800 transition-colors">
        <i class="fas fa-user-circle mr-2"></i> Mi perfil
      </NuxtLink>
      
      <NuxtLink to="/mis-tickets" class="block px-4 py-2 text-white hover:bg-blue-800 transition-colors">
        <i class="fas fa-ticket-alt mr-2"></i> Mis entradas
      </NuxtLink>
      
      <div class="border-t border-blue-800 mt-2 pt-2">
        <button 
          @click="logout" 
          class="block w-full text-left px-4 py-2 text-white hover:bg-blue-800 transition-colors"
        >
          <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '~/stores/auth';
import { useRouter } from 'vue-router';

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const router = useRouter();
const authStore = useAuthStore();
const { $toast } = useNuxtApp();
const isOpen = ref(false);

const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};

const goToProfile = () => {
  // Navegar directamente a la página de perfil
  router.push('/perfil');
};

const closeMenu = (e) => {
  // Cerrar el menú si se hace clic fuera de él
  if (isOpen.value) {
    isOpen.value = false;
  }
};

const logout = async () => {
  await authStore.logout();
  $toast.info('Has cerrado sesión correctamente');
  router.push('/');
};

// Agregar listener para cerrar el menú al hacer clic fuera
onMounted(() => {
  document.addEventListener('click', closeMenu);
});

onUnmounted(() => {
  document.removeEventListener('click', closeMenu);
});
</script>
