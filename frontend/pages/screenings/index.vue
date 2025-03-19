<template>
  <div class="screenings-page">
    <div class="container py-5">
      <div class="row mb-4">
        <div class="col-12">
          <h1>Cartelera</h1>
          <p class="lead">Consulta nuestras próximas funciones y compra tus entradas</p>
        </div>
      </div>
      
      <div class="screenings-filters mb-4">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-calendar"></i></span>
              <input 
                type="date" 
                class="form-control" 
                v-model="selectedDate"
                min="2023-01-01"
              />
            </div>
          </div>
          
          <div class="col-md-3">
            <select class="form-select" v-model="selectedMovie">
              <option value="">Todas las películas</option>
              <option 
                v-for="movie in movies" 
                :key="movie.id" 
                :value="movie.id"
              >
                {{ movie.title }}
              </option>
            </select>
          </div>
          
          <div class="col-md-3">
            <select class="form-select" v-model="selectedAuditorium">
              <option value="">Todas las salas</option>
              <option 
                v-for="auditorium in auditoriums" 
                :key="auditorium.id" 
                :value="auditorium.id"
              >
                {{ auditorium.name }}
              </option>
            </select>
          </div>
          
          <div class="col-md-3">
            <button 
              class="btn btn-primary w-100" 
              @click="applyFilters"
            >
              <i class="bi bi-search me-2"></i> Buscar Funciones
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando funciones...</p>
      </div>
      
      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>
      
      <div v-else-if="!filteredScreenings.length" class="text-center py-5">
        <i class="bi bi-calendar-x fs-1 text-muted"></i>
        <p class="mt-2">No hay funciones disponibles con los filtros seleccionados.</p>
      </div>
      
      <div v-else>
        <!-- Agrupación por día -->
        <div 
          v-for="(group, date) in groupedScreenings" 
          :key="date" 
          class="screening-day mb-5"
        >
          <h3 class="date-header">{{ formatGroupDate(date) }}</h3>
          
          <div class="row row-cols-1 row-cols-lg-2 g-4">
            <div 
              v-for="screening in group" 
              :key="screening.id" 
              class="col"
            >
              <div class="card screening-card h-100">
                <div class="row g-0">
                  <div class="col-md-4">
                    <img 
                      v-if="getMoviePoster(screening.movie_id)" 
                      :src="`/storage/${getMoviePoster(screening.movie_id)}`" 
                      :alt="getMovieTitle(screening.movie_id)" 
                      class="img-fluid rounded-start screening-poster"
                    />
                    <div v-else class="no-poster d-flex align-items-center justify-content-center h-100 rounded-start">
                      <i class="bi bi-film fs-1 text-muted"></i>
                    </div>
                  </div>
                  
                  <div class="col-md-8">
                    <div class="card-body d-flex flex-column h-100">
                      <h5 class="card-title">{{ getMovieTitle(screening.movie_id) }}</h5>
                      
                      <div class="screening-info mb-3">
                        <div class="mb-1">
                          <i class="bi bi-clock me-2"></i>
                          <strong>Hora:</strong> {{ formatTime(screening.date_time) }}
                        </div>
                        <div class="mb-1">
                          <i class="bi bi-building me-2"></i>
                          <strong>Sala:</strong> {{ getAuditoriumName(screening.auditorium_id) }}
                        </div>
                        <div>
                          <i class="bi bi-tag me-2"></i>
                          <strong>Género:</strong> {{ getMovieGenre(screening.movie_id) }}
                        </div>
                      </div>
                      
                      <div class="screening-footer mt-auto d-flex justify-content-between align-items-center">
                        <span class="price">
                          <strong>Precio:</strong> {{ formatPrice(screening.price || 8.5) }}
                        </span>
                        <NuxtLink 
                          :to="`/buy-tickets/${screening.movie_id}/${screening.id}`" 
                          class="btn btn-primary"
                        >
                          <i class="bi bi-ticket-perforated me-1"></i> Comprar
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
</template>

<script>
import { useMoviesStore } from '~/stores/movies';
import { useScreeningsStore } from '~/stores/screenings';

