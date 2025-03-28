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
            
            <div class="py-2 px-3 bg-light rounded mt-3 mb-2">
              <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-circle text-info me-2"></i>
                <small>Selecciona una función de abajo para comprar entradas</small>
              </div>
            </div>
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
                  <strong class="screening-day">{{ formatScreeningDate(screening.date_time) }}</strong>
                  <div class="screening-details">
                    <span class="badge bg-secondary me-2">{{ screening.is_special ? 'Sesión Especial' : 'Normal' }}</span>
                    <span class="ms-1">{{ screening.auditorium ? screening.auditorium.name : `Sala ${screening.auditorium_id}` }}</span>
                    <span class="ms-3 price">{{ screening.price }}€</span>
                  </div>
                </div>
                
                <NuxtLink :to="`/select-seats?screening_id=${screening.id}`" class="btn btn-sm btn-outline-primary">
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
        // Usamos fetch para obtener las proyecciones de esta película
        const response = await fetch(`http://localhost:8000/api/movie/${this.movieId}/screenings`, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error al obtener proyecciones: ${response.status}`);
        }
        
        const data = await response.json();
        this.screenings = data;
      } catch (error) {
        console.error('Error al cargar las sesiones:', error);
      } finally {
        this.loadingScreenings = false;
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
      
      const date = new Date(dateObj);
      const weekdays = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
      const day = weekdays[date.getDay()];
      
      // Formateamos la fecha con el día de la semana destacado
      return `${day} - ${date.toLocaleString('es-ES', {
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit'
      })}`;
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
  margin-bottom: 0.5rem;
  border-radius: 0.5rem;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}

.screening-day {
  display: block;
  font-size: 1.1rem;
  color: #343a40;
}

.screening-details {
  margin-top: 0.25rem;
  font-size: 0.9rem;
  color: #6c757d;
}

.price {
  font-weight: bold;
  color: #28a745;
}
</style>