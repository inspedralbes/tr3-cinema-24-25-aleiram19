<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Administración de Géneros</h1>
      <button class="btn btn-primary" @click="showForm = true" v-if="!showForm">
        <i class="bi bi-plus-circle me-2"></i> Nuevo Género
      </button>
    </div>
    
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    
    <div v-if="showForm">
      <GenreForm 
        :genre="editingGenre" 
        @saved="onGenreSaved" 
        @cancel="cancelForm"
      />
    </div>
    
    <div v-else>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Películas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading && !genres.length">
              <td colspan="4" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
              </td>
            </tr>
            
            <tr v-else-if="!genres.length">
              <td colspan="4" class="text-center py-4">
                No hay géneros registrados
              </td>
            </tr>
            
            <tr v-for="genre in genres" :key="genre.id">
              <td>{{ genre.id }}</td>
              <td>{{ genre.name }}</td>
              <td>
                <button 
                  class="btn btn-sm btn-outline-info" 
                  @click="viewMovies(genre)"
                  title="Ver películas"
                >
                  <i class="bi bi-film me-1"></i> Ver películas
                </button>
              </td>
              <td>
                <div class="btn-group">
                  <button 
                    class="btn btn-sm btn-outline-primary"
                    @click="editGenre(genre)"
                    title="Editar"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger"
                    @click="confirmDelete(genre)"
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
    
    <!-- Modal de películas por género -->
    <div class="modal fade" ref="moviesModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Películas de género: {{ selectedGenre?.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="moviesLoading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
            
            <div v-else-if="!genreMovies.length" class="text-center py-4">
              <p>No hay películas asociadas a este género</p>
            </div>
            
            <div v-else class="list-group">
              <div class="list-group-item d-flex align-items-center" v-for="movie in genreMovies" :key="movie.id">
                <img 
                  v-if="movie.poster" 
                  :src="`/storage/${movie.poster}`" 
                  alt="Poster" 
                  class="me-3" 
                  style="height: 60px; border-radius: 4px;"
                />
                <div v-else class="me-3 no-poster">Sin imagen</div>
                <div>
                  <h6 class="mb-1">{{ movie.title }}</h6>
                  <p class="mb-0 small text-muted">{{ movie.duration }} min | {{ formatDate(movie.release_date) }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
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
            <p>¿Estás seguro de que deseas eliminar el género "{{ deletingGenre?.name }}"?</p>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <p>Nota: No podrás eliminar géneros que tengan películas asociadas.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button 
              type="button" 
              class="btn btn-danger" 
              @click="deleteGenre" 
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
import { useGenresStore } from '~/stores/genres';
import apiService from '~/services/api';
import { Modal } from 'bootstrap';
import GenreForm from './GenreForm.vue';

export default {
  name: 'GenreList',
  
  components: {
    GenreForm
  },
  
  data() {
    return {
      showForm: false,
      editingGenre: null,
      deletingGenre: null,
      selectedGenre: null,
      genreMovies: [],
      moviesLoading: false,
      deleteModal: null,
      moviesModal: null,
      deleteLoading: false,
      error: null
    };
  },
  
  computed: {
    genres() {
      return useGenresStore().genres;
    },
    
    loading() {
      return useGenresStore().loading;
    }
  },
  
  async mounted() {
    // Inicializar los modales
    this.deleteModal = new Modal(this.$refs.deleteModal);
    this.moviesModal = new Modal(this.$refs.moviesModal);
    
    // Cargar los géneros
    await this.loadData();
  },
  
  methods: {
    async loadData() {
      try {
        const genresStore = useGenresStore();
        
        // Cargar géneros
        await genresStore.fetchGenres();
      } catch (error) {
        console.error('Error al cargar los datos:', error);
        this.error = 'Error al cargar los datos. Por favor, intenta de nuevo.';
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    
    editGenre(genre) {
      this.editingGenre = genre;
      this.showForm = true;
    },
    
    async viewMovies(genre) {
      this.selectedGenre = genre;
      this.moviesLoading = true;
      this.genreMovies = [];
      
      this.moviesModal.show();
      
      try {
        this.genreMovies = await useGenresStore().fetchMoviesByGenre(genre.id);
      } catch (error) {
        console.error('Error al cargar las películas:', error);
        this.error = 'Error al cargar las películas del género';
      } finally {
        this.moviesLoading = false;
      }
    },
    
    confirmDelete(genre) {
      this.deletingGenre = genre;
      this.deleteModal.show();
    },
    
    async deleteGenre() {
      if (!this.deletingGenre) return;
      
      this.deleteLoading = true;
      
      try {
        await apiService.genres.delete(this.deletingGenre.id);
        
        // Actualizar la lista de géneros
        await useGenresStore().fetchGenres();
        
        this.deleteModal.hide();
        this.deletingGenre = null;
      } catch (error) {
        console.error('Error al eliminar el género:', error);
        this.error = error.message || 'Error al eliminar el género. Puede que tenga películas asociadas.';
      } finally {
        this.deleteLoading = false;
      }
    },
    
    async onGenreSaved() {
      // Recargar los géneros
      await useGenresStore().fetchGenres();
      
      // Ocultar formulario
      this.cancelForm();
    },
    
    cancelForm() {
      this.showForm = false;
      this.editingGenre = null;
    }
  }
};
</script>

<style scoped>
.no-poster {
  width: 45px;
  height: 60px;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  color: #666;
  border-radius: 4px;
}
</style>