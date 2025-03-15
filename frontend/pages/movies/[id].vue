<template>
  <div class="movie-detail-page">
    <div class="container py-5">
      <div class="mb-4">
        <NuxtLink to="/movies" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left me-2"></i> Volver a películas
        </NuxtLink>
      </div>
      
      <MovieDetail :movieId="movieId" />
    </div>
  </div>
</template>

<script>
import MovieDetail from '~/components/public/MovieDetail.vue';

export default {
  name: 'MovieDetailPage',
  
  components: {
    MovieDetail
  },
  
  computed: {
    movieId() {
      return this.$route.params.id;
    }
  },
  
  head() {
    // Para un título dinámico, podríamos usar el título de la película
    // Para simplicidad, usamos un título genérico por ahora
    return {
      title: 'Detalle de Película - Cinema'
    };
  }
};
</script>

<style scoped>
.movie-detail-page {
  min-height: 80vh;
}
</style>