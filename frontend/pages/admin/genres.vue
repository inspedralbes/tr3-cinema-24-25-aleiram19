<template>
  <div class="admin-genres-page">
    <GenreList />
  </div>
</template>

<script>
import GenreList from '~/components/admin/GenreList.vue';

export default {
  name: 'AdminGenresPage',
  
  components: {
    GenreList
  },
  
  layout: 'admin', // Asumiendo que tienes un layout para admin
  
  middleware: ['auth', 'admin'], // Middleware para verificar que el usuario esté autenticado y sea admin
  
  head() {
    return {
      title: 'Administración de Géneros - Cinema'
    };
  }
};
</script>

<style scoped>
.admin-genres-page {
  padding: 20px;
}
</style>