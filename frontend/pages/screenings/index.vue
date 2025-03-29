<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    <div class="bg-navy-900 text-white min-h-screen py-12 pt-20">
    <div class="container mx-auto px-4">
      <!-- Título de la página -->
      <div class="text-center mb-14">
        <h1 class="text-4xl font-extrabold text-white uppercase tracking-wide animate-fade-in">SESIONES DE CINE</h1>
        <div class="h-1 w-24 bg-gradient-to-r from-blue-600 to-blue-400 mx-auto mt-4 rounded shadow-md"></div>
        <p class="text-gray-300 mt-4">Consulta nuestras sesiones por día y película</p>
      </div>
      
      <!-- Estado de carga -->
      <div v-if="loading" class="text-center py-20">
        <div class="border-4 border-blue-600/30 border-t-blue-600 rounded-full w-10 h-10 animate-spin mx-auto"></div>
        <p class="text-white mt-4">Cargando sesiones...</p>
      </div>
      
      <!-- Sin resultados -->
      <div v-else-if="!screenings.length" class="text-center py-16 bg-gradient-to-br from-blue-900/50 to-blue-800/60 rounded-xl p-8 border border-blue-700/50 max-w-2xl mx-auto shadow-lg">
        <i class="fas fa-calendar-times text-6xl text-blue-400 mb-6"></i>
        <p class="text-2xl font-bold text-white mb-2">No hay sesiones disponibles</p>
        <p class="text-gray-300">Por favor, vuelve a consultar más tarde.</p>
      </div>
      
      <!-- Resultados agrupados por día -->
      <div v-else>
        <div 
          v-for="(screeningsByDay, date) in groupedScreenings" 
          :key="date" 
          class="mb-12"
        >
          <!-- Encabezado del día -->
          <div class="flex items-center mb-6 pb-3">
            <i class="fas fa-calendar-day text-3xl text-blue-400 mr-3"></i>
            <h2 class="text-2xl font-bold text-white">{{ formatGroupDate(date) }}</h2>
            <div class="ml-4 h-0.5 flex-grow bg-gradient-to-r from-blue-600 to-transparent opacity-60 rounded"></div>
          </div>
          
          <!-- Películas por día -->
          <div class="space-y-8">
            <div 
              v-for="(screeningsByMovie, movieId) in getScreeningsByMovie(screeningsByDay)" 
              :key="movieId" 
              class="bg-gradient-to-br from-blue-900/50 to-blue-800/60 rounded-lg overflow-hidden shadow-lg hover:shadow-blue-500/20 transition-all duration-300 hover:-translate-y-1 border border-blue-700/40"
            >
              <div class="md:flex">
                <!-- Poster de la película -->
                <div class="md:w-1/4 h-64 md:h-auto relative overflow-hidden">
                  <!-- Poster con fallback -->
                  <div class="relative w-full h-full bg-gradient-to-br from-blue-900/80 to-blue-800/60">
                    <img 
                      v-if="getMoviePoster(movieId)" 
                      :src="getMoviePoster(movieId)" 
                      :alt="getMovieTitle(movieId)" 
                      class="w-full h-full object-cover"
                      @error="handleImageError"
                      :key="`movie-poster-${movieId}`"
                    />
                    <div 
                      class="absolute inset-0 flex items-center justify-center text-center p-4"
                      :class="{ 'opacity-0': !imageFallbacks[movieId] }"
                    >
                      <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-film text-5xl text-blue-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-white">{{ getMovieTitle(movieId) }}</h3>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Información de la película y sesiones -->
                <div class="md:w-3/4 p-6">
                  <h3 class="text-xl md:text-2xl font-bold mb-4 text-white">{{ getMovieTitle(movieId) }}</h3>
                  
                  <!-- Sesiones disponibles -->
                  <div class="mb-4">
                    <h4 class="text-lg font-medium mb-3 text-blue-300">Horario: {{ formatTime(screeningsByMovie[0].date_time) }}</h4>
                    <div class="flex flex-wrap gap-3">
                      <div class="bg-blue-950 px-4 py-3 rounded-lg border border-blue-800 transition-colors hover:bg-blue-800">
                        <div class="flex items-center space-x-4">
                          <div>
                            <div class="text-sm text-blue-200 mb-2">
                              <span class="font-medium">Salas disponibles:</span> 
                              <span class="text-white">{{ getSalasList(screeningsByMovie) }}</span>
                            </div>
                            <div class="flex items-center text-green-400 font-bold">
                              <i class="fas fa-tag text-green-300 mr-2"></i>
                              Precio: {{ formatPrice(screeningsByMovie[0].price || 8.5) }}
                            </div>
                          </div>
                          
                          <div class="ml-auto">
                            <NuxtLink 
                              :to="`/select-seats?screening_id=${screeningsByMovie[0].id}&movie=${movieId}`" 
                              class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white text-sm font-medium px-4 py-2 rounded-full transition-all duration-300 hover:shadow-md hover:scale-105 flex items-center space-x-1"
                              @click="saveMovieInfoToSession(movieId, screeningsByMovie[0])"
                            >
                              <i class="fas fa-ticket-alt mr-1"></i> Comprar Entradas
                            </NuxtLink>
                          </div>
                        </div>
                      </div>
                    </div>
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

