<template>
  <nav :class="[
    'fixed top-0 left-0 w-full z-50 transition-all duration-300 ease-in-out',
    'py-5 md:py-5 border-b border-transparent',
    isScrolled ? 'bg-navy-900/95 shadow-md py-3 md:py-3' : 'bg-blue-900/80 backdrop-blur-sm'
  ]">
    <div class="container mx-auto px-4 flex items-center justify-between">
      <!-- Logo y Nombre del Cine -->
      <NuxtLink to="/" class="text-2xl font-bold">
        <span class="bg-gradient-to-r from-white to-blue-400 bg-clip-text text-transparent font-extrabold tracking-wider">
          CineXeperience
        </span>
      </NuxtLink>
      
      <!-- Botón hamburguesa para móviles -->
      <button 
        class="lg:hidden text-white focus:outline-none" 
        @click="toggleMobileMenu"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      
      <!-- Links de navegación para escritorio -->
      <div class="hidden lg:flex lg:items-center space-x-6">
        <NuxtLink 
          to="/cartelera"
          class="text-white font-medium px-4 py-2 relative group transition-all duration-300 ease-in-out hover:text-blue-400"
        >
          <i class="fas fa-film mr-1"></i> Cartelera
          <span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-blue-400 transform -translate-x-1/2 transition-all duration-300 group-hover:w-full"></span>
        </NuxtLink>
        
        <NuxtLink 
          to="/promociones"
          class="text-white font-medium px-4 py-2 relative group transition-all duration-300 ease-in-out hover:text-blue-400"
        >
          <i class="fas fa-tags mr-1"></i> Promociones
          <span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-blue-400 transform -translate-x-1/2 transition-all duration-300 group-hover:w-full"></span>
        </NuxtLink>
        
        <a 
          href="#"
          class="text-white font-medium px-4 py-2 relative group transition-all duration-300 ease-in-out hover:text-blue-400"
        >
          <i class="fas fa-map-marker-alt mr-1"></i> Cines
          <span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-blue-400 transform -translate-x-1/2 transition-all duration-300 group-hover:w-full"></span>
        </a>
        
        <NuxtLink 
          to="/login"
          class="text-white font-medium px-6 py-2 rounded-full border border-white/30 backdrop-blur-sm transition-all duration-300 ease-in-out hover:bg-white/10 hover:border-white/50"
        >
          <i class="fas fa-user mr-1"></i> Iniciar sesión
        </NuxtLink>
        
        <NuxtLink 
          to="/cartelera"
          class="ml-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2 rounded-full transition-all duration-300 ease-in-out hover:shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-0.5"
        >
          COMPRAR ENTRADAS
        </NuxtLink>
      </div>
    </div>
    
    <!-- Menú móvil -->
    <div 
      v-if="mobileMenuOpen" 
      class="lg:hidden absolute top-full left-0 w-full bg-navy-900/95 backdrop-blur-sm py-4 shadow-lg transform transition-transform duration-300 ease-in-out"
    >
      <div class="container mx-auto px-4 flex flex-col space-y-4">
        <NuxtLink 
          to="/cartelera"
          class="text-white font-medium py-3 px-4 rounded-lg hover:bg-white/10 transition-colors"
        >
          <i class="fas fa-film mr-2"></i> Cartelera
        </NuxtLink>
        
        <NuxtLink 
          to="/promociones"
          class="text-white font-medium py-3 px-4 rounded-lg hover:bg-white/10 transition-colors"
        >
          <i class="fas fa-tags mr-2"></i> Promociones
        </NuxtLink>
        
        <a 
          href="#"
          class="text-white font-medium py-3 px-4 rounded-lg hover:bg-white/10 transition-colors"
        >
          <i class="fas fa-map-marker-alt mr-2"></i> Cines
        </a>
        
        <NuxtLink 
          to="/login"
          class="text-white font-medium py-3 px-4 rounded-lg hover:bg-white/10 transition-colors"
        >
          <i class="fas fa-user mr-2"></i> Iniciar sesión
        </NuxtLink>
        
        <NuxtLink 
          to="/cartelera"
          class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors"
        >
          COMPRAR ENTRADAS
        </NuxtLink>
      </div>
    </div>

    <!-- Barra de compra rápida (móvil) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-navy-900/95 backdrop-blur-sm border-t border-white/10 p-3 transform transition-transform duration-300 ease-in-out" :class="isScrolled ? 'translate-y-0' : 'translate-y-full'">
      <div class="flex items-center justify-between gap-2">
        <select class="flex-1 bg-white/10 text-white text-sm rounded-lg px-3 py-2 border border-white/20 focus:border-blue-500 focus:outline-none">
          <option value="">Seleccionar película</option>
          <option>Dune: Parte Dos</option>
          <option>Godzilla vs Kong</option>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-500 transition-colors">
          Comprar
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const isScrolled = ref(false);
const mobileMenuOpen = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>