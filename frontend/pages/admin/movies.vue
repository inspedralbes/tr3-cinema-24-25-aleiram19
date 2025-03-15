<template>
  <div class="admin-movies-page">
    <MovieList />
  </div>
</template>

<script>
import MovieList from '~/components/admin/MovieList.vue';

export default {
  name: 'AdminMoviesPage',
  
  components: {
    MovieList
  },
  
  layout: 'admin', // Asumiendo que tienes un layout para admin
  
  middleware: ['auth', 'admin'], // Middleware para verificar que el usuario esté autenticado y sea admin
  
  head() {
    return {
      title: 'Administración de Películas - Cinema'
    };
  }
};
</script>

<style scoped>
.admin-movies-page {
  padding: 20px;
}
</style>