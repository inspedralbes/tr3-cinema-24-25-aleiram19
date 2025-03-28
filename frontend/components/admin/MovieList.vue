<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Administración de Películas</h1>
      <button class="btn btn-primary" @click="showForm = true" v-if="!showForm">
        <i class="bi bi-plus-circle me-2"></i> Nueva Película
      </button>
    </div>
    
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    
    <div v-if="showForm">
      <MovieForm 
        :movie="editingMovie" 
        @saved="onMovieSaved" 
        @cancel="cancelForm"
      />
    </div>
    
    <div v-else>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Poster</th>
              <th>Título</th>
              <th>Género</th>
              <th>Duración</th>
              <th>Estreno</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading && !movies.length">
              <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
              </td>
            </tr>
            
            <tr v-else-if="!movies.length">
              <td colspan="6" class="text-center py-4">
                No hay películas registradas
              </td>
            </tr>
            
            <tr v-for="movie in movies" :key="movie.id">
              <td>
                <img 
                  v-if="movie.poster" 
                  :src="`/storage/${movie.poster}`" 
                  alt="Poster" 
                  class="img-thumbnail" 
                  style="height: 80px;"
                />
                <div v-else class="no-poster">Sin imagen</div>
              </td>
              <td>{{ movie.title }}</td>
              <td>{{ getGenreName(movie.movie_genre_id || movie.genre_id) }}</td>
              <td>{{ movie.duration }} min</td>
              <td>{{ formatDate(movie.release_date) }}</td>
              <td>
                <div class="btn-group">
                  <button 
                    class="btn btn-sm btn-outline-primary"
                    @click="editMovie(movie)"
                    title="Editar"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger"
                    @click="confirmDelete(movie)"
                    title="Eliminar"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" ref="deleteModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar la película "{{ deletingMovie?.title }}"?</p>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button 
              type="button" 
              class="btn btn-danger" 
              @click="deleteMovie" 
              :disabled="deleteLoading"
            >
              <span v-if="deleteLoading" class="spinner-border spinner-border-sm me-2"></span>
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import { useMoviesStore } from '~/stores/movies';
import { useGenresStore } from '~/stores/genres';
import apiService from '~/services/api';
import { Modal } from 'bootstrap';
import MovieForm from './MovieForm.vue';

export default {
  name: 'MovieList',
  
  components: {
    MovieForm
  },
  
  data() {
    return {
      showForm: false,
      editingMovie: null,
      deletingMovie: null,
      deleteModal: null,
      deleteLoading: false,
      error: null
    };
  },
  
  computed: {
    movies() {
      return useMoviesStore().movies;
    },
    
    loading() {
      return useMoviesStore().loading;
    }
  },
  
  async mounted() {
    // Inicializar el modal
    this.deleteModal = new Modal(this.$refs.deleteModal);
    
    // Cargar las películas
    await this.loadData();
  },
  
  methods: {
    async loadData() {
      try {
        const moviesStore = useMoviesStore();
        const genresStore = useGenresStore();
        
        // Cargar géneros si no están cargados
        if (genresStore.genres.length === 0) {
          await genresStore.fetchGenres();
        }
        
        // Cargar películas
        await moviesStore.fetchMovies();
      } catch (error) {
        console.error('Error al cargar los datos:', error);
        this.error = 'Error al cargar los datos. Por favor, intenta de nuevo.';
      }
    },
    
    getGenreName(genreId) {
      return useGenresStore().getGenreName(genreId);
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    
    editMovie(movie) {
      this.editingMovie = movie;
      this.showForm = true;
    },
    
    confirmDelete(movie) {
      this.deletingMovie = movie;
      this.deleteModal.show();
    },
    
    async deleteMovie() {
      if (!this.deletingMovie) return;
      
      this.deleteLoading = true;
      
      try {
        await apiService.movies.delete(this.deletingMovie.id);
        
        // Actualizar la lista de películas
        await useMoviesStore().fetchMovies();
        
        this.deleteModal.hide();
        this.deletingMovie = null;
      } catch (error) {
        console.error('Error al eliminar la película:', error);
        this.error = error.message || 'Error al eliminar la película';
      } finally {
        this.deleteLoading = false;
      }
    },
    
    async onMovieSaved() {
      // Recargar las películas
      await useMoviesStore().fetchMovies();
      
      // Ocultar formulario
      this.cancelForm();
    },
    
    cancelForm() {
      this.showForm = false;
      this.editingMovie = null;
    }
  }
};
</script>

<style scoped>
.no-poster {
  width: 60px;
  height: 80px;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: #666;
  border-radius: 4px;
}
</style>