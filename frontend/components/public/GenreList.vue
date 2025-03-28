<template>
  <div class="genre-list">
    <div v-if="loading" class="text-center py-3">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
    
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    
    <div v-else-if="!genres.length" class="alert alert-light">
      No hay géneros disponibles.
    </div>
    
    <div v-else class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
      <div v-for="genre in genres" :key="genre.id" class="col">
        <div 
          class="genre-card"
          :class="{ 'active': selectedGenre === genre.id }"
          @click="selectGenre(genre.id)"
        >
          <div class="genre-icon">
            <i class="bi" :class="getGenreIcon(genre.name)"></i>
          </div>
          <div class="genre-name">{{ genre.name }}</div>
        </div>
      </div>
      
      <div class="col">
        <div 
          class="genre-card"
          :class="{ 'active': selectedGenre === null }"
          @click="selectGenre(null)"
        >
          <div class="genre-icon">
            <i class="bi bi-grid"></i>
          </div>
          <div class="genre-name">Todos</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useGenresStore } from '~/stores/genres';

export default {
  name: 'GenreList',
  
  props: {
    selected: {
      type: Number,
      default: null
    }
  },
  
  emits: ['select'],
  
  data() {
    return {
      selectedGenre: this.selected
    };
  },
  
  computed: {
    genresStore() {
      return useGenresStore();
    },
    
    genres() {
      return this.genresStore.genres;
    },
    
    loading() {
      return this.genresStore.loading;
    },
    
    error() {
      return this.genresStore.error;
    }
  },
  
  watch: {
    selected(newVal) {
      this.selectedGenre = newVal;
    }
  },
  
  async created() {
    await this.loadGenres();
  },
  
  methods: {
    async loadGenres() {
      try {
        if (this.genres.length === 0) {
          await this.genresStore.fetchGenres();
        }
      } catch (error) {
        console.error('Error al cargar géneros:', error);
      }
    },
    
    selectGenre(genreId) {
      this.selectedGenre = genreId;
      this.$emit('select', genreId);
    },
    
    getGenreIcon(genreName) {
      // Mapear géneros comunes a iconos de Bootstrap
      const genreIcons = {
        'Acción': 'bi-lightning',
        'Aventura': 'bi-compass',
        'Comedia': 'bi-emoji-laughing',
        'Drama': 'bi-emoji-frown',
        'Terror': 'bi-ghost',
        'Ciencia Ficción': 'bi-rocket',
        'Fantasía': 'bi-magic',
        'Romance': 'bi-heart',
        'Animación': 'bi-stars',
        'Documental': 'bi-camera-reels',
        'Thriller': 'bi-exclamation-diamond',
        'Musical': 'bi-music-note',
        'Infantil': 'bi-emoji-smile',
        'Bélica': 'bi-shield',
        'Western': 'bi-award',
        'Historia': 'bi-book',
        'Misterio': 'bi-question-circle'
      };
      
      // Normalizar el nombre del género eliminando acentos y convirtiendo a minúsculas
      const normalizedName = genreName
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase();
      
      // Buscar coincidencias parciales
      for (const [key, icon] of Object.entries(genreIcons)) {
        if (normalizedName.includes(key.toLowerCase())) {
          return icon;
        }
      }
      
      // Icono por defecto
      return 'bi-tag';
    }
  }
};
</script>

<style scoped>
.genre-list {
  margin-bottom: 2rem;
}

.genre-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  height: 100%;
  min-height: 120px;
  text-align: center;
}

.genre-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  background-color: #e9ecef;
}

.genre-card.active {
  background-color: var(--bs-primary);
  color: white;
}

.genre-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.genre-name {
  font-weight: 500;
}
</style>