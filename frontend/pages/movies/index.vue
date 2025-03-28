<template>
  <div class="movies-page">
    <div class="container py-5">
      <div class="row mb-4">
        <div class="col-12">
          <h1>Todas las Películas</h1>
          <p class="lead">Descubre todas nuestras películas disponibles</p>
        </div>
      </div>
      
      <GenreList 
        :selected="selectedGenre" 
        @select="handleGenreSelect" 
      />
      
      <MovieGrid 
        :initialGenre="selectedGenre" 
      />
    </div>
  </div>
</template>

<script>
import MovieGrid from '~/components/public/MovieGrid.vue';
import GenreList from '~/components/public/GenreList.vue';

export default {
  name: 'MoviesPage',
  
  components: {
    MovieGrid,
    GenreList
  },
  
  data() {
    return {
      selectedGenre: null
    };
  },
  
  head() {
    return {
      title: 'Películas - Cinema'
    };
  },
  
  mounted() {
    // Comprobar si hay un género en la URL
    const genreParam = this.$route.query.genre;
    if (genreParam) {
      this.selectedGenre = parseInt(genreParam);
    }
  },
  
  methods: {
    handleGenreSelect(genreId) {
      this.selectedGenre = genreId;
      
      // Actualizar la URL para reflejar el filtro de género
      if (genreId) {
        this.$router.push({ query: { genre: genreId } });
      } else {
        this.$router.push({ query: {} });
      }
    }
  }
};
</script>

<style scoped>
.movies-page {
  min-height: 80vh;
}
</style>