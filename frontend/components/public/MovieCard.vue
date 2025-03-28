<template>
  <div class="movie-card card h-100">
    <div class="card-img-top position-relative">
      <img 
        v-if="movie.poster" 
        :src="`/storage/${movie.poster}`" 
        :alt="movie.title" 
        class="movie-poster"
      />
      <div v-else class="no-poster d-flex align-items-center justify-content-center">
        <i class="bi bi-film fs-1 text-muted"></i>
      </div>
      
      <div v-if="showGenre && movie.genre" class="genre-badge">
        {{ movie.genre.name }}
      </div>
    </div>
    
    <div class="card-body">
      <h5 class="card-title">{{ movie.title }}</h5>
      <p class="card-text movie-info">
        <span class="duration">
          <i class="bi bi-clock me-1"></i> {{ movie.duration }} min
        </span>
        <span class="release-date ms-3">
          <i class="bi bi-calendar-event me-1"></i> {{ formatDate(movie.release_date) }}
        </span>
      </p>
      <p class="card-text description">{{ truncateDescription(movie.description) }}</p>
    </div>
    
    <div class="card-footer d-flex justify-content-between">
      <NuxtLink :to="`/movies/${movie.id}`" class="btn btn-outline-primary">
        <i class="bi bi-info-circle me-1"></i> Detalles
      </NuxtLink>
      
      <NuxtLink :to="`/movies/${movie.id}`" class="btn btn-primary">
        <i class="bi bi-ticket-perforated me-1"></i> Ver Funciones
      </NuxtLink>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MovieCard',
  
  props: {
    movie: {
      type: Object,
      required: true
    },
    showGenre: {
      type: Boolean,
      default: true
    }
  },
  
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    
    truncateDescription(text) {
      if (!text) return '';
      return text.length > 120 ? text.slice(0, 120) + '...' : text;
    }
  }
};
</script>

<style scoped>
.movie-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.movie-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.movie-poster, .no-poster {
  height: 300px;
  width: 100%;
  object-fit: cover;
}

.no-poster {
  background-color: #f0f0f0;
}

.genre-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
}

.movie-info {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.description {
  font-size: 0.9rem;
  color: #333;
  line-height: 1.4;
}

.card-title {
  font-weight: bold;
  margin-bottom: 0.75rem;
}

.card-footer {
  background-color: white;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  padding: 1rem;
}
</style>