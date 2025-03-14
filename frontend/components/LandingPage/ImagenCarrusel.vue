<template>
  <div>
    <div id="mainCarousel" class="relative h-screen w-full overflow-hidden">
      <!-- Indicadores -->
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10 flex space-x-2">
        <button 
          v-for="(movie, index) in moviesList" 
          :key="`indicator-${movie.id}`"
          class="w-3 h-3 rounded-full transition-all duration-300 focus:outline-none"
          :class="{ 'bg-blue-600 scale-110': currentSlide === index, 'bg-white opacity-50': currentSlide !== index }"
          @click="setSlide(index)"
          :aria-label="`Slide ${index + 1}`"
        ></button>
      </div>
      
      <!-- Slides -->
      <div class="h-full w-full">
        <div 
          v-for="(movie, index) in moviesList" 
          :key="movie.id" 
          class="absolute inset-0 transition-opacity duration-1000"
          :class="{ 'opacity-100': currentSlide === index, 'opacity-0': currentSlide !== index }"
        >
          <!-- Contenedor de imagen -->
          <div class="relative h-full w-full overflow-hidden">
            <img 
              :src="movie.image" 
              class="w-full h-full object-cover transition-transform duration-10000 ease-in-out"
              :class="{ 'scale-100': currentSlide === index, 'scale-105': currentSlide !== index }"
              :alt="movie.title"
            >
            <!-- Gradiente para mejorar legibilidad -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-900/60 to-transparent"></div>
          </div>
          
          <!-- Contenido -->
          <div class="absolute inset-0 flex items-center">
            <div class="container mx-auto px-4">
              <div class="max-w-3xl ml-10 md:ml-16 pt-20 md:pt-0">
                <span class="inline-block bg-blue-600 text-white text-xs font-bold uppercase tracking-wider py-2 px-4 rounded mb-5">{{ movie.badge }}</span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-2 leading-tight tracking-tight drop-shadow-lg">{{ movie.title }}</h1>
                <p class="text-xl text-gray-200 mb-4">{{ movie.genre }}</p>
                <div class="flex space-x-6 mb-8">
                  <span class="flex items-center text-gray-300"><i class="fas fa-clock mr-2"></i> {{ movie.duration }}</span>
                  <span class="flex items-center text-gray-300"><i class="fas fa-star mr-2 text-yellow-500"></i> {{ movie.rating }}</span>
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                  <button class="px-6 py-3 border-2 border-white text-white font-semibold rounded-full flex items-center justify-center hover:bg-white/20 transition duration-300 hover:-translate-y-1">
                    <i class="fas fa-play-circle mr-2"></i> Ver Trailer
                  </button>
                  <button class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-full flex items-center justify-center hover:bg-blue-500 transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-600/30">
                    <i class="fas fa-ticket-alt mr-2"></i> Comprar Entradas
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Controles -->
      <button 
        class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/30 text-white hover:bg-black/50 transition duration-300 focus:outline-none"
        @click="prevSlide"
      >
        <i class="fas fa-chevron-left"></i>
        <span class="sr-only">Anterior</span>
      </button>
      <button 
        class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black/30 text-white hover:bg-black/50 transition duration-300 focus:outline-none"
        @click="nextSlide"
      >
        <i class="fas fa-chevron-right"></i>
        <span class="sr-only">Siguiente</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount } from 'vue';

export default {
  setup() {
    const currentSlide = ref(0);
    const autoplayInterval = ref(null);
    
    const moviesList = [
      { 
        id: 1, 
        title: "Bridget Jones: Loca por él", 
        genre: "Comedia/Romance", 
        image: "/img/portada1.jpg", 
        badge: "¡YA EN TU CINE!",
        duration: "105 min",
        rating: "8.2/10"
      },
      { 
        id: 2, 
        title: "Mickey 17", 
        genre: "Ciencia Ficción/Aventura", 
        image: "/img/portada2.jpg",
        badge: "ESTRENO EXCLUSIVO",
        duration: "142 min",
        rating: "8.7/10"
      },
      { 
        id: 3, 
        title: "Anora", 
        genre: "Drama", 
        image: "/img/portada3.jpeg",
        badge: "PREMIO DE CANNES",
        duration: "116 min",
        rating: "9.0/10"
      },
      { 
        id: 4, 
        title: "Paddington: Aventura en la Selva", 
        genre: "Familiar/Aventura", 
        image: "/img/portada4.jpg",
        badge: "PRÓXIMAMENTE",
        duration: "98 min",
        rating: "7.9/10"
      }
    ];
    
    const nextSlide = () => {
      currentSlide.value = (currentSlide.value + 1) % moviesList.length;
      resetAutoplayTimer();
    };
    
    const prevSlide = () => {
      currentSlide.value = (currentSlide.value - 1 + moviesList.length) % moviesList.length;
      resetAutoplayTimer();
    };
    
    const setSlide = (index) => {
      currentSlide.value = index;
      resetAutoplayTimer();
    };
    
    const startAutoplay = () => {
      autoplayInterval.value = setInterval(() => {
        nextSlide();
      }, 5000);
    };
    
    const resetAutoplayTimer = () => {
      if (autoplayInterval.value) {
        clearInterval(autoplayInterval.value);
        startAutoplay();
      }
    };
    
    onMounted(() => {
      startAutoplay();
    });
    
    onBeforeUnmount(() => {
      if (autoplayInterval.value) {
        clearInterval(autoplayInterval.value);
      }
    });
    
    return {
      currentSlide,
      moviesList,
      nextSlide,
      prevSlide,
      setSlide
    };
  }
};
</script>

