import { defineStore } from 'pinia';
import { useNuxtApp } from 'nuxt/app';

// Definición del store para películas
export const useMoviesStore = defineStore('movies', {
  // Estado del store
  state: () => ({
    movies: [],          // Lista de películas
    loading: false,      // Indicador de carga
    error: null          // Mensaje de error si ocurre alguno
  }),

  // Getters
  getters: {
    // Obtener todas las películas
    getAllMovies() {
      return this.movies;
    },
    
    // Obtener películas actualmente en cartelera
    getCurrentMovies() {
      return this.movies; // Devolvemos todas las películas sin filtrar
    },
    
    // Obtener película por ID
    getMovieById: (state) => (id) => {
      return state.movies.find(movie => movie.id === id);
    }
  },

  // Acciones
  actions: {
    // Obtener todas las películas
    async fetchMovies() {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch('http://localhost:8000/api/movie', {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        this.movies = data;
        
        // Mostrar un mensaje de éxito usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.success('Películas cargadas correctamente');
        }
        
        return data;
      } catch (error) {
        // Manejar errores y mostrar mensaje al usuario
        console.error('Error en fetchMovies:', error);
        this.error = error.message;
        
        // Mostrar un mensaje de error usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.error(`Error al cargar películas: ${error.message}`);
        }
        
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    // Obtener películas en cartelera
    async fetchCurrentMovies() {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch('http://localhost:8000/api/movie/current', {
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        this.movies = data;
        
        // Mostrar un mensaje de éxito usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.success('Cartelera cargada correctamente');
        }
        
        return data;
      } catch (error) {
        // Manejar errores y mostrar mensaje al usuario
        console.error('Error en fetchCurrentMovies:', error);
        this.error = error.message;
        
        // Mostrar un mensaje de error usando Toast
        const nuxtApp = useNuxtApp();
        if (nuxtApp.$toast) {
          nuxtApp.$toast.error(`Error al cargar cartelera: ${error.message}`);
        }
        
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    // Obtener detalles de una película
    async fetchMovieDetails(id) {
      try {
        this.loading = true;
        this.error = null;
        
        // Hacer la petición con el header Accept: application/json
        const response = await fetch(`http://localhost:8000/api/movie/${id}`, {
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error(`Error en la petición: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Actualizar la película en el estado local
        const index = this.movies.findIndex(movie => movie.id === id);
        
        if (index !== -1) {
          this.movies[index] = data;
        } else {
          this.movies.push(data);
        }
        
        return data;
      } catch (error) {
        console.error('Error en fetchMovieDetails:', error);
        this.error = error.message;
        return null;
      } finally {
        this.loading = false;
      }
    }
  }
});