<script>
import { useMoviesStore } from '~/stores/movies';
import { useScreeningsStore } from '~/stores/screenings';
import { useHead } from '#imports';
import LandingPageNavBar from '~/components/LandingPage/NavBar.vue';
import LandingPageFooter from '~/components/LandingPage/Footer.vue';

export default {
  name: 'ScreeningsPage',
  
  components: {
    LandingPageNavBar,
    LandingPageFooter
  },
  
  data() {
    return {
      screenings: [],
      auditoriums: [],
      loading: false,
      error: null,
      genres: [],
      imageFallbacks: {}
    };
  },
  
  computed: {
    moviesStore() {
      return useMoviesStore();
    },
    
    movies() {
      return this.moviesStore.movies;
    },
    
    // Filtrar solo las sesiones activas
    activeScreenings() {
      return this.screenings.filter(screening => screening.active);
    },
    
    groupedScreenings() {
      const groups = {};
      
      // Usar activeScreenings en lugar de screenings
      this.activeScreenings.forEach(screening => {
        const date = new Date(screening.date_time).toISOString().split('T')[0];
        if (!groups[date]) {
          groups[date] = [];
        }
        groups[date].push(screening);
      });
      
      // Ordenar grupos por fecha
      return Object.fromEntries(
        Object.entries(groups).sort(([dateA], [dateB]) => {
          return new Date(dateA) - new Date(dateB);
        })
      );
    }
  },
  
  async created() {
    await this.loadData();
    
    // Debugging: Imprimir las primeras películas para verificar sus propiedades
    if (this.movies && this.movies.length > 0) {
      
      // Verificar campos de imágenes para las primeras 3 películas
      for (let i = 0; i < Math.min(3, this.movies.length); i++) {
        const movie = this.movies[i];
              }
    }
  },
  
  methods: {
    // Manejar errores de carga de imágenes
    handleImageError(event) {
      console.log('Error al cargar imagen:', event.target.src);
      // Obtener el ID de la película desde el atributo alt o key
      const movieId = event.target.alt;
      // Marcar esta película como que tiene fallback
      this.imageFallbacks[movieId] = true;
      // La opacidad del div de fallback cambiará automáticamente por el binding
    },
    // Método para agrupar proyecciones por película
    getScreeningsByMovie(screeningsOfDay) {
      const groups = {};
      
      screeningsOfDay.forEach(screening => {
        const movieId = screening.movie_id;
        if (!groups[movieId]) {
          groups[movieId] = [];
        }
        groups[movieId].push(screening);
      });
      
      // Ordenar por película alfabéticamente
      return Object.fromEntries(
        Object.entries(groups).sort(([movieIdA], [movieIdB]) => {
          const movieA = this.getMovieTitle(parseInt(movieIdA));
          const movieB = this.getMovieTitle(parseInt(movieIdB));
          return movieA.localeCompare(movieB);
        })
      );
    },
    
    async loadData() {
      this.loading = true;
      this.error = null;
      
      try {
        // Cargar películas si no están cargadas
        if (this.movies.length === 0) {
          await this.moviesStore.fetchMovies();
        }
        
        // Cargar sesiones y auditorios desde la API
        const screeningsStore = useScreeningsStore();
        this.screenings = await screeningsStore.fetchScreenings();
        
        // Cargar los auditorios desde la API
        this.auditoriums = await screeningsStore.fetchAuditoriums();
      } catch (error) {
        console.error('Error al cargar datos:', error);
        this.error = 'Error al cargar las sesiones. Por favor, intenta de nuevo.';
      } finally {
        this.loading = false;
      }
    },
    
    getSalasList(screenings) {
      // Extrae los nombres de las salas y los une en una cadena
      const salasNames = screenings.map(screening => 
        this.getAuditoriumName(screening.auditorium_id)
      );
      return salasNames.join(', ');
    },
    
    getMovieTitle(movieId) {
      const movie = this.movies.find(m => m.id === parseInt(movieId));
      return movie ? movie.title : 'Película no encontrada';
    },
    
    getMoviePoster(movieId) {
      // Si ya sabemos que la imagen falló, usa directamente el fallback
      if (this.imageFallbacks[movieId]) {
        return '/img/logo_cine.png';
      }
      
      const movie = this.movies.find(m => m.id === parseInt(movieId));
      if (movie) {
        // Primero intentar con poster
        if (movie.poster) {
          // Si comienza con '/storage', es una ruta en el servidor
          if (movie.poster.startsWith('/storage/')) {
            return movie.poster;
          }
          // Si no tiene el prefijo '/storage', añadirlo
          else if (!movie.poster.startsWith('/')) {
            return `/storage/${movie.poster}`;
          }
          // Si ya es una URL completa o tiene otro prefijo
          else {
            return movie.poster;
          }
        }
        // Si no hay poster pero hay image
        else if (movie.image) {
          if (movie.image.startsWith('/img/')) {
            return movie.image;
          } else if (movie.image.startsWith('/')) {
            return movie.image;
          } else {
            return `/img/${movie.image}`;
          }
        }
      }
      // Si no hay poster o imagen, usa una imagen predeterminada
      return '/img/logo_cine.png';
    },
    
    getMovieGenre(movieId) {
      const movie = this.movies.find(m => m.id === parseInt(movieId));
      if (movie && movie.genre && movie.genre.name) {
        return movie.genre.name;
      } else if (movie && movie.genre_name) {
        return movie.genre_name;
      }
      return 'Sin género';
    },
    
    getAuditoriumName(auditoriumId) {
      const auditorium = this.auditoriums.find(a => a.id === parseInt(auditoriumId));
      return auditorium ? auditorium.name : 'Sala no encontrada';
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    formatGroupDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      const today = new Date();
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);
      
      today.setHours(0, 0, 0, 0);
      tomorrow.setHours(0, 0, 0, 0);
      date.setHours(0, 0, 0, 0);
      
      let prefix = '';
      if (date.getTime() === today.getTime()) {
        prefix = 'Hoy';
      } else if (date.getTime() === tomorrow.getTime()) {
        prefix = 'Mañana';
      }
      
      const formattedDate = date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long'
      });
      
      return prefix ? `${prefix} - ${formattedDate}` : formattedDate;
    },
    
    formatTime(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    formatPrice(price) {
      if (typeof price !== 'number') {
        price = parseFloat(price) || 0;
      }
      return `${price.toFixed(2)} €`;
    },
    
    // Guardar información de la película y sesión en sessionStorage para que esté disponible
    // en la página de selección de asientos
    saveMovieInfoToSession(movieId, screening) {
      const movie = this.movies.find(m => m.id === parseInt(movieId));
      if (!movie) return;
      
      const screeningDate = new Date(screening.date_time);
      const formattedTime = screeningDate.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
      });
      
      const movieInfo = {
        id: parseInt(movieId),
        screening_id: screening.id,
        title: movie.title,
        date: screening.date_time,
        time: formattedTime
      };
      
      sessionStorage.setItem('selectedMovie', JSON.stringify(movieInfo));
    }
  },
  
  // Definimos metadatos de la página utilizando useHead para Nuxt 3
  setup() {
    useHead({
      title: 'Sesiones - Cinema',
      meta: [
        { name: 'description', content: 'Consulta la cartelera y sesiones disponibles del cine' }
      ]
    });
  }
};
</script>

<style scoped>
.transition-transform {
  transition: transform 0.3s ease;
}

/* Mejoras de estilos para la visualización */
.bg-blue-900 {
  background-color: rgba(5, 29, 64, 0.95);
}

.bg-navy-900 {
  background-color: #051D40;
}

/* Animaciones */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out forwards;
}

/* Estilos responsivos mejorados */
@media (max-width: 768px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>