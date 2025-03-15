<template>
  <div class="movie-form">
    <h2>{{ isEditing ? 'Editar Película' : 'Nueva Película' }}</h2>
    
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    
    <form @submit.prevent="saveMovie" enctype="multipart/form-data">
      <div class="form-group mb-3">
        <label for="title">Título</label>
        <input 
          id="title" 
          v-model="form.title" 
          type="text" 
          class="form-control"
          required
        />
      </div>
      
      <div class="form-group mb-3">
        <label for="description">Descripción</label>
        <textarea 
          id="description" 
          v-model="form.description" 
          class="form-control"
          rows="3"
          required
        ></textarea>
      </div>
      
      <div class="form-group mb-3">
        <label for="duration">Duración (minutos)</label>
        <input 
          id="duration" 
          v-model.number="form.duration" 
          type="number" 
          class="form-control"
          min="1"
          required
        />
      </div>
      
      <div class="form-group mb-3">
        <label for="genre_id">Género</label>
        <select 
          id="genre_id" 
          v-model="form.genre_id" 
          class="form-control"
          required
        >
          <option value="" disabled>Selecciona un género</option>
          <option 
            v-for="genre in genres" 
            :key="genre.id" 
            :value="genre.id"
          >
            {{ genre.name }}
          </option>
        </select>
      </div>
      
      <div class="form-group mb-3">
        <label for="release_date">Fecha de Estreno</label>
        <input 
          id="release_date" 
          v-model="form.release_date" 
          type="date" 
          class="form-control"
          required
        />
      </div>
      
      <div class="form-group mb-3">
        <label for="poster">Poster</label>
        <input 
          id="poster" 
          type="file" 
          class="form-control"
          @change="handleImageUpload"
          accept="image/*"
        />
        <small v-if="isEditing && form.poster" class="form-text text-muted">
          Dejar en blanco para mantener la imagen actual
        </small>
        
        <div v-if="previewImage || (isEditing && movie.poster)" class="mt-2">
          <img 
            :src="previewImage || (isEditing ? `/storage/${movie.poster}` : '')" 
            alt="Vista previa del poster" 
            class="img-thumbnail" 
            style="max-height: 200px;"
          />
        </div>
      </div>
      
      <div class="form-group mb-3">
        <label for="trailer_url">URL del Trailer</label>
        <input 
          id="trailer_url" 
          v-model="form.trailer_url" 
          type="url" 
          class="form-control"
          placeholder="https://www.youtube.com/watch?v=..."
        />
      </div>
      
      <div class="d-flex justify-content-between">
        <button 
          type="submit" 
          class="btn btn-primary"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          {{ isEditing ? 'Actualizar' : 'Guardar' }}
        </button>
        
        <button 
          type="button" 
          class="btn btn-secondary"
          @click="$emit('cancel')"
        >
          Cancelar
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { useGenresStore } from '~/stores/genres';
import apiService from '~/services/api';

export default {
  name: 'MovieForm',
  
  props: {
    movie: {
      type: Object,
      default: null
    }
  },
  
  emits: ['saved', 'cancel'],
  
  data() {
    return {
      form: {
        title: '',
        description: '',
        duration: 90,
        genre_id: '',
        release_date: '',
        poster: null,
        trailer_url: ''
      },
      previewImage: null,
      loading: false,
      error: null
    };
  },
  
  computed: {
    isEditing() {
      return !!this.movie;
    },
    
    genres() {
      return useGenresStore().genres;
    }
  },
  
  async created() {
    // Cargar géneros si no están cargados ya
    const genresStore = useGenresStore();
    if (genresStore.genres.length === 0) {
      await genresStore.fetchGenres();
    }
    
    // Si estamos editando, llenar el formulario con los datos de la película
    if (this.isEditing) {
      this.form.title = this.movie.title;
      this.form.description = this.movie.description;
      this.form.duration = this.movie.duration;
      this.form.genre_id = this.movie.movie_genre_id || this.movie.genre_id;
      this.form.release_date = this.formatDate(this.movie.release_date);
      this.form.trailer_url = this.movie.trailer_url || '';
    }
  },
  
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toISOString().split('T')[0];
    },
    
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.form.poster = file;
        
        // Crear vista previa
        const reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        this.form.poster = null;
        this.previewImage = null;
      }
    },
    
    async saveMovie() {
      this.loading = true;
      this.error = null;
      
      try {
        // Crear FormData para manejar la subida de archivos
        const formData = new FormData();
        formData.append('title', this.form.title);
        formData.append('description', this.form.description);
        formData.append('duration', this.form.duration);
        formData.append('genre_id', this.form.genre_id);
        formData.append('release_date', this.form.release_date);
        
        if (this.form.trailer_url) {
          formData.append('trailer_url', this.form.trailer_url);
        }
        
        if (this.form.poster instanceof File) {
          formData.append('poster', this.form.poster);
        }
        
        let response;
        
        if (this.isEditing) {
          // Actualizar película existente
          response = await apiService.movies.update(this.movie.id, formData);
        } else {
          // Crear nueva película
          response = await apiService.movies.create(formData);
        }
        
        this.$emit('saved', response.movie);
      } catch (error) {
        console.error('Error al guardar la película:', error);
        this.error = error.message || 'Ha ocurrido un error al guardar la película';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.movie-form {
  max-width: 800px;
  margin: 0 auto;
}
</style>