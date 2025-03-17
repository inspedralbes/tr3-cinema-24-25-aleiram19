// Importaciones necesarias para Vue (Pinia)
import { defineStore } from 'pinia';
// Ya no se importa el servicio API

// Definición del store para géneros
export const useGenresStore = defineStore('genres', {
  // Estado del store
  state: () => ({
    genres: [],          // Lista de géneros
    loading: false,      // Indicador de carga
    error: null          // Mensaje de error si ocurre alguno
  }),

  // Getters
  getters: {
    // Obtener todos los géneros
    getAllGenres() {
      return this.genres;
    },
    
    // Obtener género por ID
    getGenreById: (state) => (id) => {
      return state.genres.find(genre => genre.id === id);
    }
  },

  // Acciones
  actions: {
    // Obtener todos los géneros
    async fetchGenres() {
      try {
        this.loading = true;
        this.error = null;
        
        // Usar fetch directamente con la URL completa
        const response = await fetch('http://localhost:8000/api/genre');
        if (!response.ok) {
          throw new Error(`Error: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        this.genres = data;
        return data;
      } catch (error) {
        console.error('Error en fetchGenres:', error);
        this.error = error.message;
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    // Obtener películas por género
    async fetchMoviesByGenre(genreId) {
      try {
        this.loading = true;
        this.error = null;
        
        // Usar fetch directamente con la URL completa
        const response = await fetch(`http://localhost:8000/api/genre/${genreId}/movies`);
        if (!response.ok) {
          throw new Error(`Error: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        return data; // No se actualiza el estado local, se devuelve la respuesta
      } catch (error) {
        console.error('Error en fetchMoviesByGenre:', error);
        this.error = error.message;
        return [];
      } finally {
        this.loading = false;
      }
    }
  }
});
