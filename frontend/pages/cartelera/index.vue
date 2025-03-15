<template>
  <div class="min-h-screen flex flex-col bg-[#051D40]">
    <LandingPageNavBar />
    <!-- Ajustado el padding top para el navbar fijo -->
    <div class="pt-24">
      <PeliculasGridCartelera 
        :peliculas="peliculasFiltradas" 
        :generos="generos" 
        :loading="loading" 
        :error="error"
        :filtroActivo="filtroGeneroId"
        :trailerActivo="trailerActivo"
        :trailerUrl="trailerUrl"
        @filtrar="filtrarPorGenero"
        @verTrailer="verTrailer"
        @cerrarTrailer="cerrarTrailer"
        @recargar="cargarDatos"
        @imageError="handleImageError"
      />
    </div>
    <LandingPageFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useMoviesStore } from "@/stores/movies";
import { useGenresStore } from '@/stores/genres';

definePageMeta({
  title: 'Cartelera - CineXeperience',
  description: 'Descubre las películas en cartelera en CineXeperience'
});

// Importamos el componente de películas
import PeliculasGridCartelera from '@/components/cartelera/PeliculasGrid.vue';

// Stores
const moviesStore = useMoviesStore();
const genresStore = useGenresStore();

// Estado local
const filtroGeneroId = ref(null);
const trailerActivo = ref(false);
const trailerUrl = ref('');

// Estado computado
const peliculas = computed(() => moviesStore.movies);
const generos = computed(() => genresStore.genres);

const loading = computed(() => moviesStore.loading || genresStore.loading);
const error = computed(() => moviesStore.error || genresStore.error);

const peliculasFiltradas = computed(() => {
  if (filtroGeneroId.value === null) {
    return peliculas.value;
  }
  return peliculas.value.filter(pelicula => pelicula.movie_genre_id === filtroGeneroId.value);
});

// Métodos
const filtrarPorGenero = (generoId) => {
  filtroGeneroId.value = generoId;
};

const verTrailer = (url) => {
  if (!url) return;
  trailerUrl.value = url;
  trailerActivo.value = true;
  document.body.style.overflow = 'hidden';
};

const cerrarTrailer = () => {
  trailerActivo.value = false;
  trailerUrl.value = '';
  document.body.style.overflow = 'auto';
};

const handleImageError = (e) => {
  if (e.target && e.target.nextElementSibling) {
    e.target.style.display = 'none';
    e.target.nextElementSibling.style.display = 'flex';
  }
};

// Cargar datos
const cargarDatos = async () => {
  try {
    await Promise.all([
      moviesStore.fetchCurrentMovies(),
      genresStore.fetchGenres()
    ]);
  } catch (error) {
    console.error('Error al cargar datos:', error);
  }
};

// Al montar la página
onMounted(async () => {
  await cargarDatos();
});
</script>