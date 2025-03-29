<template>
  <div class="bg-blue-900 text-white py-20">
    <!-- Contenedor principal -->
    <section class="max-w-6xl mx-auto py-20 px-4">
      <div class="text-center mb-14">
        <h2 class="text-3xl font-extrabold uppercase tracking-wide">TOP PELÍCULAS</h2>
        <div class="h-1 w-24 bg-blue-600 mx-auto mt-4 mb-12 rounded"></div>
      </div>
      
      <div class="relative w-full overflow-hidden">
        <div class="flex transition-transform duration-500 ease-in-out" :style="{ transform: showSecondSlide ? 'translateX(-100%)' : 'translateX(0%)' }">
          <!-- Slide 1 -->
          <div class="min-w-full flex-shrink-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 justify-center">
              <div 
                v-for="movie in moviesSlide1" 
                :key="movie.id"
                class="cursor-pointer flex justify-center"
              >
                <!-- Tarjeta con aspect ratio vertical y tamaño reducido -->
                <div class="relative aspect-[2/3] w-full max-w-[220px] rounded-lg overflow-hidden">
                  <img 
                    :src="movie.image" 
                    :alt="movie.title" 
                    class="w-full h-full object-cover transition-transform duration-10000 ease-in-out"
                  >
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-900/60 to-transparent"></div>
                  <div class="absolute bottom-4 left-4">
                    <span 
                      v-if="movie.badge" 
                      class="inline-block bg-blue-600 text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded mb-2"
                    >
                      {{ movie.badge }}
                    </span>
                    <h3 class="text-xl font-extrabold">{{ movie.title }}</h3>
                    <p class="text-sm text-gray-200">{{ movie.genre }}</p>
                    <div class="flex items-center text-gray-300 text-sm">
                      <span class="mr-2"><i class="fas fa-clock"></i> {{ movie.duration }}</span>
                      <span class="flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-1"></i> {{ movie.rating }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Slide 2 -->
          <div class="min-w-full flex-shrink-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 justify-center">
              <div 
                v-for="movie in moviesSlide2" 
                :key="movie.id"
                class="cursor-pointer flex justify-center"
              >
                <div class="relative aspect-[2/3] w-full max-w-[220px] rounded-lg overflow-hidden">
                  <img 
                    :src="movie.image" 
                    :alt="movie.title" 
                    class="w-full h-full object-cover transition-transform duration-10000 ease-in-out"
                  >
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-900/60 to-transparent"></div>
                  <div class="absolute bottom-4 left-4">
                    <span 
                      v-if="movie.badge" 
                      class="inline-block bg-blue-600 text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded mb-2"
                    >
                      {{ movie.badge }}
                    </span>
                    <h3 class="text-xl font-extrabold">{{ movie.title }}</h3>
                    <p class="text-sm text-gray-200">{{ movie.genre }}</p>
                    <div class="flex items-center text-gray-300 text-sm">
                      <span class="mr-2"><i class="fas fa-clock"></i> {{ movie.duration }}</span>
                      <span class="flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-1"></i> {{ movie.rating }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      
      <!-- Controles de navegación -->
      <div class="flex justify-center space-x-4 mt-16">
        <button 
          class="w-12 h-12 rounded-full bg-white bg-opacity-10 flex items-center justify-center text-white transition-all duration-300 hover:bg-blue-600 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" 
          @click="showFirstSlide" 
          :disabled="!showSecondSlide"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <button 
          class="w-12 h-12 rounded-full bg-white bg-opacity-10 flex items-center justify-center text-white transition-all duration-300 hover:bg-blue-600 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" 
          @click="showNextSlide" 
          :disabled="showSecondSlide"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showSecondSlide: false,
      allMovies: [
        { 
          id: 1, 
          title: "EL CABALLERO OSCURO", 
          genre: "Acción", 
          image: "/img/batman_peli.jpg",
          badge: "¡EN CINE!",
          duration: "152 min",
          rating: "9.0/10",
          comingSoon: false
        },
        { 
          id: 2, 
          title: "INTERSTELLAR", 
          genre: "Ciencia Ficción", 
          image: "/img/interestelar_peli.jpg",
          badge: "ESTRENO",
          duration: "169 min",
          rating: "8.7/10",
          comingSoon: false
        },
        { 
          id: 3, 
          title: "GLADIATOR", 
          genre: "Acción/Historia", 
          image: "/img/gladiator_peli.jpg",
          badge: "CLÁSICO",
          duration: "155 min",
          rating: "8.5/10",
          comingSoon: false
        },
        { 
          id: 4, 
          title: "BEAUTY AND THE BEAST", 
          genre: "Fantasía/Romance", 
          image: "/img/bellaybestia_peli.jpg",
          badge: "PRÓXIMAMENTE",
          duration: "129 min",
          rating: "7.9/10",
          comingSoon: true
        },
      ]
    };
  },
  computed: {
    moviesSlide1() {
      return this.allMovies.slice(0, 4);
    },
    moviesSlide2() {
      return this.allMovies.slice(4, 8);
    }
  },
  methods: {
    showNextSlide() {
      this.showSecondSlide = true;
    },
    showFirstSlide() {
      this.showSecondSlide = false;
    }
  }
}
</script>
