<template>
  <div class="movie-detail">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-2">Cargando información de la película...</p>
    </div>
    
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    
    <div v-else-if="!movie" class="text-center py-5">
      <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
      <p class="mt-2">La película no se ha encontrado</p>
    </div>
    
    <div v-else class="movie-content">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="movie-poster-container">
            <img 
              v-if="movie.poster" 
              :src="`/storage/${movie.poster}`" 
              :alt="movie.title" 
              class="movie-poster img-fluid rounded"
            />
            <div 
              v-else 
              class="no-poster rounded d-flex align-items-center justify-content-center"
            >
              <i class="bi bi-film fs-1 text-muted"></i>
            </div>
            
            <NuxtLink :to="`/buy-tickets/${movie.id}`" class="btn btn-primary w-100 mt-3">
              <i class="bi bi-ticket-perforated me-2"></i> Comprar Entradas
            </NuxtLink>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="d-flex align-items-center mb-2">
            <h1 class="movie-title mb-0">{{ movie.title }}</h1>
            <span 
              v-if="movie.genre" 
              class="badge bg-primary ms-3"
            >
              {{ movie.genre.name }}
            </span>
          </div>
          
          <div class="movie-meta mb-4">
            <span class="me-4">
              <i class="bi bi-clock me-1"></i> {{ movie.duration }} minutos
            </span>
            <span>
              <i class="bi bi-calendar-event me-1"></i> {{ formatDate(movie.release_date) }}
            </span>
          </div>
          
          <div class="movie-description mb-4">
            <h5>Sinopsis</h5>
            <p>{{ movie.description }}</p>
          </div>
          
          <div v-if="movie.trailer_url" class="movie-trailer mb-4">
            <h5>Trailer</h5>
            <div class="ratio ratio-16x9">
              <iframe 
                :src="getEmbedUrl(movie.trailer_url)" 
                title="Trailer"
                allowfullscreen
              ></iframe>
            </div>
          </div>
          
          <div class="movie-screenings mb-4">
            <h5>Próximas Funciones</h5>
            
            <div v-if="loadingScreenings" class="text-center py-3">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <span class="ms-2">Cargando funciones...</span>
            </div>
            
            <div v-else-if="!screenings.length" class="alert alert-info">
              No hay funciones programadas para esta película.
            </div>
            
            <div v-else class="list-group">
              <div 
                v-for="(screening, index) in screenings" 
                :key="index"
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div>
                  <strong>{{ formatScreeningDate(screening.date_time) }}</strong>
                  <span class="ms-3">{{ screening.auditorium.name }}</span>
                </div>
                
                <NuxtLink :to="`/buy-tickets/${movie.id}/${screening.id}`" class="btn btn-sm btn-outline-primary">
                  Seleccionar
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row mt-5">
        <div class="col-12">
          <h3>Películas Relacionadas</h3>
          <div v-if="loadingRelated" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
            <span class="ms-2">Cargando películas relacionadas...</span>
          </div>
          
          <div v-else-if="!relatedMovies.length" class="alert alert-light">
            No hay películas relacionadas disponibles.
          </div>
          
          <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <div v-for="relatedMovie in relatedMovies" :key="relatedMovie.id" class="col">
              <MovieCard :movie="relatedMovie" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMoviesStore } from '~/stores/movies';
import { useGenresStore } from '~/stores/genres';
import MovieCard from './MovieCard.vue';
import apiService from '~/services/api';

export default {
  name: 'MovieDetail',
  
  components: {
    MovieCard
  },
  
  props: {
    movieId: {
      type: [Number, String],
      required: true
    }
  },
  
  data() {
    return {
      screenings: [],
      relatedMovies: [],
      loadingScreenings: false,
      loadingRelated: false
    };
  },
  
  computed: {
    moviesStore() {
      return useMoviesStore();
    },
    
    genresStore() {
      return useGenresStore();
    },
    
    movie() {
      return this.moviesStore.currentMovie;
    },
    
    loading() {
      return this.moviesStore.loading;
    },
    
    error() {
      return this.moviesStore.error;
    }
  },
  
  async created() {
    await this.loadMovie();
    if (this.movie) {
      this.loadScreenings();
      this.loadRelatedMovies();
    }
  },
  
  methods: {
    async loadMovie() {
      try {
        await this.moviesStore.fetchMovieById(this.movieId);
      } catch (error) {
        console.error('Error al cargar la película:', error);
      }
    },
    
    async loadScreenings() {
      this.loadingScreenings = true;
      try {
        // Aquí deberíamos usar el servicio API para obtener las sesiones
        // Por ahora, simularemos algunos datos
        // this.screenings = await apiService.screenings.getByMovie(this.movieId);
        
        // Datos de ejemplo
        setTimeout(() => {
          this.screenings = [
            {
              id: 1,
              date_time: new Date(Date.now() + 24 * 60 * 60 * 1000), // Mañana
              auditorium: { id: 1, name: 'Sala 1' }
            },
            {
              id: 2,
              date_time: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // Pasado mañana
              auditorium: { id: 2, name: 'Sala 2' }
            },
            {
              id: 3,
              date_time: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000), // En 3 días
              auditorium: { id: 1, name: 'Sala 1' }
            }
          ];
          this.loadingScreenings = false;
        }, 1000);
      } catch (error) {
        console.error('Error al cargar las sesiones:', error);
      } finally {
        // this.loadingScreenings = false;
      }
    },
    
    async loadRelatedMovies() {
      this.loadingRelated = true;
      try {
        if (this.movie && (this.movie.movie_genre_id || this.movie.genre_id)) {
          const genreId = this.movie.movie_genre_id || this.movie.genre_id;
          
          // Cargar películas del mismo género
          const relatedByGenre = await this.genresStore.fetchMoviesByGenre(genreId);
          
          // Filtrar para no incluir la película actual
          this.relatedMovies = relatedByGenre
            .filter(movie => movie.id !== parseInt(this.movieId))
            .slice(0, 4); // Limitar a 4 películas relacionadas
        }
      } catch (error) {
        console.error('Error al cargar películas relacionadas:', error);
      } finally {
        this.loadingRelated = false;
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    formatScreeningDate(dateObj) {
      if (!dateObj) return '';
      
      return new Date(dateObj).toLocaleString('es-ES', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    getEmbedUrl(url) {
      // Convertir URL de YouTube en URL de embed
      if (url && url.includes('youtube.com/watch')) {
        const videoId = new URL(url).searchParams.get('v');
        return `https://www.youtube.com/embed/${videoId}`;
      } else if (url && url.includes('youtu.be')) {
        const videoId = url.split('/').pop();
        return `https://www.youtube.com/embed/${videoId}`;
      }
      
      return url;
    }
  }
};
</script>

<style scoped>
.movie-detail {
  padding: 2rem 0;
}

.movie-poster {
  width: 100%;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.no-poster {
  width: 100%;
  aspect-ratio: 2/3;
  background-color: #f0f0f0;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.movie-title {
  font-weight: bold;
}

.movie-meta {
  color: #666;
  margin-top: 0.5rem;
}

.movie-description {
  line-height: 1.6;
}

.list-group-item {
  transition: background-color 0.2s ease;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}
</style>