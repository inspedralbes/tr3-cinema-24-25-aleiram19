<template>
  <div class="movie-grid">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-2">Cargando películas...</p>
    </div>
    
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    
    <div v-else-if="!filteredMovies.length" class="text-center py-5">
      <i class="bi bi-film fs-1 text-muted"></i>
      <p class="mt-2">No se encontraron películas{{ selectedGenre ? ' en este género' : '' }}</p>
    </div>
    
    <div v-else>
      <div class="mb-4 d-md-flex justify-content-between align-items-center">
        <h3 class="mb-3 mb-md-0">
          {{ title || 'Películas' }}
          <span v-if="selectedGenre" class="text-primary">
            {{ ` - ${getGenreName(selectedGenre)}` }}
          </span>
        </h3>
        
        <div class="d-flex gap-3">
          <div class="dropdown">
            <button 
              class="btn btn-outline-primary dropdown-toggle" 
              type="button" 
              id="genreDropdown" 
              data-bs-toggle="dropdown" 
              aria-expanded="false"
            >
              <i class="bi bi-tags me-1"></i>
              {{ selectedGenre ? getGenreName(selectedGenre) : 'Todos los géneros' }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="genreDropdown">
              <li>
                <a class="dropdown-item" href="#" @click.prevent="changeGenre(null)">
                  Todos los géneros
                </a>
              </li>
              <li v-for="genre in genres" :key="genre.id">
                <a 
                  class="dropdown-item" 
                  href="#" 
                  @click.prevent="changeGenre(genre.id)"
                  :class="{ 'active': selectedGenre === genre.id }"
                >
                  {{ genre.name }}
                </a>
              </li>
            </ul>
          </div>
          
          <div class="input-group" style="max-width: 250px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input 
              type="text" 
              class="form-control" 
              placeholder="Buscar películas..." 
              v-model="searchQuery"
            />
          </div>
        </div>
      </div>
      
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <div v-for="movie in filteredMovies" :key="movie.id" class="col">
          <MovieCard :movie="movie" :showGenre="!selectedGenre" />
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
  name: 'MovieGrid',
  
  components: {
    MovieCard
  },
  
  props: {
    title: {
      type: String,
      default: ''
    },
    currentOnly: {
      type: Boolean,
      default: false
    },
    initialGenre: {
      type: Number,
      default: null
    }
  },
  
  data() {
    return {
      selectedGenre: this.initialGenre,
      searchQuery: '',
    };
  },
  
  computed: {
    moviesStore() {
      return useMoviesStore();
    },
    
    genresStore() {
      return useGenresStore();
    },
    
    movies() {
      return this.moviesStore.movies;
    },
    
    genres() {
      return this.genresStore.genres;
    },
    
    loading() {
      return this.moviesStore.loading || this.genresStore.loading;
    },
    
    error() {
      return this.moviesStore.error || this.genresStore.error;
    },
    
    filteredMovies() {
      let result = this.movies;
      
      // Filtrar por género si está seleccionado
      if (this.selectedGenre) {
        result = result.filter(movie => 
          (movie.movie_genre_id === this.selectedGenre) || 
          (movie.genre && movie.genre.id === this.selectedGenre)
        );
      }
      
      // Filtrar por búsqueda
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase().trim();
        result = result.filter(movie => 
          movie.title.toLowerCase().includes(query) || 
          (movie.description && movie.description.toLowerCase().includes(query))
        );
      }
      
      return result;
    }
  },
  
  async created() {
    await this.loadData();
  },
  
  methods: {
    async loadData() {
      try {
        // Cargar géneros si no están cargados
        if (this.genres.length === 0) {
          await this.genresStore.fetchGenres();
        }
        
        // Cargar películas según la prop currentOnly
        if (this.currentOnly) {
          await this.moviesStore.fetchCurrentMovies();
        } else {
          await this.moviesStore.fetchMovies();
        }
      } catch (error) {
        console.error('Error al cargar datos:', error);
      }
    },
    
    getGenreName(genreId) {
      return this.genresStore.getGenreName(genreId);
    },
    
    changeGenre(genreId) {
      this.selectedGenre = genreId;
    }
  }
};
</script>

<style scoped>
.movie-grid {
  padding: 1rem 0;
}

.dropdown-item.active {
  background-color: var(--bs-primary);
  color: white;
}
</style>