export default {
  name: 'ScreeningsPage',
  
  data() {
    return {
      screenings: [],
      auditoriums: [],
      selectedDate: this.formatDateForInput(new Date()),
      selectedMovie: '',
      selectedAuditorium: '',
      loading: false,
      error: null
    };
  },
  
  computed: {
    moviesStore() {
      return useMoviesStore();
    },
    
    movies() {
      return this.moviesStore.movies;
    },
    
    filteredScreenings() {
      let result = [...this.screenings];
      
      // Filtrar por película
      if (this.selectedMovie) {
        result = result.filter(s => s.movie_id == this.selectedMovie);
      }
      
      // Filtrar por sala
      if (this.selectedAuditorium) {
        result = result.filter(s => s.auditorium_id == this.selectedAuditorium);
      }
      
      // Filtrar por fecha
      if (this.selectedDate) {
        const selectedDateStart = new Date(this.selectedDate);
        selectedDateStart.setHours(0, 0, 0, 0);
        
        const selectedDateEnd = new Date(this.selectedDate);
        selectedDateEnd.setHours(23, 59, 59, 999);
        
        result = result.filter(s => {
          const screeningDate = new Date(s.date_time);
          return screeningDate >= selectedDateStart && screeningDate <= selectedDateEnd;
        });
      }
      
      return result;
    },
    
    groupedScreenings() {
      const groups = {};
      
      this.filteredScreenings.forEach(screening => {
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
  },
  
  methods: {
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
        this.error = 'Error al cargar las funciones. Por favor, intenta de nuevo.';
      } finally {
        this.loading = false;
      }
    },
    
    loadMockData() {
      // Auditorios simulados
      this.auditoriums = [
        { id: 1, name: 'Sala 1' },
        { id: 2, name: 'Sala 2' },
        { id: 3, name: 'Sala 3 - 3D' },
        { id: 4, name: 'Sala VIP' }
      ];
      
      // Funciones simuladas
      const today = new Date();
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);
      const dayAfterTomorrow = new Date(today);
      dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);
      
      this.screenings = [];
      
      // Asegurarnos de que tenemos películas
      if (this.movies.length) {
        // Funciones para hoy
        for (let i = 0; i < 5; i++) {
          const hour = 14 + (i * 2); // 14:00, 16:00, 18:00, 20:00, 22:00
          const movie = this.movies[i % this.movies.length];
          const auditorium = this.auditoriums[i % this.auditoriums.length];
          
          const screeningDate = new Date(today);
          screeningDate.setHours(hour, 0, 0);
          
          this.screenings.push({
            id: i + 1,
            movie_id: movie.id,
            auditorium_id: auditorium.id,
            date_time: screeningDate,
            price: 8.5 + (i % 3) // Variar precios: 8.5, 9.5, 10.5
          });
        }
        
        // Funciones para mañana
        for (let i = 0; i < 5; i++) {
          const hour = 14 + (i * 2);
          const movie = this.movies[(i + 2) % this.movies.length];
          const auditorium = this.auditoriums[(i + 1) % this.auditoriums.length];
          
          const screeningDate = new Date(tomorrow);
          screeningDate.setHours(hour, 0, 0);
          
          this.screenings.push({
            id: i + 6,
            movie_id: movie.id,
            auditorium_id: auditorium.id,
            date_time: screeningDate,
            price: 8.5 + (i % 3)
          });
        }
        
        // Funciones para pasado mañana
        for (let i = 0; i < 5; i++) {
          const hour = 14 + (i * 2);
          const movie = this.movies[(i + 4) % this.movies.length];
          const auditorium = this.auditoriums[(i + 2) % this.auditoriums.length];
          
          const screeningDate = new Date(dayAfterTomorrow);
          screeningDate.setHours(hour, 0, 0);
          
          this.screenings.push({
            id: i + 11,
            movie_id: movie.id,
            auditorium_id: auditorium.id,
            date_time: screeningDate,
            price: 8.5 + (i % 3)
          });
        }
      }
    },
    
    applyFilters() {
      // Ya aplicamos los filtros automáticamente a través de computed properties
      // Esta función podría usarse si quisiéramos hacer una petición al servidor
    },
    
    getMovieTitle(movieId) {
      const movie = this.movies.find(m => m.id === movieId);
      return movie ? movie.title : 'Película no encontrada';
    },
    
    getMoviePoster(movieId) {
      const movie = this.movies.find(m => m.id === movieId);
      return movie ? movie.poster : null;
    },
    
    getMovieGenre(movieId) {
      const movie = this.movies.find(m => m.id === movieId);
      if (movie && movie.genre) {
        return movie.genre.name;
      } else if (movie && movie.movie_genre_id) {
        const genre = this.genres.find(g => g.id === movie.movie_genre_id);
        return genre ? genre.name : 'Sin género';
      }
      return 'Sin género';
    },
    
    getAuditoriumName(auditoriumId) {
      const auditorium = this.auditoriums.find(a => a.id === auditoriumId);
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
      return `${price.toFixed(2)} €`;
    },
    
    formatDateForInput(date) {
      return date.toISOString().split('T')[0];
    }
  },
  
  head() {
    return {
      title: 'Cartelera - Cinema'
    };
  }
};
</script>

<style scoped>
.screenings-page {
  min-height: 80vh;
}

.screening-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
}

.screening-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.screening-poster {
  height: 100%;
  object-fit: cover;
}

.no-poster {
  background-color: #f0f0f0;
  min-height: 200px;
}

.date-header {
  position: relative;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  text-transform: capitalize;
}

.date-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background-color: var(--bs-primary);
}

.screening-info {
  font-size: 0.9rem;
  color: #666;
}

.price {
  font-size: 1.1rem;
}
</style>