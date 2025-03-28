<template>
  <div class="genre-form">
    <h2>{{ isEditing ? 'Editar Género' : 'Nuevo Género' }}</h2>
    
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    
    <form @submit.prevent="saveGenre">
      <div class="form-group mb-3">
        <label for="name">Nombre</label>
        <input 
          id="name" 
          v-model="form.name" 
          type="text" 
          class="form-control"
          required
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
import apiService from '~/services/api';

export default {
  name: 'GenreForm',
  
  props: {
    genre: {
      type: Object,
      default: null
    }
  },
  
  emits: ['saved', 'cancel'],
  
  data() {
    return {
      form: {
        name: ''
      },
      loading: false,
      error: null
    };
  },
  
  computed: {
    isEditing() {
      return !!this.genre;
    }
  },
  
  created() {
    // Si estamos editando, llenar el formulario con los datos del género
    if (this.isEditing) {
      this.form.name = this.genre.name;
    }
  },
  
  methods: {
    async saveGenre() {
      this.loading = true;
      this.error = null;
      
      try {
        let response;
        
        if (this.isEditing) {
          // Actualizar género existente
          response = await apiService.genres.update(this.genre.id, this.form);
        } else {
          // Crear nuevo género
          response = await apiService.genres.create(this.form);
        }
        
        this.$emit('saved', response.genre);
      } catch (error) {
        console.error('Error al guardar el género:', error);
        this.error = error.message || 'Ha ocurrido un error al guardar el género';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.genre-form {
  max-width: 500px;
  margin: 0 auto;
}
</style>